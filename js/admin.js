'use strict';
dotclear.ready(() => {
    //Big image
    $('#default_image_selector').on('click', function (e) {
        $('input[name="change-button-id"]').val(this.id);
        window.open(
            'index.php?process=Media&plugin_id=admin.blog.theme&popup=1&select=1',
            'dc_popup',
            'alwaysRaised=yes,dependent=yes,toolbar=yes,height=500,width=760,menubar=no,resizable=yes,scrollbars=yes,status=no',
        );
        e.preventDefault();
        return false;
    });
    $('#default_image_selector_reset').on('click', (e) => {
        const url = `${$('input[name="theme-url"]').val()}/images/image-placeholder-1920x1080.jpg`;
        const thumb = `${$('input[name="theme-url"]').val()}/images/.image-placeholder-1920x1080_s.jpg`;
        $('#default_image_url').val(url);
        $('#default_image_tb_url').val(thumb);
        $('#default_image_tb_src').attr('src', thumb);
        $('#default_image_media_alt').val('');
    });

    $('#default_image_url').on('change', (e) => {
        const url = `${$('input[name="theme-url"]').val()}/images/image-placeholder-1920x1080.jpg`;
        let thumb = `${$('input[name="theme-url"]').val()}/images/.image-placeholder-1920x1080_s.jpg`;
        if ($('#default_image_url').val() != url) {
            thumb = $('#default_image_tb_url').val();
        }
        $('#default_image_tb_src').attr('src', thumb);
    });
    
    //Small image
    $('#default_small_image_selector').on('click', function (e) {
        $('input[name="change-button-id"]').val(this.id);
        window.open(
            'index.php?process=Media&plugin_id=admin.blog.theme&popup=1&select=1',
            'dc_popup',
            'alwaysRaised=yes,dependent=yes,toolbar=yes,height=500,width=760,menubar=no,resizable=yes,scrollbars=yes,status=no',
        );
        e.preventDefault();
        return false;
    });
    $('#default_small_image_selector_reset').on('click', (e) => {
        const url = `${$('input[name="theme-url"]').val()}/images/image-placeholder-600x338.jpg`;
        const thumb = `${$('input[name="theme-url"]').val()}/images/.image-placeholder-600x338_s.jpg`;
        $('#default_small_image_url').val(url);
        $('#default_small_image_tb_url').val(thumb);
        $('#default_small_image_tb_src').attr('src', thumb);
        $('#default_small_image_media_alt').val('');
    });

    $('#default_small_image_url').on('change', (e) => {
        const url = `${$('input[name="theme-url"]').val()}/images/image-placeholder-600x338.jpg`;
        let thumb = `${$('input[name="theme-url"]').val()}/images/.image-placeholder-600x338_s.jpg`;
        if ($('#default_small_image_url').val() != url) {
            thumb = $('#default_small_image_tb_url').val();
        }
        $('#default_small_image_tb_src').attr('src', thumb);
    });

    $('#featured_post_url_selector').on('click', function (e) {
        window.open('popup_posts.php?plugin_id=admin.blog.theme&type=post', 'dc_popup', 'alwaysRaised=yes,dependent=yes,toolbar=yes,height=500,width=760,menubar=no,resizable=yes,scrollbars=yes,status=no');
        e.preventDefault();
        return false;
    });

    // stickers reorder
    $('#stickerslist').sortable({
        'cursor': 'move'
    });
    $('#stickerslist tr').hover(function () {
        $(this).css({
            'cursor': 'move'
        });
    }, function () {
        $(this).css({
            'cursor': 'auto'
        });
    });
    $('#theme_config').submit(function () {
        const order = [];
        $('#stickerslist tr td input.position').each(function () {
            order.push(this.name.replace(/^order\[([^\]]+)\]$/, '$1'));
        });
        $('input[name=ds_order]')[0].value = order.join(',');
        return true;
    });
    $('#stickerslist tr td input.position').hide();
    $('#stickerslist tr td.handle').addClass('handler');
});