<?php
/**
 * @brief editorial, a theme for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Themes
 *
 * @copyright Philippe aka amalgame
 * @copyright GPL-2.0-only
 */

if (!defined('DC_CONTEXT_ADMIN')) {
    return;
}

l10n::set(dirname(__FILE__) . '/locales/' . $_lang . '/admin');

$standalone_config = (boolean) $core->themes->moduleInfo($core->blog->settings->system->theme, 'standalone_config');

$s = $GLOBALS['core']->blog->settings->themes->get($GLOBALS['core']->blog->settings->system->theme . '_featured');
$s = @unserialize($s);

if (!is_array($s)) {
    $s = [];
}
if (!isset($s['featured_post_url'])) {
    $s['featured_post_url'] = '';
}
if (!isset($s['main_color'])) {
    $s['main_color'] = '#f56a6a';
}

$stickers = $core->blog->settings->themes->get($core->blog->settings->system->theme . '_stickers');
$stickers = @unserialize($stickers);

$stickers_full = [];
// Get all sticker images already used
if (is_array($stickers)) {
    foreach ($stickers as $v) {
        $stickers_full[] = $v['image'];
    }
}

// Get social media images
$stickers_images = ['fab fa-diaspora','fas fa-rss','fab fa-linkedin-in','fab fa-gitlab','fab fa-github','fab fa-twitter','fab fa-facebook-f',
'fab fa-instagram', 'fab fa-mastodon','fab fa-pinterest','fab fa-snapchat','fab fa-soundcloud','fab fa-youtube'];
if (is_array($stickers_images)) {
    foreach ($stickers_images as $v) {
        if (!in_array($v, $stickers_full)) {
            // image not already used
            $stickers[] = [
                    'label' => null,
                    'url'   => null,
                    'image' => $v];
        }
    }
}

// Load contextual help
if (file_exists(dirname(__FILE__) . '/locales/' . $_lang . '/resources.php')) {
    require dirname(__FILE__) . '/locales/' . $_lang . '/resources.php';
}

$conf_tab = $_POST['conf_tab'] ?? 'presentation';

if (!empty($_POST)) {
    try {
        # HTML
        if ($conf_tab == 'presentation') {
            $s['featured_post_url'] = $_POST['featured_post_url'];
            $s['main_color'] = $_POST['main_color'];
        }

        if ($conf_tab == 'links') {
            $stickers = [];
            for ($i = 0; $i < count($_POST['sticker_image']); $i++) {
                $stickers[] = [
                    'label' => $_POST['sticker_label'][$i],
                    'url'   => $_POST['sticker_url'][$i],
                    'image' => $_POST['sticker_image'][$i]
                ];
            }

            $order = [];
            if (empty($_POST['ds_order']) && !empty($_POST['order'])) {
                $order = $_POST['order'];
                asort($order);
                $order = array_keys($order);
            }
            if (!empty($order)) {
                $new_stickers = [];
                foreach ($order as $i => $k) {
                    $new_stickers[] = [
                        'label' => $stickers[$k]['label'],
                        'url'   => $stickers[$k]['url'],
                        'image' => $stickers[$k]['image']
                    ];
                }
                $stickers = $new_stickers;
            }
        }
        
        $core->blog->settings->addNamespace('themes');
        $core->blog->settings->themes->put($core->blog->settings->system->theme . '_featured', serialize($s));
        $core->blog->settings->themes->put($core->blog->settings->system->theme . '_stickers', serialize($stickers));

        // Blog refresh
        $core->blog->triggerBlog();

        // Template cache reset
        $core->emptyTemplatesCache();

        dcPage::success(__('Theme configuration upgraded.'), true, true);
    } catch (Exception $e) {
        $core->error->add($e->getMessage());
    }
}

// Legacy mode
if (!$standalone_config) {
    echo '</form>';
}

echo '<div class="multi-part" id="themes-list' . ($conf_tab == 'presentation' ? '' : '-presentation') . '" title="' . __('Presentation') . '">';

echo '<form id="theme_config" action="' . $core->adminurl->get('admin.blog.theme', ['conf' => '1']) .
    '" method="post" enctype="multipart/form-data">';

echo '<h4 class="pretty-title">' . __('Blog\'s featured publication') . '</h4>';

echo '<p><label for="featured_post_url" class="classic">' . __('Entry URL:') . '</label> ' .
    form::field('featured_post_url', 30, 255, html::escapeHTML($s['featured_post_url'])) .
    ' <button type="button" id="featured_post_url_selector">' . __('Choose an entry') . '</button>' .
    '</p>' .
    '<p class="form-note info maximal">' . __('Leave this field empty to use the default presentation (latest post)') . '</p> ';

echo '<h4 class="pretty-title">' . __('Colors') . '</h4>';

echo '<p class="field"><label for="main_color">' . __('Links and buttons\' color:') . '</label> ' .
    form::color('main_color', 30, 255, $s['main_color']) . '</p>' ;

echo '<p><input type="hidden" name="conf_tab" value="presentation" /></p>';
echo '<p class="clear"><input type="submit" value="' . __('Save') . '" />' . $core->formNonce() . '</p>';
echo form::hidden(['base_url'], $GLOBALS['core']->blog->url);
echo '</form>';

echo '</div>'; // Close tab

echo '<div class="multi-part" id="themes-list' . ($conf_tab == 'links' ? '' : '-links') . '" title="' . __('Stickers') . '">';
echo '<form id="theme_config" action="' . $core->adminurl->get('admin.blog.theme', ['conf' => '1']) .
    '" method="post" enctype="multipart/form-data">';

echo '<h4 class="pretty-title">' . __('Social links') . '</h4>';

echo
'<div class="table-outer">' .
'<table class="dragable">' . '<caption class="sr-only">' . __('Social links (header)') . '</caption>' .
'<thead>' .
'<tr>' .
'<th scope="col">' . '</th>' .
'<th scope="col">' . __('Image') . '</th>' .
'<th scope="col">' . __('Label') . '</th>' .
'<th scope="col">' . __('URL') . '</th>' .
    '</tr>' .
    '</thead>' .
    '<tbody id="stickerslist">';
$count = 0;
foreach ($stickers as $i => $v) {
    $count++;
    $v['service'] = str_replace('-link.png', '', $v['image']);
    echo
    '<tr class="line" id="l_' . $i . '">' .
    '<td class="handle">' . form::number(['order[' . $i . ']'], [
        'min'     => 0,
        'max'     => count($stickers),
        'default' => $count,
        'class'   => 'position'
    ]) .
    form::hidden(['dynorder[]', 'dynorder-' . $i], $i) . '</td>' .
    '<td class="linkimg">' . form::hidden(['sticker_image[]'], $v['image']) . '<i class="' . $v['image'] . '" title="' . $v['label'] . '"></i> ' . '</td>' .
    '<td scope="row">' . form::field(['sticker_label[]', 'dsl-' . $i], 20, 255, $v['label']) . '</td>' .
    '<td>' . form::field(['sticker_url[]', 'dsu-' . $i], 40, 255, $v['url']) . '</td>' .
        '</tr>';
}
echo
    '</tbody>' .
    '</table></div>';

    echo '<p><input type="hidden" name="conf_tab" value="links" /></p>';
    echo '<p class="clear">' . form::hidden('ds_order', '') . '<input type="submit" value="' . __('Save') . '" />' . $core->formNonce() . '</p>';
    echo '</form>';

echo '</div>'; // Close tab

dcPage::helpBlock('editorial');

// Legacy mode
if (!$standalone_config) {
    echo '<form style="display:none">';
}
