$(function () {
    $('#featured_post_url_selector').on('click', function (e) {
        window.open('popup_posts.php?plugin_id=admin.blog.theme&type=post', 'dc_popup', 'alwaysRaised=yes,dependent=yes,toolbar=yes,height=500,width=760,' + 'menubar=no,resizable=yes,scrollbars=yes,status=no');
        e.preventDefault();
        return false;
    });

    // stickers reorder
    $('#stickerslist').sortable({'cursor':'move'});
    $('#stickerslist tr').hover(function () {
        $(this).css({'cursor':'move'});
    }, function () {
        $(this).css({'cursor':'auto'});
    });
    $('#theme_config').submit(function() {
        var order=[];
        $('#stickerslist tr td input.position').each(function() {
            order.push(this.name.replace(/^order\[([^\]]+)\]$/,'$1'));
        });
        $('input[name=ds_order]')[0].value = order.join(',');
        return true;
    });
    $('#stickerslist tr td input.position').hide();
    $('#stickerslist tr td.handle').addClass('handler');
});