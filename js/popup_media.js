'use strict';
dotclear.ready(() => {
    $('#media-select-cancel').on('click', () => {
        window.close();
    });
    $('#media-select-ok').on('click', () => {
        const main = window.opener;
        const href = $('input[name="url"]').val();
        const thumburl = $('input[name="src"]').eq(2).val();
        let buttonId = main.$('input[name="change-button-id"]').val();

        if (buttonId == 'default_image_selector') {
            main.$('#default_image_tb_url').prop('value', thumburl);
            main.$('#default_image_url').prop('value', href).trigger('change');
        }
        if (buttonId == 'default_small_image_selector') {
            main.$('#default_small_image_tb_url').prop('value', thumburl);
            main.$('#default_small_image_url').prop('value', href).trigger('change');
        }
        window.close();
    });
});
