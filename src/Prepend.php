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

            echo
            My::jsLoad('admin.js') . "\n" .
            My::jsLoad('popup_posts.js') . "\n" .
            '<script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js" integrity="sha384-rOA1PnstxnOBLzCLMcre8ybwbTmemjzdNlILg8O7z1lUkLXozs4DHonlDtnE7fpc" crossorigin="anonymous"></script>' . "\n" .
            My::cssLoad('admin.css') . "\n" ;

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
