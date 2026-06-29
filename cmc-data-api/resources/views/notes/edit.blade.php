@extends('layouts.app')

@section('title', 'Éditer la note #' . $note->id)
@section('breadcrumb')
    <a href="{{ route('web.notes.index') }}">Notes</a>
    <span class="topbar-sep">/</span>
    <a href="{{ route('web.notes.show', $note) }}">#{{ $note->id }}</a>
    <span class="topbar-sep">/</span>
    <span class="current">Éditer</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Éditer la note</h1>
            <p class="page-subtitle">
                {{ $note->stagiaire?->nom }} {{ $note->stagiaire?->prenom }}
                @if($note->seance?->affectation?->module) · {{ $note->seance->affectation->module->libelle }} @endif
            </p>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span class="card-title">Modifier</span></div>
        <div class="card-body">
            <form method="POST" action="{{ route('web.notes.update', $note) }}">
                @csrf @method('PATCH')
                <div class="form-grid">

                    <div class="form-group">
                        <label class="form-label" for="select-pole">Pôle</label>
                        <select id="select-pole" class="form-control">
                            <option value="">— Choisir un pôle —</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="select-filiere">Filière</label>
                        <select id="select-filiere" class="form-control" disabled>
                            <option value="">— Choisir une filière —</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="select-annee">Année de formation</label>
                        <select id="select-annee" class="form-control" disabled>
                            <option value="">— Choisir une année —</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="select-groupe">Groupe <span class="req">*</span></label>
                        <select id="select-groupe" class="form-control" disabled>
                            <option value="">— Choisir un groupe —</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="edit-note-stagiaire">Stagiaire <span class="req">*</span></label>
                        <div style="display: flex; gap: 8px;">
                            <select id="edit-note-stagiaire" name="stagiaire_cef" class="form-control" required style="flex: 1;">
                                <option value="">— Choisir un stagiaire —</option>
                            </select>
                            <input type="text" id="search-stagiaire" class="form-control" placeholder="Rechercher..." style="width: 130px;" disabled>
                        </div>
                        @error('stagiaire_cef')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="edit-note-seance">Séance <span class="req">*</span></label>
                        <select id="edit-note-seance" name="seance_id" class="form-control" required>
                            <option value="">— Choisir une séance —</option>
                        </select>
                        @error('seance_id')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="edit-note-type">Type</label>
                        <select id="edit-note-type" name="type" class="form-control">
                            <option value="">— Non défini —</option>
                            <option value="cc"   @selected(old('type', $note->type) === 'cc')>CC — Contrôle continu</option>
                            <option value="efm"  @selected(old('type', $note->type) === 'efm')>EFM — Épreuve de fin de module</option>
                            <option value="tp"   @selected(old('type', $note->type) === 'tp')>TP — Travaux pratiques</option>
                            <option value="th"   @selected(old('type', $note->type) === 'th')>TH — Travaux d'heures</option>
                            <option value="syn"  @selected(old('type', $note->type) === 'syn')>SYN — Synchrone</option>
                            <option value="exam" @selected(old('type', $note->type) === 'exam')>Examen</option>
                        </select>
                        @error('type')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="edit-note-valeur">Note /20</label>
                        <input id="edit-note-valeur" type="number" name="valeur" class="form-control"
                               step="0.01" min="0" max="20"
                               value="{{ old('valeur', $note->valeur) }}">
                        @error('valeur')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="edit-note-decision">Décision</label>
                        <select id="edit-note-decision" name="decision" class="form-control">
                            <option value="">— Non définie —</option>
                            <option value="Admis"      @selected(old('decision', $note->decision) === 'Admis')>Admis</option>
                            <option value="Redoublant" @selected(old('decision', $note->decision) === 'Redoublant')>Redoublant</option>
                            <option value="Abandon"    @selected(old('decision', $note->decision) === 'Abandon')>Abandon</option>
                            <option value="Rattrapage" @selected(old('decision', $note->decision) === 'Rattrapage')>Rattrapage</option>
                        </select>
                        @error('decision')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('web.notes.show', $note) }}" class="btn btn-outline">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const poleSelect = document.getElementById('select-pole');
    const filiereSelect = document.getElementById('select-filiere');
    const anneeSelect = document.getElementById('select-annee');
    const groupeSelect = document.getElementById('select-groupe');
    const stagiaireSelect = document.getElementById('edit-note-stagiaire');
    const seanceSelect = document.getElementById('edit-note-seance');
    const typeSelect = document.getElementById('edit-note-type');
    const searchStagiaire = document.getElementById('search-stagiaire');

    const initialStagiaireCef = '{{ $note->stagiaire_cef }}';
    const initialSeanceId = '{{ $note->seance_id }}';

    let allStagiaires = [];
    let selectedPole = null;
    let selectedFiliere = null;
    let selectedAnnee = null;
    let selectedGroupe = null;
    let selectedStagiaire = initialStagiaireCef;
    let selectedSeance = initialSeanceId;

    // First resolve the hierarchy of the current stagiaire
    fetch(`/api/v1/hierarchy/resolve/stagiaire/${initialStagiaireCef}`)
        .then(res => res.json())
        .then(hierarchy => {
            selectedPole = hierarchy.pole_id;
            selectedFiliere = hierarchy.filiere_code;
            selectedAnnee = hierarchy.annee_id;
            selectedGroupe = hierarchy.groupe_id;

            // Load poles
            fetch('/api/v1/hierarchy/poles')
                .then(res => res.json())
                .then(poles => {
                    poles.forEach(pole => {
                        const opt = document.createElement('option');
                        opt.value = pole.id;
                        opt.textContent = pole.libelle;
                        if (pole.id == selectedPole) opt.selected = true;
                        poleSelect.appendChild(opt);
                    });
                    if (selectedPole) poleSelect.dispatchEvent(new Event('change'));
                });
        });

    // 2. Pole -> Filieres
    poleSelect.addEventListener('change', function() {
        filiereSelect.innerHTML = '<option value="">— Choisir une filière —</option>';
        filiereSelect.disabled = !this.value;
        anneeSelect.innerHTML = '<option value="">— Choisir une année —</option>';
        anneeSelect.disabled = true;
        groupeSelect.innerHTML = '<option value="">— Choisir un groupe —</option>';
        groupeSelect.disabled = true;
        resetStagiairesAndSeances();

        if (!this.value) return;

        fetch(`/api/v1/hierarchy/filieres?pole_id=${this.value}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(filiere => {
                    const opt = document.createElement('option');
                    opt.value = filiere.code_filiere;
                    opt.textContent = filiere.libelle;
                    if (filiere.code_filiere === selectedFiliere) opt.selected = true;
                    filiereSelect.appendChild(opt);
                });
                if (selectedFiliere) filiereSelect.dispatchEvent(new Event('change'));
            });
    });

    // 3. Filiere -> Annees
    filiereSelect.addEventListener('change', function() {
        anneeSelect.innerHTML = '<option value="">— Choisir une année —</option>';
        anneeSelect.disabled = !this.value;
        groupeSelect.innerHTML = '<option value="">— Choisir un groupe —</option>';
        groupeSelect.disabled = true;
        resetStagiairesAndSeances();

        if (!this.value) return;

        fetch(`/api/v1/hierarchy/annees?filiere_code=${this.value}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(annee => {
                    const opt = document.createElement('option');
                    opt.value = annee.id;
                    opt.textContent = annee.label;
                    if (annee.id == selectedAnnee) opt.selected = true;
                    anneeSelect.appendChild(opt);
                });
                if (selectedAnnee) anneeSelect.dispatchEvent(new Event('change'));
            });
    });

    // 4. Annee -> Groupes
    anneeSelect.addEventListener('change', function() {
        groupeSelect.innerHTML = '<option value="">— Choisir un groupe —</option>';
        groupeSelect.disabled = !this.value;
        resetStagiairesAndSeances();

        if (!this.value) return;

        fetch(`/api/v1/hierarchy/groupes?annee_id=${this.value}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(groupe => {
                    const opt = document.createElement('option');
                    opt.value = groupe.id;
                    opt.textContent = groupe.code;
                    if (groupe.id == selectedGroupe) opt.selected = true;
                    groupeSelect.appendChild(opt);
                });
                if (selectedGroupe) groupeSelect.dispatchEvent(new Event('change'));
            });
    });

    // 5. Groupe -> Stagiaires & Seances
    groupeSelect.addEventListener('change', function() {
        resetStagiairesAndSeances();
        if (!this.value) return;

        // Fetch stagiaires
        fetch(`/api/v1/hierarchy/stagiaires?groupe_id=${this.value}`)
            .then(res => res.json())
            .then(data => {
                allStagiaires = data;
                renderStagiaireOptions(data);
                searchStagiaire.disabled = false;
            });

        // Fetch seances
        fetch(`/api/v1/hierarchy/seances?groupe_id=${this.value}`)
            .then(res => res.json())
            .then(data => {
                // Only show evaluable sessions
                const evaluable = data.filter(s => s.is_evaluable || s.id == selectedSeance);
                if (evaluable.length === 0) {
                    seanceSelect.innerHTML = '<option value="">— Aucune séance d\'évaluation trouvée pour ce groupe —</option>';
                    return;
                }
                
                evaluable.forEach(seance => {
                    const opt = document.createElement('option');
                    opt.value = seance.id;
                    opt.textContent = `${seance.formatted_date} — ${seance.module?.libelle || 'Module'} (${seance.type.toUpperCase()})`;
                    opt.dataset.type = seance.type;
                    if (seance.id == selectedSeance) opt.selected = true;
                    seanceSelect.appendChild(opt);
                });
            });
    });

    // 6. Live filter stagiaires by input text
    searchStagiaire.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const filtered = allStagiaires.filter(s => 
            s.nom.toLowerCase().includes(query) || 
            s.prenom.toLowerCase().includes(query) || 
            s.cef.toLowerCase().includes(query)
        );
        renderStagiaireOptions(filtered);
    });

    // 7. Auto select Note Type when Seance changes
    seanceSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption && selectedOption.dataset.type) {
            typeSelect.value = selectedOption.dataset.type;
        } else {
            typeSelect.value = '';
        }
    });

    function renderStagiaireOptions(list) {
        stagiaireSelect.innerHTML = '<option value="">— Choisir un stagiaire (' + list.length + ' disponible(s)) —</option>';
        if (list.length === 0) {
            stagiaireSelect.innerHTML = '<option value="">— Aucun stagiaire trouvé —</option>';
            return;
        }
        list.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.cef;
            opt.textContent = `${s.nom} ${s.prenom} (${s.cef})`;
            if (s.cef === selectedStagiaire) opt.selected = true;
            stagiaireSelect.appendChild(opt);
        });
    }

    function resetStagiairesAndSeances() {
        allStagiaires = [];
        stagiaireSelect.innerHTML = '<option value="">— Choisir un stagiaire —</option>';
        seanceSelect.innerHTML = '<option value="">— Choisir une séance —</option>';
        searchStagiaire.value = '';
        searchStagiaire.disabled = true;
    }
});
</script>
@endpush
