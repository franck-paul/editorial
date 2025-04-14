'use strict';
document.addEventListener('DOMContentLoaded', () => {
    const themeConfig = document.getElementById('stickers');
    if (themeConfig) {


        // Big image
        document.getElementById('default_image_selector').addEventListener('click', function (e) {
            document.querySelector('input[name="change-button-id"]').value = this.id;
            window.open(
                'index.php?process=Media&plugin_id=admin.blog.theme&popup=1&select=1',
                'dc_popup',
                'alwaysRaised=yes,dependent=yes,toolbar=yes,height=500,width=760,menubar=no,resizable=yes,scrollbars=yes,status=no'
            );
            e.preventDefault();
        });

        document.getElementById('default_image_selector_reset').addEventListener('click', () => {
            const themeUrl = document.querySelector('input[name="theme-url"]').value;
            const url = `${themeUrl}/images/image-placeholder-1920x1080.jpg`;
            const thumb = `${themeUrl}/images/.image-placeholder-1920x1080_s.jpg`;
            document.getElementById('default_image_url').value = url;
            document.getElementById('default_image_tb_url').value = thumb;
            document.getElementById('default_image_tb_src').src = thumb;
            document.getElementById('default_image_media_alt').value = '';
        });

        document.getElementById('default_image_url').addEventListener('change', () => {
            const themeUrl = document.querySelector('input[name="theme-url"]').value;
            const defaultUrl = `${themeUrl}/images/image-placeholder-1920x1080.jpg`;
            const thumb = `${themeUrl}/images/.image-placeholder-1920x1080_s.jpg`;
            if (document.getElementById('default_image_url').value !== defaultUrl) {
                thumb = document.getElementById('default_image_tb_url').value;
            }
            document.getElementById('default_image_tb_src').src = thumb;
        });

        // Small image
        document.getElementById('default_small_image_selector').addEventListener('click', function (e) {
            document.querySelector('input[name="change-button-id"]').value = this.id;
            window.open(
                'index.php?process=Media&plugin_id=admin.blog.theme&popup=1&select=1',
                'dc_popup',
                'alwaysRaised=yes,dependent=yes,toolbar=yes,height=500,width=760,menubar=no,resizable=yes,scrollbars=yes,status=no'
            );
            e.preventDefault();
        });

        document.getElementById('default_small_image_selector_reset').addEventListener('click', () => {
            const themeUrl = document.querySelector('input[name="theme-url"]').value;
            const url = `${themeUrl}/images/image-placeholder-600x338.jpg`;
            const thumb = `${themeUrl}/images/.image-placeholder-600x338_s.jpg`;
            document.getElementById('default_small_image_url').value = url;
            document.getElementById('default_small_image_tb_url').value = thumb;
            document.getElementById('default_small_image_tb_src').src = thumb;
            document.getElementById('default_small_image_media_alt').value = '';
        });

        document.getElementById('default_small_image_url').addEventListener('change', () => {
            const themeUrl = document.querySelector('input[name="theme-url"]').value;
            const defaultUrl = `${themeUrl}/images/image-placeholder-600x338.jpg`;
            const thumb = `${themeUrl}/images/.image-placeholder-600x338_s.jpg`;
            if (document.getElementById('default_small_image_url').value !== defaultUrl) {
                thumb = document.getElementById('default_small_image_tb_url').value;
            }
            document.getElementById('default_small_image_tb_src').src = thumb;
        });

        document.getElementById('featured_post_url_selector').addEventListener('click', function (e) {
            window.open(
                'popup_posts.php?plugin_id=admin.blog.theme&type=post',
                'dc_popup',
                'alwaysRaised=yes,dependent=yes,toolbar=yes,height=500,width=760,menubar=no,resizable=yes,scrollbars=yes,status=no'
            );
            e.preventDefault();
        });

        // Stickers reorder
        const stickersList = document.getElementById('stickerslist');
        let draggedRow = null;

        stickersList.addEventListener('dragstart', (e) => {
            if (e.target.tagName === 'TR') {
                draggedRow = e.target;
                e.target.style.opacity = '0.5';
            }
        });

        stickersList.addEventListener('dragend', (e) => {
            if (e.target.tagName === 'TR') {
                e.target.style.opacity = '';
            }
        });

        stickersList.addEventListener('dragover', (e) => {
            e.preventDefault();
            const targetRow = e.target.closest('tr');
            if (targetRow && targetRow !== draggedRow) {
                const bounding = targetRow.getBoundingClientRect();
                const offset = e.clientY - bounding.top;
                if (offset > bounding.height / 2) {
                    targetRow.after(draggedRow);
                } else {
                    targetRow.before(draggedRow);
                }
            }
        });

        stickersList.querySelectorAll('tr').forEach(row => {
            row.setAttribute('draggable', true);
            row.style.cursor = 'move';
            const handle = row.querySelector('td.handle');
            if (handle) {
                //handle.style.display = 'block';
                handle.style.backgroundImage = 'url("style/drag.svg")';
                handle.style.backgroundRepeat = 'no-repeat';
                handle.style.backgroundPosition = 'center';
                handle.style.width = '20px';
                handle.style.height = '20px';
            }
        });

        Array.from(stickersList.querySelectorAll('tr td input.position')).forEach(input => input.style.display = 'none');
        Array.from(stickersList.querySelectorAll('tr td.handle')).forEach(handler => handler.classList.add('handler'));


        themeConfig.addEventListener('submit', () => {
            const order = Array.from(stickersList.querySelectorAll('tr td input.position')).map(input =>
                input.name.replace(/^order\[([^\]]+)\]$/, '$1')
            );
            document.querySelector('input[name="ds_order"]').value = order.join(',');
        });
    }


});