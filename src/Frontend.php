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

use ArrayObject;
use Dotclear\App;
use Dotclear\Core\Process;

class Frontend extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::FRONTEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        # load locales
        My::l10n('main');

        # Templates

        App::behavior()->addBehavior('publicHeadContent', [self::class, 'publicHeadContent']);

        App::frontend()->template()->addBlock('editorialDefaultIf', [self::class, 'editorialDefaultIf']);
        App::frontend()->template()->addBlock('editorialFeaturedIf', [self::class, 'editorialFeaturedIf']);

        App::frontend()->template()->addValue('editorialBigImage', [self::class, 'editorialBigImage']);
        App::frontend()->template()->addValue('editorialSmallImage', [self::class, 'editorialSmallImage']);

        App::frontend()->template()->addValue('editorialUserColors', [self::class, 'editorialUserColors']);
        App::frontend()->template()->addValue('editorialSocialLinks', [self::class, 'editorialSocialLinks']);

        App::behavior()->addBehavior('templateBeforeBlockV2', [self::class, 'templateBeforeBlock']);

        return true;
    }

    public static function editorialDefaultIf(ArrayObject $attr, string $content)
    {
        $s = App::blog()->settings->themes->get(App::blog()->settings->system->theme . '_featured');
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
        $s = App::blog()->settings->themes->get(App::blog()->settings->system->theme . '_featured');
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

    public static function editorialBigImage(ArrayObject $attr): string
    {
        return '<?php echo ' . self::class . '::bigImageHelper(); ?>';
    }

    public static function bigImageHelper()
    {
        $si = App::blog()->settings->themes->get(App::blog()->settings->system->theme . '_images');
        $si = $si ? (unserialize($si) ?: []) : [];

        if (!is_array($si)) {
            $si = [];
        }

        $imgSrc = $si['default_image_url'];

        if (!empty($imgSrc)) {
            $parsedUrl = parse_url($imgSrc);
            $path      = $parsedUrl['path'] ?? '';

            $pathInfo  = pathinfo($path);
            $imgSrc    = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.' . $pathInfo['extension'];
        }

        return $imgSrc;
    }

    public static function editorialSmallImage(ArrayObject $attr): string
    {
        return '<?php echo ' . self::class . '::smallImageHelper(); ?>';
    }

    public static function smallImageHelper()
    {
        $si = App::blog()->settings->themes->get(App::blog()->settings->system->theme . '_images');
        $si = $si ? (unserialize($si) ?: []) : [];

        $imgSrc = $si['default_small_image_url'];

        if (!empty($imgSrc)) {
            $parsedUrl = parse_url($imgSrc);
            $path      = $parsedUrl['path'] ?? '';

            $pathInfo  = pathinfo($path);
            $extension = strtolower($pathInfo['extension']) === 'jpeg' ? 'jpg' : $pathInfo['extension'];
            $imgSrc    = $pathInfo['dirname'] . '/' . '.' . $pathInfo['filename'] . '_m.' . $extension;
        }

        return $imgSrc;
    }

    public static function editorialUserColors(ArrayObject $attr): string
    {
        return '<?php echo ' . self::class . '::editorialUserColorsHelper(); ?>';
    }

    public static function editorialUserColorsHelper()
    {
        $style = App::blog()->settings->themes->get(App::blog()->settings->system->theme . '_style');
        $style = $style ? (unserialize($style) ?: []) : [];

        if (!is_array($style)) {
            $style = [];
        }
        if (!isset($style['main_color'])) {
            $style['main_color'] = '#f56a6a';
        }

        $main_color = $style['main_color'];

        if ($main_color != '#f56a6a') {
            return
            '<style type="text/css">' . "\n" .
            ':root {--main-color: ' . $main_color . '}' . "\n" .
            '</style>' . "\n";
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

        $style = App::blog()->settings->themes->get(App::blog()->settings->system->theme . '_stickers');

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
            '<li><a class="social-icon" title="' . $label . '" href="' . $url . '"><span class="sr-only">' . $label . '</span>' .
            '<i class="' . $image . '"></i>' .
            '</a></li>' . "\n";
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
        $s = App::blog()->settings->themes->get(App::blog()->settings->system->theme . '_featured');
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
            "if (!isset(\$params)) { \$params['post_type'] = ['post', 'page', 'related']; }\n" .
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
