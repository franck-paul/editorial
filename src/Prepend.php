<?php
/**
 * @brief Editorial, a theme for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Themes
 *
 * @copyright Philippe aka amalgame and HTML5 UP
 * @copyright GPL-2.0-only
 */

namespace Dotclear\Theme\Editorial;

use dcCore;
use dcNsProcess;
use dcPage;
use http;

class Prepend extends dcNsProcess
{
    public static function init(): bool
    {
        self::$init = defined('DC_CONTEXT_ADMIN');

        return self::$init;
    }

    public static function process(): bool
    {
        if (!self::$init) {
            return false;
        }

        dcCore::app()->addBehavior('adminPageHTMLHead', function () {
            if (dcCore::app()->blog->settings->system->theme !== basename(dirname(__DIR__))) {
                return;
            }

            if (preg_match('#^http(s)?://#', dcCore::app()->blog->settings->system->themes_url)) {
                $theme_url = http::concatURL(dcCore::app()->blog->settings->system->themes_url, '/' . dcCore::app()->blog->settings->system->theme);
            } else {
                $theme_url = http::concatURL(dcCore::app()->blog->url, dcCore::app()->blog->settings->system->themes_url . '/' . dcCore::app()->blog->settings->system->theme);
            }

            echo '<script src="' . $theme_url . '/assets/js/admin.js' . '"></script>' . "\n" .
            '<script src="https://use.fontawesome.com/releases/v5.15.4/js/all.js" crossorigin="anonymous"></script>' . "\n" .
            '<script src="' . $theme_url . '/assets/js/popup_posts.js' . '"></script>' . "\n" .
            '<link rel="stylesheet" media="screen" href="' . $theme_url . '/assets/css/admin.css' . '" />' . "\n";

            dcCore::app()->auth->user_prefs->addWorkspace('accessibility');
            if (!dcCore::app()->auth->user_prefs->accessibility->nodragdrop) {
                echo
                dcPage::jsLoad('js/jquery/jquery-ui.custom.js') .
                dcPage::jsLoad('js/jquery/jquery.ui.touch-punch.js');
            }
        });

        dcCore::app()->addBehavior('adminPageHTTPHeaderCSP', function ($csp) {
            if (dcCore::app()->blog->settings->system->theme !== basename(dirname(__DIR__))) {
                return;
            }

            if (isset($csp['script-src'])) {
                $csp['script-src'] .= ' use.fontawesome.com';
            } else {
                $csp['script-src'] = 'use.fontawesome.com';
            }
        });

        return true;
    }
}
