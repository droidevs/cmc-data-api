/**
 * CMCCascade — shared utility for non-strict cascading filter selects.
 * All index views push their cascade logic via @push('scripts') and call
 * these helpers so the pattern stays DRY and consistent.
 */
window.CMCCascade = (function () {

    /**
     * Reset a <select> to its empty placeholder option only.
     * @param {HTMLSelectElement} select
     * @param {string}            placeholder  Text of the first <option value="">
     */
    function reset(select, placeholder) {
        const text = placeholder || (select.options[0] ? select.options[0].text : '—');
        select.innerHTML = `<option value="">${text}</option>`;
    }

    /**
     * Populate a <select> with items fetched from the API.
     * Restores the previously-selected value when the same item still exists.
     *
     * @param {HTMLSelectElement} select
     * @param {Array}   items       Array of objects from the API
     * @param {string}  valueKey    Property to use as <option value>
     * @param {string}  labelKey    Property to use as <option> text
     * @param {string}  placeholder Text of the empty first option
     * @param {string}  selected    Value to pre-select (e.g. old filter value)
     */
    function populate(select, items, valueKey, labelKey, placeholder, selected) {
        const prev     = selected !== undefined ? String(selected) : String(select.value);
        const ph       = placeholder || (select.options[0] ? select.options[0].text : '—');
        let   html     = `<option value="">${ph}</option>`;

        items.forEach(function (item) {
            const val  = item[valueKey];
            const lbl  = item[labelKey] || val;
            const sel  = String(val) === prev ? ' selected' : '';
            html      += `<option value="${val}"${sel}>${lbl}</option>`;
        });

        select.innerHTML = html;
    }

    /**
     * Fetch JSON from a URL (returns a Promise<Array>).
     * @param {string} url
     */
    function json(url) {
        return fetch(url).then(function (r) {
            if (!r.ok) return [];
            return r.json();
        }).catch(function () { return []; });
    }

    /**
     * Build a query-string URL, omitting blank/null params.
     * @param {string} base
     * @param {Object} params
     */
    function url(base, params) {
        const q = Object.entries(params)
            .filter(function (_ref) { return _ref[1] !== '' && _ref[1] !== null && _ref[1] !== undefined; })
            .map(function (_ref2) { return encodeURIComponent(_ref2[0]) + '=' + encodeURIComponent(_ref2[1]); })
            .join('&');
        return q ? base + '?' + q : base;
    }

    return { reset: reset, populate: populate, json: json, url: url };
})();//
