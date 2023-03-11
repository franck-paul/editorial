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

use ArrayObject;
use dcCore;
use dcNsProcess;
use l10n;
use http;

class Frontend extends dcNsProcess
{
    public static function init(): bool
    {
        self::$init = defined('DC_RC_PATH');

        return self::$init;
    }

    public static function process(): bool
    {
        if (!self::$init) {
            return false;
        }

        l10n::set(__DIR__ . '/../locales/' . dcCore::app()->lang . '/main');

        # Templates
        dcCore::app()->tpl->addBlock('editorialDefaultIf', [self::class, 'editorialDefaultIf']);
        dcCore::app()->tpl->addBlock('editorialFeaturedIf', [self::class, 'editorialFeaturedIf']);

        dcCore::app()->tpl->addValue('editorialUserColors', [self::class, 'editorialUserColors']);
        dcCore::app()->tpl->addValue('editorialSocialLinks', [self::class, 'editorialSocialLinks']);

        dcCore::app()->addBehavior('templateBeforeBlockV2', [self::class, 'templateBeforeBlock']);

        return true;
    }

    public static function editorialDefaultIf(ArrayObject $attr, string $content)
    {
        $s = dcCore::app()->blog->settings->themes->get(dcCore::app()->blog->settings->system->theme . '_featured');
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
        $s = dcCore::app()->blog->settings->themes->get(dcCore::app()->blog->settings->system->theme . '_featured');
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

    public static function editorialUserColors(ArrayObject $attr): string
    {
        return '<?php echo ' . self::class . '::editorialUserColorsHelper(); ?>';
    }

    public static function editorialUserColorsHelper()
    {
        $style = dcCore::app()->blog->settings->themes->get(dcCore::app()->blog->settings->system->theme . '_style');
        $style = $style ? (unserialize($style) ?: []) : [];

        if (!is_array($style)) {
            $style = [];
        }
        if (!isset($style['main_color'])) {
            $style['main_color'] = '#F56A6A';
        }

        $editorial_user_main_color = $style['main_color'];

        if (preg_match('#^http(s)?://#', dcCore::app()->blog->settings->system->themes_url)) {
            $theme_url = http::concatURL(dcCore::app()->blog->settings->system->themes_url, '/' . dcCore::app()->blog->settings->system->theme);
        } else {
            $theme_url = http::concatURL(dcCore::app()->blog->url, dcCore::app()->blog->settings->system->themes_url . '/' . dcCore::app()->blog->settings->system->theme);
        }

        $editorial_user_colors_css_url = $theme_url . '/assets/css/user-colors.php';

        if ($editorial_user_main_color != '#F56A6A') {
            $editorial_user_main_color = substr($editorial_user_main_color, 1);

            return '<link rel="stylesheet" type="text/css" href="' . $editorial_user_colors_css_url . '?main_color=' . $editorial_user_main_color . '" media="screen" />';
        }
    }

    public static function editorialSocialLinks($attr)
    {
        return '<?php echo ' . self::class . '::editorialSocialLinksHelper(); ?>';
    }
    public static function editorialSocialLinksHelper()
    {
        # Social media links
        $res = '';

        $style = dcCore::app()->blog->settings->themes->get(dcCore::app()->blog->settings->system->theme . '_stickers');

        if ($style === null) {
            $default = true;
        } else {
            $style = $style ? (unserialize($style) ?: []) : [];

            $style = array_filter($style, self::class . '::cleanSocialLinks');

            $count = 0;
            foreach ($style as $sticker) {
                $res .= self::setSocialLink($count, ($count == count($style)), $sticker['label'], $sticker['url'], $sticker['image']);
                $count++;
            }
        }

        if ($res != '') {
            return $res;
        }
    }
    protected static function setSocialLink($position, $last, $label, $url, $image)
    {
        return
            '<a class="social-icon" title="' . $label . '" href="' . $url . '"><span class="sr-only">' . $label . '</span>' .
            '<i class="' . $image . '"></i>' .
            '</a>' . "\n";
    }

    protected static function cleanSocialLinks($style)
    {
        if (is_array($style)) {
            if (isset($style['label']) && isset($style['url']) && isset($style['image'])) {
                if ($style['label'] != null && $style['url'] != null && $style['image'] != null) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function templateBeforeBlock(string $block, ArrayObject $attr): string
    {
        $s = dcCore::app()->blog->settings->themes->get(dcCore::app()->blog->settings->system->theme . '_featured');
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
