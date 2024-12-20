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

use Dotclear\App;
use Dotclear\Core\Process;
use Dotclear\Core\Backend\Page;

class Backend extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::BACKEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        App::behavior()->addBehavior('adminPageHTMLHead', function () {
            if (App::blog()->settings->system->theme !== My::id()) {
                return;
            }

            echo
            My::jsLoad('admin.js') . "\n" .
            My::jsLoad('popup_posts.js') . "\n" .
            My::jsLoad('fontawesome.js') . "\n" .
            My::cssLoad('admin.css') . "\n" ;

            App::auth()->user_prefs->addWorkspace('accessibility');
            if (!App::auth()->user_prefs->accessibility->nodragdrop) {
                echo
                Page::jsLoad('js/jquery/jquery-ui.custom.js') .
                Page::jsLoad('js/jquery/jquery.ui.touch-punch.js');
            }
        });

        App::behavior()->addBehavior('adminPageHTTPHeaderCSP', function ($csp) {
            if (App::blog()->settings->system->theme !== My::id()) {
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
