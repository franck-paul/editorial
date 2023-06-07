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
declare(strict_types=1);

namespace Dotclear\Theme\editorial;

use dcCore;
use dcNsProcess;
use dcPage;
use Dotclear\Helper\Network\Http;

class Prepend extends dcNsProcess
{
    public static function init(): bool
    {
        return (static::$init = My::checkContext(My::PREPEND));
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        dcCore::app()->addBehavior('adminPageHTMLHead', function () {
            if (dcCore::app()->blog->settings->system->theme !== My::id()) {
                return;
            }

            if (preg_match('#^http(s)?://#', dcCore::app()->blog->settings->system->themes_url)) {
                $theme_url = Http::concatURL(dcCore::app()->blog->settings->system->themes_url, '/' . dcCore::app()->blog->settings->system->theme);
            } else {
                $theme_url = Http::concatURL(dcCore::app()->blog->url, dcCore::app()->blog->settings->system->themes_url . '/' . dcCore::app()->blog->settings->system->theme);
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
            if (dcCore::app()->blog->settings->system->theme !== My::id()) {
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
