'use strict';
Object.assign(dotclear, dotclear.getData('admin.blog_pref'));
dotclear.ready(() => {
    const cancel = document.getElementById('link-insert-cancel');
    if (cancel)
        cancel.addEventListener('click', () => { window.close(); });
    const entries = document.querySelectorAll('#form-entries tr>td.maximal>a');
    if (window.opener) {
        const base_url = window.opener.document.querySelector('input[name="base_url"]').value;

        for (const entry of entries) {
            entry.addEventListener('click', () => {
                const stripBaseURL = (url) => base_url !== '' && url.startsWith(base_url) ? url.substring(base_url.length) : url;

                if (window.opener) {
                    const title = stripBaseURL(entry.getAttribute('title'));
                    const next = title.indexOf('/');
                    const href = next === -1 ? title : title.substring(next + 1);
                    window.opener.document.getElementById('featured_post_url').setAttribute('value', href);
                }
                window.close();
            });
        }
    }
});