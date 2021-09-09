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
namespace themes\editorial;

if (!defined('DC_RC_PATH')) {
    return;
}

\l10n::set(dirname(__FILE__).'/locales/'.$_lang.'/main');

$core->tpl->addBlock('editorialDefaultIf', [__NAMESPACE__ . '\featuredPostTpl', 'editorialDefaultIf']);
$core->tpl->addBlock('editorialFeaturedIf', [__NAMESPACE__ . '\featuredPostTpl', 'editorialFeaturedIf']);

$core->tpl->addValue('editorialUserColors', [__NAMESPACE__ . '\tplEditorialTheme', 'editorialUserColors']);
$core->tpl->addValue('editorialSocialLinks', [__NAMESPACE__ . '\tplEditorialTheme', 'editorialSocialLinks']);

$core->addBehavior('templateBeforeBlock', [__NAMESPACE__ . '\behaviorsFeaturedPost','templateBeforeBlock']);

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
}

class tplEditorialTheme
{
    public static function editorialUserColors($attr)
    {
        return '<?php echo ' . __NAMESPACE__ . '\tplEditorialTheme::editorialUserColorsHelper(); ?>';
    }

    public static function editorialUserColorsHelper()
    {
        $core = $GLOBALS['core'];

        if (preg_match('#^http(s)?://#', $core->blog->settings->system->themes_url)) {
            $theme_url = \http::concatURL($core->blog->settings->system->themes_url, '/' . $core->blog->settings->system->theme);
        } else {
            $theme_url = \http::concatURL($core->blog->url, $core->blog->settings->system->themes_url . '/' . $core->blog->settings->system->theme);
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

        if ($editorial_user_main_color !='#f56a6a') {
            $editorial_user_main_color = substr($editorial_user_main_color, 1);
            return '<link rel="stylesheet" type="text/css" href="'. $editorial_user_colors_css_url .'?main_color='.$editorial_user_main_color.'" media="screen" />';
        } else {
            return;
        }
    }

    public static function editorialSocialLinks($attr)
    {
        return '<?php echo ' . __NAMESPACE__ . '\tplEditorialTheme::editorialSocialLinksHelper(); ?>';
    }

    public static function editorialSocialLinksHelper()
    {
        global $core;
        # Social media links
        $res     = '';

        $s = $core->blog->settings->themes->get($core->blog->settings->system->theme . '_stickers');

        if ($s === null) {
            $default = true;
        } else {
            $s = @unserialize($s);
            
            $s = array_filter($s, 'self::cleanSocialLinks');
                
            $count = 0;
            foreach ($s as $sticker) {
                $res .= self::setSocialLink($count, ($count == count($s)), $sticker['label'], $sticker['url'], $sticker['image']);
                $count++;
            }
        }

        if ($res != '') {
            return $res;
        }
    }
    protected static function setSocialLink($position, $last, $label, $url, $image)
    {
        return '<li id="slink' . $position . '"' . ($last ? ' class="last"' : '') . '>' . "\n" .
            '<a class="social-icon icon brands" title="' . $label . '" href="' . $url . '"><span class="sr-only">' . $label . '</span>' .
            '<i class="' . $image . '"></i>' .
            '</a>' . "\n" .
            '</li>' . "\n";
    }

    protected static function cleanSocialLinks($s)
    {
        if (is_array($s)) {
            if (isset($s['label']) && isset($s['url']) && isset($s['image'])) {
                if ($s['label'] != null && $s['url'] != null && $s['image'] != null) {
                    return true;
                }
            }
        }
        return false;
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
