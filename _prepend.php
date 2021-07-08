<?php
/**
 * @brief Éditorial, a theme for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Themes
 *
 * @copyright Philippe aka amalgame
 * @copyright GPL-2.0-only
 */

namespace themes\editorial;

if (!defined('DC_RC_PATH')) {
    return;
}
// public part below

if (!defined('DC_CONTEXT_ADMIN')) {
    return false;
}
// admin part below

# Behaviors
$GLOBALS['core']->addBehavior('adminPageHTMLHead', [__NAMESPACE__ . '\tplEditorialThemeAdmin', 'adminPageHTMLHead']);
$GLOBALS['core']->addBehavior('adminPopupPosts', [__NAMESPACE__ . '\tplEditorialThemeAdmin', 'adminPopupPosts']);

class tplEditorialThemeAdmin
{
    public static function adminPageHTMLHead()
    {
        global $core;
        if ($core->blog->settings->system->theme != 'editorial') {
            return;
        }

        echo "
        <script>
        $(function() {
            $('#featured_post_url_selector').on('click', function (e) {
                window.open('popup_posts.php?plugin_id=admin.blog.theme&type=post', 'dc_popup', 'alwaysRaised=yes,dependent=yes,toolbar=yes,height=500,width=760,' + 'menubar=no,resizable=yes,scrollbars=yes,status=no');
                e.preventDefault();
                return false;
            });
        });
        </script>";
    }

    public static function adminPopupPosts($editor = '')
    {
        $core = $GLOBALS['core'];

        if (empty($editor) || $editor != 'admin.blog.theme') {
            return;
        }
        if (preg_match('#^http(s)?://#', $core->blog->settings->system->themes_url)) {
            $theme_url = \http::concatURL($core->blog->settings->system->themes_url, '/' . $core->blog->settings->system->theme);
        } else {
            $theme_url = \http::concatURL($core->blog->url, $core->blog->settings->system->themes_url . '/' . $core->blog->settings->system->theme);
        }

        return '<script src="' . $theme_url . '/assets/js/popup_posts.js' . '"></script>';
    }
}
