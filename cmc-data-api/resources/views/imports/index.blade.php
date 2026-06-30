@extends('layouts.app')

@section('title', 'Import')
@section('breadcrumb')<span class="current">Import</span>@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Import de données</h1>
            <p class="page-subtitle">Importez des formateurs ou des stagiaires depuis un fichier Excel ou CSV.</p>
        </div>
    </div>

    <div class="card" style="margin-bottom:24px">
        <div class="card-header"><span class="card-title">Nouveau fichier</span></div>
        <div class="card-body">
            <form id="import-form">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="import-type">Type d'import<span class="req">*</span></label>
                        <select id="import-type" name="type" class="form-control" required>
                            <option value="">Sélectionnez un type…</option>
                            <option value="formateurs">Formateurs</option>
                            <option value="stagiaires">Stagiaires</option>
                        </select>
                        <div class="form-error" id="error-type" hidden></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="import-mode">Mode</label>
                        <select id="import-mode" name="mode" class="form-control">
                            <option value="upsert" selected>Upsert — mettre à jour si existant</option>
                            <option value="insert_only">Insertion seule — ignorer les doublons</option>
                        </select>
                        <div class="form-error" id="error-mode" hidden></div>
                    </div>

                    <div class="form-group full">
                        <label class="form-label" for="import-file">Fichier<span class="req">*</span></label>

                        <label for="import-file" class="import-dropzone" id="import-dropzone">
                            <svg class="import-dropzone-icon" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M12 12v9m0-9l-3 3m3-3l3 3"/>
                            </svg>
                            <div class="import-dropzone-text">
                                <strong id="import-filename">Cliquez pour choisir un fichier</strong>
                                <span>.xlsx, .xls ou .csv — 20 Mo max</span>
                            </div>
                            <input type="file" id="import-file" name="file" accept=".xlsx,.xls,.csv" class="import-dropzone-input" required>
                        </label>
                        <div class="form-error" id="error-file" hidden></div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="import-submit">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 12m4-4v12"/></svg>
                        Importer
                    </button>
                    <button type="reset" class="btn btn-outline" id="import-reset">Réinitialiser</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Inline progress / network error --}}
    <div id="import-alert-zone"></div>

    {{-- Result summary, populated by JS after a successful (or partially successful) call --}}
    <div id="import-results" hidden>
        <div class="stats-grid" id="import-stats"></div>

        <div class="card" id="import-row-errors-card" hidden>
            <div class="card-header">
                <span class="card-title">Lignes en erreur</span>
                <span class="badge badge-red" id="import-row-errors-count">0</span>
            </div>
            <div class="table-wrap" style="border:none;border-radius:0">
                <table>
                    <thead>
                    <tr>
                        <th>Ligne</th>
                        <th>Message</th>
                    </tr>
                    </thead>
                    <tbody id="import-row-errors-body"></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* ─── Import dropzone ─── */
        .import-dropzone {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 22px 24px;
            border: 1.5px dashed var(--slate-200);
            border-radius: 10px;
            background: var(--slate-50);
            cursor: pointer;
            transition: border-color .15s, background .15s;
        }

        .import-dropzone:hover,
        .import-dropzone.is-dragover {
            border-color: var(--indigo);
            background: var(--indigo-soft);
        }

        .import-dropzone.has-file {
            border-style: solid;
            border-color: var(--indigo);
        }

        .import-dropzone-icon {
            color: var(--indigo);
            flex-shrink: 0;
        }

        .import-dropzone-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
            font-size: 13px;
            color: var(--slate-500);
        }

        .import-dropzone-text strong {
            font-size: 13.5px;
            color: var(--slate-700);
            font-weight: 600;
        }

        .import-dropzone-input {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            opacity: 0;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form        = document.getElementById('import-form');
            const typeSelect   = document.getElementById('import-type');
            const modeSelect   = document.getElementById('import-mode');
            const fileInput    = document.getElementById('import-file');
            const dropzone      = document.getElementById('import-dropzone');
            const filenameLabel = document.getElementById('import-filename');
            const submitBtn      = document.getElementById('import-submit');
            const alertZone       = document.getElementById('import-alert-zone');
            const resultsWrap      = document.getElementById('import-results');
            const statsGrid          = document.getElementById('import-stats');
            const rowErrorsCard       = document.getElementById('import-row-errors-card');
            const rowErrorsCount      = document.getElementById('import-row-errors-count');
            const rowErrorsBody       = document.getElementById('import-row-errors-body');

            const fieldErrors = {
                type: document.getElementById('error-type'),
                mode: document.getElementById('error-mode'),
                file: document.getElementById('error-file'),
            };

            function clearFieldErrors() {
                Object.values(fieldErrors).forEach(function (el) {
                    el.hidden = true;
                    el.textContent = '';
                });
            }

            function showFieldErrors(errors) {
                Object.keys(errors).forEach(function (key) {
                    if (fieldErrors[key]) {
                        fieldErrors[key].textContent = errors[key][0];
                        fieldErrors[key].hidden = false;
                    }
                });
            }

            function showAlert(type, message) {
                const icon = type === 'success'
                    ? '<svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>'
                    : '<svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
                alertZone.innerHTML = '<div class="alert alert-' + type + '">' + icon + message + '</div>';
            }

            function clearAlert() {
                alertZone.innerHTML = '';
            }

            function statCard(label, value, color) {
                return '<div class="stat-card" style="--stat-color:var(--' + color + ')">'
                    +   '<div class="stat-label">' + label + '</div>'
                    +   '<div class="stat-value">' + value + '</div>'
                    + '</div>';
            }

            fileInput.addEventListener('change', function () {
                const file = fileInput.files[0];
                if (file) {
                    filenameLabel.textContent = file.name;
                    dropzone.classList.add('has-file');
                } else {
                    filenameLabel.textContent = 'Cliquez pour choisir un fichier';
                    dropzone.classList.remove('has-file');
                }
            });

            ['dragover', 'dragenter'].forEach(function (evt) {
                dropzone.addEventListener(evt, function (e) {
                    e.preventDefault();
                    dropzone.classList.add('is-dragover');
                });
            });

            ['dragleave', 'drop'].forEach(function (evt) {
                dropzone.addEventListener(evt, function (e) {
                    e.preventDefault();
                    dropzone.classList.remove('is-dragover');
                });
            });

            dropzone.addEventListener('drop', function (e) {
                const file = e.dataTransfer.files[0];
                if (file) {
                    fileInput.files = e.dataTransfer.files;
                    fileInput.dispatchEvent(new Event('change'));
                }
            });

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                clearFieldErrors();
                clearAlert();
                resultsWrap.hidden = true;

                const formData = new FormData();
                formData.append('type', typeSelect.value);
                formData.append('mode', modeSelect.value);
                if (fileInput.files[0]) {
                    formData.append('file', fileInput.files[0]);
                }

                submitBtn.disabled = true;
                submitBtn.textContent = 'Import en cours…';

                fetch('/api/v1/import', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: formData,
                })
                    .then(function (res) {
                        return res.json().then(function (data) { return { status: res.status, data: data }; });
                    })
                    .then(function (_ref) {
                        const status = _ref.status, data = _ref.data;

                        if (status === 422) {
                            showAlert('error', 'Veuillez corriger les champs ci-dessous.');
                            if (data.errors) showFieldErrors(data.errors);
                            return;
                        }

                        if (status >= 400) {
                            showAlert('error', data.message || "Une erreur est survenue pendant l'import.");
                            return;
                        }

                        renderResults(data, status);
                    })
                    .catch(function () {
                        showAlert('error', "Impossible de contacter le serveur. Vérifiez votre connexion et réessayez.");
                    })
                    .finally(function () {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 12m4-4v12"/></svg> Importer';
                    });
            });

            function renderResults(data, status) {
                const s = data.summary || { inserted: 0, updated: 0, skipped: 0, errors: 0 };

                if (status === 207 || s.errors > 0) {
                    showAlert('warning', 'Import terminé avec ' + s.errors + ' ligne(s) en erreur.');
                } else {
                    showAlert('success', 'Import terminé avec succès.');
                }

                statsGrid.innerHTML =
                    statCard('Insérés', s.inserted, 'green')
                    + statCard('Mis à jour', s.updated, 'indigo')
                    + statCard('Ignorés', s.skipped, 'amber')
                    + statCard('Erreurs', s.errors, 'red');

                rowErrorsBody.innerHTML = '';
                const rowErrors = data.row_errors || [];
                if (rowErrors.length > 0) {
                    rowErrorsCount.textContent = rowErrors.length;
                    rowErrors.forEach(function (err) {
                        const row = err.row ?? err.line ?? '—';
                        const msg = err.message ?? (typeof err === 'string' ? err : JSON.stringify(err));
                        rowErrorsBody.insertAdjacentHTML('beforeend',
                            '<tr><td class="font-mono" style="font-size:12px">' + row + '</td><td>' + msg + '</td></tr>'
                        );
                    });
                    rowErrorsCard.hidden = false;
                } else {
                    rowErrorsCard.hidden = true;
                }

                resultsWrap.hidden = false;
                resultsWrap.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    </script>
@endpush
