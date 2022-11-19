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

use ArrayObject;

\l10n::set(dirname(__FILE__) . '/locales/' . \dcCore::app()->lang . '/main');

\dcCore::app()->tpl->addBlock('editorialDefaultIf', [featuredPostTpl::class, 'editorialDefaultIf']);
\dcCore::app()->tpl->addBlock('editorialFeaturedIf', [featuredPostTpl::class, 'editorialFeaturedIf']);

\dcCore::app()->tpl->addValue('editorialUserColors', [tplEditorialTheme::class, 'editorialUserColors']);
\dcCore::app()->tpl->addValue('editorialSocialLinks', [tplEditorialTheme::class, 'editorialSocialLinks']);

\dcCore::app()->addBehavior('templateBeforeBlockV2', [behaviorsFeaturedPost::class, 'templateBeforeBlock']);

class featuredPostTpl
{
    public static function editorialDefaultIf(ArrayObject $attr, string $content)
    {
        $s = \dcCore::app()->blog->settings->themes->get(\dcCore::app()->blog->settings->system->theme . '_featured');
        $s = $s ? (unserialize($s) ?: []) : [];

        if (!is_array($s)) {
            $s = [];
        }
        if (!isset($s['featured_post_url'])) {
            $s['featured_post_url'] = '';
        }

        $featuredPostURL = $s['featured_post_url'];

        if (empty($featuredPostURL)) {
            return $content;
        }
    }

    public static function editorialFeaturedIf(ArrayObject $attr, string $content)
    {
        $s = \dcCore::app()->blog->settings->themes->get(\dcCore::app()->blog->settings->system->theme . '_featured');
        $s = $s ? (unserialize($s) ?: []) : [];

        if (!is_array($s)) {
            $s = [];
        }
        if (!isset($s['featured_post_url'])) {
            $s['featured_post_url'] = '';
        }

        $featuredPostURL = $s['featured_post_url'];

        if (!empty($featuredPostURL)) {
            return $content;
        }
    }
}

class tplEditorialTheme
{
    public static function editorialUserColors(ArrayObject $attr)
    {
        return '<?php echo ' . __NAMESPACE__ . '\tplEditorialTheme::editorialUserColorsHelper(); ?>';
    }

    public static function editorialUserColorsHelper()
    {
        if (preg_match('#^http(s)?://#', \dcCore::app()->blog->settings->system->themes_url)) {
            $theme_url = \http::concatURL(\dcCore::app()->blog->settings->system->themes_url, '/' . \dcCore::app()->blog->settings->system->theme);
        } else {
            $theme_url = \http::concatURL(\dcCore::app()->blog->url, \dcCore::app()->blog->settings->system->themes_url . '/' . \dcCore::app()->blog->settings->system->theme);
        }

        $s = \dcCore::app()->blog->settings->themes->get(\dcCore::app()->blog->settings->system->theme . '_featured');
        $s = $s ? (unserialize($s) ?: []) : [];

        if (!is_array($s)) {
            $s = [];
        }
        if (!isset($s['main_color'])) {
            $s['main_color'] = '#f56a6a';
        }

        $editorial_user_main_color = $s['main_color'];

        $editorial_user_colors_css_url = $theme_url . '/assets/css/user-colors.php';

        if ($editorial_user_main_color != '#f56a6a') {
            $editorial_user_main_color = substr($editorial_user_main_color, 1);

            return '<link rel="stylesheet" type="text/css" href="' . $editorial_user_colors_css_url . '?main_color=' . $editorial_user_main_color . '" media="screen" />';
        }
    }

    public static function editorialSocialLinks(ArrayObject $attr)
    {
        return '<?php echo ' . __NAMESPACE__ . '\tplEditorialTheme::editorialSocialLinksHelper(); ?>';
    }

    public static function editorialSocialLinksHelper()
    {
        // Social media links
        $res = '';

        $s = \dcCore::app()->blog->settings->themes->get(\dcCore::app()->blog->settings->system->theme . '_stickers');

        if ($s === null) {
            $default = true;
        } else {
            $s = $s ? (unserialize($s) ?: []) : [];

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
    public static function templateBeforeBlock(string $block, ArrayObject $attr): string
    {
        $s = \dcCore::app()->blog->settings->themes->get(\dcCore::app()->blog->settings->system->theme . '_featured');
        $s = $s ? (unserialize($s) ?: []) : [];

        if (!is_array($s)) {
            $s = [];
        }
        if (!isset($s['featured_post_url'])) {
            $s['featured_post_url'] = '';
        }

        $featuredPostURL = $s['featured_post_url'];

        if ($block == 'Entries' && isset($attr['featured_url']) && $attr['featured_url'] == 1) {
            return
            "<?php\n" .
            "if (!isset(\$params)) { \$params = []; }\n" .
            "if (!isset(\$params['sql'])) { \$params['sql'] = ''; }\n" .
            "\$params['sql'] .= \"AND P.post_url = '" . urldecode($featuredPostURL) . "' \";\n" .
                "?>\n";
        } elseif ($block == 'Entries' && isset($attr['featured_url']) && $attr['featured_url'] == 0) {
            return
            "<?php\n" .
            "if (!isset(\$params)) { \$params = []; }\n" .
            "if (!isset(\$params['sql'])) { \$params['sql'] = ''; }\n" .
            "\$params['sql'] .= \"AND P.post_url != '" . urldecode($featuredPostURL) . "' \";\n" .
                "?>\n";
        }

        return '';
    }
}
