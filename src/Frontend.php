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

        App::frontend()->template()->addBlock('editorialDefaultIf', self::editorialDefaultIf(...));
        App::frontend()->template()->addBlock('editorialFeaturedIf', self::editorialFeaturedIf(...));

        App::frontend()->template()->addBlock('editorialImagesIf', self::editorialImagesIf(...));

        App::frontend()->template()->addValue('editorialBigImage', self::editorialBigImage(...));
        App::frontend()->template()->addValue('editorialSmallImage', self::editorialSmallImage(...));

        App::frontend()->template()->addValue('editorialBigImageAlt', self::editorialBigImageAlt(...));
        App::frontend()->template()->addValue('editorialSmallImageAlt', self::editorialSmallImageAlt(...));

        App::frontend()->template()->addValue('editorialUserColors', self::editorialUserColors(...));
        App::frontend()->template()->addValue('editorialSocialLinks', self::editorialSocialLinks(...));

        App::behavior()->addBehavior('templateBeforeBlockV2', self::templateBeforeBlock(...));

        return true;
    }

    /**
     * @param   ArrayObject<string, mixed>  $attr   The attributes
     */
    public static function editorialDefaultIf(ArrayObject $attr, string $content): string
    {
        return '<?php if (' . self::class . '::defaultIf()) { ?>' . $content . '<?php } ?>';
    }

    public static function defaultIf(): bool
    {
        $featured = self::decode('featured');

        return empty($featured['featured_post_url'] ?? '');
    }

    /**
     * @param   ArrayObject<string, mixed>  $attr   The attributes
     */
    public static function editorialFeaturedIf(ArrayObject $attr, string $content): string
    {
        return '<?php if (!' . self::class . '::defaultIf()) { ?>' . $content . '<?php } ?>';
    }

    /**
     * @param   ArrayObject<string, mixed>  $attr   The attributes
     */
    public static function editorialImagesIf(ArrayObject $attr, string $content): string
    {
        return '<?php if (' . self::class . '::imagesIf()) { ?>' . $content . '<?php } ?>';
    }

    public static function imagesIf(): bool
    {
        $images = self::decode('images');

        $images['images_disabled'] ??= false;

        if (!App::plugins()->moduleExists('featuredMedia')) {
            $images['images_disabled'] = true;
        }

        return $images['images_disabled'] === false;
    }

    /**
     * @param   ArrayObject<string, mixed>  $attr   The attributes
     */
    public static function editorialBigImage(ArrayObject $attr): string
    {
        return '<?php echo ' . self::class . '::bigImageHelper(); ?>';
    }

    public static function bigImageHelper(): string
    {
        $images = self::decode('images');
        $source = $images['default_image_url'] ?? '';

        if (!empty($source)) {
            $url    = parse_url($source);
            $path   = $url['path'] ?? '';
            $source = pathinfo($path, PATHINFO_DIRNAME) . '/' . pathinfo($path, PATHINFO_FILENAME) . '.' . pathinfo($path, PATHINFO_EXTENSION);
        }

        return is_string($source) ? $source : '';
    }

    /**
     * @param   ArrayObject<string, mixed>  $attr   The attributes
     */
    public static function editorialBigImageAlt(ArrayObject $attr): string
    {
        return '<?= ' . self::class . '::bigImageAltHelper() ?>';
    }

    public static function bigImageAltHelper(): string
    {
        $images = self::decode('images');

        return $images['default_image_media_alt'] ?? '';
    }

    /**
     * @param   ArrayObject<string, mixed>  $attr   The attributes
     */
    public static function editorialSmallImage(ArrayObject $attr): string
    {
        return '<?= ' . self::class . '::smallImageHelper() ?>';
    }

    public static function smallImageHelper(): string
    {
        $images = self::decode('images');
        $source = $images['default_small_image_url'] ?? '';

        if (!empty($source)) {
            $url       = parse_url($source);
            $path      = $url['path'] ?? '';
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            $extension = strtolower($extension) === 'jpeg' ? 'jpg' : $extension;
            $source    = pathinfo($path, PATHINFO_DIRNAME) . '/' . pathinfo($path, PATHINFO_FILENAME) . '_m.' . $extension;
        }

        return is_string($source) ? $source : '';
    }

    /**
     * @param   ArrayObject<string, mixed>  $attr   The attributes
     */
    public static function editorialSmallImageAlt(ArrayObject $attr): string
    {
        return '<?= ' . self::class . '::smallImageAltHelper() ?>';
    }

    public static function smallImageAltHelper(): string
    {
        $images = self::decode('images');

        return $images['default_small_image_media_alt'] ?? '';
    }

    /**
     * @param   ArrayObject<string, mixed>  $attr   The attributes
     */
    public static function editorialUserColors(ArrayObject $attr): string
    {
        return '<?= ' . self::class . '::editorialUserColorsHelper() ?>';
    }

    public static function editorialUserColorsHelper(): string
    {
        $style = self::decode('style');
        $main_color = $style['main_color'] ?? '#f56a6a';

        return $main_color !== '#f56a6a' ?
            '<style type="text/css">' . "\n" .
            ':root {--main-color: ' . $main_color . '}' . "\n" .
            '</style>' . "\n" : '';
    }

    /**
     * @param   ArrayObject<string, mixed>  $attr   The attributes
     */
    public static function editorialSocialLinks(ArrayObject $attr): string
    {
        return '<?= ' . self::class . '::editorialSocialLinksHelper() ?>';
    }

    public static function editorialSocialLinksHelper(): string
    {
        # Social media links
        $res = '';

        $stickers = self::decode('stickers');
        $stickers = array_filter($stickers, self::cleanSocialLinks(...));

        $count = 0;
        foreach ($stickers as $sticker) {
            $res .= self::setSocialLink($count, ($count == count($stickers)), $sticker['label'], $sticker['url'], $sticker['image']);
            $count++;
        }

        return $res;
    }

    protected static function setSocialLink(int $position, bool $last, string $label, string $url, string $image): string
    {
        return
            '<li><a class="social-icon" title="' . $label . '" href="' . $url . '"><span class="sr-only">' . $label . '</span>' .
            '<i class="' . $image . '"></i>' .
            '</a></li>' . "\n";
    }

    protected static function cleanSocialLinks(mixed $style): bool
    {
        if (is_array($style)) {
            if (isset($style['label']) && isset($style['url']) && isset($style['image'])) {
                if ($style['label'] && $style['url'] && $style['image']) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return  array<string, mixed>
     */
    protected static function decode(string $setting): array
    {
        $res = App::blog()->settings()->get('themes')->get(App::blog()->settings()->get('system')->get('theme') . '_' . $setting);
        $res = $res ? (unserialize($res) ?: []) : [];

        if (!is_array($res)) {
            $res = [];
        }

        return $res;
    }

    /**
     * @param   ArrayObject<string, mixed>  $attr   The attributes
     */
    public static function templateBeforeBlock(string $block, ArrayObject $attr): string
    {
        if ($block === 'Entries' && isset($attr['featured_url']) && (bool) $attr['featured_url']) {
            $featured = self::decode('featured');

            return
            "<?php\n" .
            "if (!isset(\$params)) { \$params['post_type'] = ['post', 'page', 'related']; }\n" .
            "if (!isset(\$params['sql'])) { \$params['sql'] = ''; }\n" .
            "\$params['sql'] .= \"AND P.post_url = '" . urldecode($featured['featured_post_url'] ?? '') . "' \";\n" .
                "?>\n";
        } elseif ($block == 'Entries' && isset($attr['featured_url']) && $attr['featured_url'] == 0) {
            $featured = self::decode('featured');

            return
            "<?php\n" .
            "if (!isset(\$params)) { \$params = []; }\n" .
            "if (!isset(\$params['sql'])) { \$params['sql'] = ''; }\n" .
            "\$params['sql'] .= \"AND P.post_url != '" . urldecode($featured['featured_post_url'] ?? '') . "' \";\n" .
                "?>\n";
        }

        return '';
    }
}
