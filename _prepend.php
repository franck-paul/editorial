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

// Behaviors
\dcCore::app()->addBehavior('adminPageHTMLHead', [__NAMESPACE__ . '\tplEditorialThemeAdmin', 'adminPageHTMLHead']);
\dcCore::app()->addBehavior('adminPopupPosts', [__NAMESPACE__ . '\tplEditorialThemeAdmin', 'adminPopupPosts']);
\dcCore::app()->addBehavior('adminPageHTTPHeaderCSP', [__NAMESPACE__ . '\tplEditorialThemeAdmin', 'adminPageHTTPHeaderCSP']);

class tplEditorialThemeAdmin
{
    public static function adminPageHTMLHead()
    {
        if (\dcCore::app()->blog->settings->system->theme !== basename(dirname(__FILE__))) {
            return;
        }

        if (preg_match('#^http(s)?://#', \dcCore::app()->blog->settings->system->themes_url)) {
            $theme_url = \http::concatURL(\dcCore::app()->blog->settings->system->themes_url, '/' . \dcCore::app()->blog->settings->system->theme);
        } else {
            $theme_url = \http::concatURL(\dcCore::app()->blog->url, \dcCore::app()->blog->settings->system->themes_url . '/' . \dcCore::app()->blog->settings->system->theme);
        }

        echo '<script src="' . $theme_url . '/assets/js/admin.js' . '"></script>' . "\n" .
        '<script src="https://use.fontawesome.com/releases/v5.15.4/js/all.js" crossorigin="anonymous"></script>' . "\n" .
        '<link rel="stylesheet" media="screen" href="' . $theme_url . '/assets/css/admin.css' . '" />' . "\n";

        \dcCore::app()->auth->user_prefs->addWorkspace('accessibility');
        if (!\dcCore::app()->auth->user_prefs->accessibility->nodragdrop) {
            echo
            \dcPage::jsLoad('js/jquery/jquery-ui.custom.js') .
            \dcPage::jsLoad('js/jquery/jquery.ui.touch-punch.js');
        }
    }

    public static function adminPopupPosts($editor = '')
    {
        if (empty($editor) || $editor != 'admin.blog.theme') {
            return;
        }
        if (preg_match('#^http(s)?://#', \dcCore::app()->blog->settings->system->themes_url)) {
            $theme_url = \http::concatURL(\dcCore::app()->blog->settings->system->themes_url, '/' . \dcCore::app()->blog->settings->system->theme);
        } else {
            $theme_url = \http::concatURL(\dcCore::app()->blog->url, \dcCore::app()->blog->settings->system->themes_url . '/' . \dcCore::app()->blog->settings->system->theme);
        }

        return '<script src="' . $theme_url . '/assets/js/popup_posts.js' . '"></script>';
    }

    public static function adminPageHTTPHeaderCSP($csp)
    {
        if (\dcCore::app()->blog->settings->system->theme !== basename(dirname(__FILE__))) {
            return;
        }

        if (isset($csp['script-src'])) {
            $csp['script-src'] .= ' use.fontawesome.com';
        } else {
            $csp['script-src'] = 'use.fontawesome.com';
        }
    }
}
