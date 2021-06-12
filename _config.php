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
if (!isset($s['static_home_url'])) {
    $s['static_home_url'] = '';
}
if (!isset($s['main_color'])) {
    $s['main_color'] = '#f56a6a';
}

// Load contextual help
if (file_exists(dirname(__FILE__) . '/locales/' . $_lang . '/resources.php')) {
    require dirname(__FILE__) . '/locales/' . $_lang . '/resources.php';
}

if (!empty($_POST)) {
    try {
        # HTML
        $s['static_home_url'] = $_POST['static_home_url'];
        $s['main_color'] = $_POST['main_color'];
        
        $core->blog->settings->addNamespace('themes');
        $core->blog->settings->themes->put($core->blog->settings->system->theme . '_featured', serialize($s));

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

echo '<form id="theme_config" action="' . $core->adminurl->get('admin.blog.theme', ['conf' => '1']) .
    '" method="post" enctype="multipart/form-data">';

echo '<h4 class="pretty-title">' . __('Blog\'s featured publication') . '</h4>';

echo '<p><label for="static_home_url" class="classic">' . __('Entry URL:') . '</label> ' .
    form::field('static_home_url', 30, 255, html::escapeHTML($s['static_home_url'])) .
    ' <button type="button" id="featured_home_url_selector">' . __('Choose an entry') . '</button>' .
    '</p>' .
    '<p class="form-note info maximal">' . __('Leave this field empty to use the default presentation (latest post)') . '</p> ';

echo '<h4 class="pretty-title">' . __('Colors') . '</h4>';

echo '<p class="field"><label for="main_color">' . __('Links and buttons\' color:') . '</label> ' .
    form::color('main_color', 30, 255, $s['main_color']) . '</p>' ;

echo '<p class="clear"><input type="submit" value="' . __('Save') . '" />' . $core->formNonce() . '</p>';
echo '</form>';


dcPage::helpBlock('editorial');

// Legacy mode
if (!$standalone_config) {
    echo '<form style="display:none">';
}
