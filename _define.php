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
if (!defined('DC_RC_PATH')) {
    return;
}

$this->registerModule(
    'Éditorial',
    'A theme for Dotclear',
    'Philippe aka amalgame and contributors',
    '3.2',
    [
        'requires'          => [['core', '2.25']],
        'standalone_config' => true,
        'type'              => 'theme',
        'tplset'            => 'dotty',
    ]
);
