'use strict';
Object.assign(dotclear, dotclear.getData('admin.blog_pref'));
dotclear.ready(() => {
    const cancel = document.getElementById('media-insert-cancel');
    if (cancel)
        cancel.addEventListener('click', () => { window.close(); });
    if (window.opener) {
        const button = document.querySelectorAll('media-select-ok');
        button.addEventListener('click', () => {

            if (window.opener) {
                const main = window.opener;
                const href = document.querySelector('input[name="url"]').value;
                const thumburl = document.querySelectorAll('input[name="src"]')[2].value;
                let buttonId = main.document.querySelector('input[name="change-button-id"]').value;

                if (buttonId === 'default_image_selector') {
                    main.document.querySelector('#default_image_tb_url').value = thumburl;
                    const defaultImageUrl = main.document.querySelector('#default_image_url');
                    defaultImageUrl.value = href;
                    defaultImageUrl.dispatchEvent(new Event('change'));
                }
                if (buttonId === 'default_small_image_selector') {
                    main.document.querySelector('#default_small_image_tb_url').value = thumburl;
                    const defaultSmallImageUrl = main.document.querySelector('#default_small_image_url');
                    defaultSmallImageUrl.value = href;
                    defaultSmallImageUrl.dispatchEvent(new Event('change'));
                }
            }
            window.close();
        });
    }

});