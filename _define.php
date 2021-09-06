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
    'Éditorial',                                    // Name
    'A theme for Dotclear',                         // Description
    'Philippe aka amalgame and contributors',       // Author
    '2.1',                                          // Version
    [                                               // Properties
        'requires'          => [['core', '2.19']],
        'standalone_config' => true,
        'type'   => 'theme',
        'tplset' => 'dotty'
    ]
);
