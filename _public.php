<?php
/**
 * @brief Editorial, a theme for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Themes
 *
 * @copyright Philippe aka amalgame
 * @copyright GPL-2.0-only
 */

if (!defined('DC_RC_PATH')) {
    return;
}

l10n::set(dirname(__FILE__).'/locales/'.$_lang.'/main');

$core->tpl->addBlock('editorialDefaultIf', ['featuredPostTpl', 'editorialDefaultIf']);
$core->tpl->addBlock('editorialFeaturedIf', ['featuredPostTpl', 'editorialFeaturedIf']);
$core->tpl->addValue('editorialUserColors', ['featuredPostTpl', 'editorialUserColors']);

$core->addBehavior('templateBeforeBlock', array('behaviorsFeaturedPost','templateBeforeBlock'));

class featuredPostTpl
{
    public static function editorialDefaultIf($attr, $content)
    {
        $s = $GLOBALS['core']->blog->settings->themes->get($GLOBALS['core']->blog->settings->system->theme . '_featured');
        $s = @unserialize($s);

        if (!is_array($s)) {
            $s = [];
        }
        if (!isset($s['featured_post_url'])) {
            $s['featured_post_url'] = '';
        }

        $featuredPostURL = $s['featured_post_url'];

        if ($featuredPostURL =='') {
            return $content;
        } else {
            return;
        }
    }

    public static function editorialFeaturedIf($attr, $content)
    {
        $s = $GLOBALS['core']->blog->settings->themes->get($GLOBALS['core']->blog->settings->system->theme . '_featured');
        $s = @unserialize($s);

        if (!is_array($s)) {
            $s = [];
        }
        if (!isset($s['featured_post_url'])) {
            $s['featured_post_url'] = '';
        }

        $featuredPostURL = $s['featured_post_url'];

        if ($featuredPostURL !=='') {
            return $content;
        } else {
            return;
        }
    }

    public static function editorialUserColors($attr)
    {
        $core = $GLOBALS['core'];

        if (preg_match('#^http(s)?://#', $core->blog->settings->system->themes_url)) {
            $theme_url = http::concatURL($core->blog->settings->system->themes_url, '/' . $core->blog->settings->system->theme);
        } else {
            $theme_url = http::concatURL($core->blog->url, $core->blog->settings->system->themes_url . '/' . $core->blog->settings->system->theme);
        }
        
        $s = $core->blog->settings->themes->get($core->blog->settings->system->theme . '_featured');
        $s = @unserialize($s);

        if (!is_array($s)) {
            $s = [];
        }
        if (!isset($s['main_color'])) {
            $s['main_color'] = '#f56a6a';
        }

        $editorial_user_main_color = $s['main_color'];

        $editorial_user_colors_css_url = $theme_url ."/assets/css/user-colors.php";

        if ($editorial_user_main_color !=='#f56a6a') {
            $editorial_user_main_color = substr($editorial_user_main_color, 1);
            return
            "<?php\n" .
            "echo \"<link rel='stylesheet' type='text/css' href='". $editorial_user_colors_css_url ."?color=".$editorial_user_main_color."' media='screen' />\"" .
                " ?>\n";
        } else {
            return;
        }
    }
}

class behaviorsFeaturedPost
{
    public static function templateBeforeBlock($core, $b, $attr)
    {
        $s = $GLOBALS['core']->blog->settings->themes->get($GLOBALS['core']->blog->settings->system->theme . '_featured');
        $s = @unserialize($s);

        if (!is_array($s)) {
            $s = [];
        }
        if (!isset($s['featured_post_url'])) {
            $s['featured_post_url'] = '';
        }

        $featuredPostURL = $s['featured_post_url'];

        if ($b == 'Entries' && isset($attr['featured_url']) && $attr['featured_url'] == 1) {
            return
            "<?php\n" .
            "if (!isset(\$params)) { \$params = []; }\n" .
            "if (!isset(\$params['sql'])) { \$params['sql'] = ''; }\n" .
            "\$params['sql'] .= \"AND P.post_url = '" . urldecode($featuredPostURL) . "' \";\n" .
                "?>\n";
        } elseif ($b == 'Entries' && isset($attr['featured_url']) && $attr['featured_url'] == 0) {
            return
            "<?php\n" .
            "if (!isset(\$params)) { \$params = []; }\n" .
            "if (!isset(\$params['sql'])) { \$params['sql'] = ''; }\n" .
            "\$params['sql'] .= \"AND P.post_url != '" . urldecode($featuredPostURL) . "' \";\n" .
                "?>\n";
        }
    }
}
