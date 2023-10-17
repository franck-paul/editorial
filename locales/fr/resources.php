<?php
/**
 * @package Dotclear
 * @subpackage Themes
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */

declare(strict_types=1);

namespace Dotclear\Theme\editorial;

use Dotclear\App;

App::backend()->resources()->set('help', 'editorial', __DIR__ . '/help/help.html');
