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

use Dotclear\App;
use Dotclear\Core\Backend\Notices;
use Dotclear\Core\Backend\Page;
use Dotclear\Core\Backend\ThemeConfig;
use Dotclear\Core\Process;
use Dotclear\Helper\File\Files;
use Dotclear\Helper\File\Path;
use Dotclear\Helper\Html\Form\Checkbox;
use Dotclear\Helper\Html\Form\Color;
use Dotclear\Helper\Html\Form\Div;
use Dotclear\Helper\Html\Form\Form;
use Dotclear\Helper\Html\Form\Label;
use Dotclear\Helper\Html\Form\Note;
use Dotclear\Helper\Html\Form\Para;
use Dotclear\Helper\Html\Form\Single;
use Dotclear\Helper\Html\Form\Submit;
use Dotclear\Helper\Html\Form\Text;
use Dotclear\Helper\Html\Form\Textarea;
use Dotclear\Helper\Html\Html;
use Exception;

class Config extends Process
{
    public static function init(): bool
    {
        // limit to backend permissions
        if (!self::status(My::checkContext(My::CONFIG))) {
            return false;
        }

        My::l10n('admin');

        App::backend()->standalone_config = (bool) App::themes()->moduleInfo(App::blog()->settings->system->theme, 'standalone_config');

        // Load contextual help
        App::themes()->loadModuleL10Nresources(My::id(), App::lang()->getLang());

        # default or user defined images settings
        $images = App::blog()->settings->themes->get(App::blog()->settings->system->theme . '_images');
        $images = $images ? (unserialize($images) ?: []) : [];

        if (!is_array($images)) {
            $images = [];
        }
        //Big image
        if (!isset($images['default_image_url'])) {
            $images['default_image_url'] = My::fileURL('/images/image-placeholder-1920x1080.jpg');
        }
        if (!isset($images['default_image_tb_url'])) {
            $images['default_image_tb_url'] = My::fileURL('/images/.image-placeholder-1920x1080_s.jpg');
        }

        //Small image
        if (!isset($images['default_small_image_url'])) {
            $images['default_small_image_url'] = My::fileURL('/images/image-placeholder-600x338.jpg');
        }
        if (!isset($images['default_small_image_tb_url'])) {
            $images['default_small_image_tb_url'] = My::fileURL('/images/.image-placeholder-600x338_s.jpg');
        }

        $style = App::blog()->settings->themes->get(App::blog()->settings->system->theme . '_style');
        $style = $style ? (unserialize($style) ?: []) : [];

        if (!is_array($style)) {
            $style = [];
        }

        if (!isset($style['main_color'])) {
            $style['main_color'] = '#f56a6a';
        }

        $featured = App::blog()->settings->themes->get(App::blog()->settings->system->theme . '_featured');
        $featured = $featured ? (unserialize($featured) ?: []) : [];

        if (!is_array($featured)) {
            $featured = [];
        }
        if (!isset($featured['featured_post_url'])) {
            $featured['featured_post_url'] = '';
        }

        $stickers = App::blog()->settings->themes->get(App::blog()->settings->system->theme . '_stickers');
        $stickers = $stickers ? (unserialize($stickers) ?: []) : [];

        $stickers_full = [];
        // Get all sticker images already used
        if (is_array($stickers)) {
            foreach ($stickers as $v) {
                $stickers_full[] = $v['image'];
            }
        }
        // Get social media images
        $stickers_images = ['fab fa-diaspora', 'fas fa-rss', 'fab fa-linkedin-in', 'fab fa-gitlab', 'fab fa-github', 'fab fa-twitter', 'fab fa-facebook-f',
            'fab fa-instagram', 'fab fa-mastodon', 'fab fa-pinterest', 'fab fa-snapchat', 'fab fa-soundcloud', 'fab fa-youtube', ];
        if (is_array($stickers_images)) {
            foreach ($stickers_images as $v) {
                if (!in_array($v, $stickers_full)) {
                    // image not already used
                    $stickers[] = [
                        'label' => null,
                        'url'   => null,
                        'image' => $v, ];
                }
            }
        }

        App::backend()->featured = $featured;
        App::backend()->style    = $style;
        App::backend()->images   = $images;
        App::backend()->stickers = $stickers;

        App::backend()->conf_tab = $_POST['conf_tab'] ?? 'presentation';

        return self::status();
    }

    /**
     * Processes the request(s).
     */
    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        if (!empty($_POST)) {
            try {
                // HTML
                if (App::backend()->conf_tab === 'presentation') {
                    $featured                      = [];
                    $style                         = [];
                    $featured['featured_post_url'] = $_POST['featured_post_url'];
                    $style['main_color']           = $_POST['main_color'];

                    //BIG IMAGE
                    # default image setting
                    if (!empty($_POST['default_image_url'])) {
                        $images['default_image_url'] = $_POST['default_image_url'];
                    } else {
                        $images['default_image_url'] = My::fileURL('/images/image-placeholder-1920x1080.jpg');
                    }
                    # default image thumbnail settings
                    if (!empty($_POST['default_image_tb_url'])) {
                        $images['default_image_tb_url'] = $_POST['default_image_tb_url'];
                    } else {
                        $images['default_image_tb_url'] = My::fileURL('.image-placeholder-1920x1080_s.jpg') . '/';
                    }

                    //SMALL IMAGE
                    # default small image setting
                    if (!empty($_POST['default_small_image_url'])) {
                        $images['default_small_image_url'] = $_POST['default_small_image_url'];
                    } else {
                        $images['default_small_image_url'] = My::fileURL('/images/image-placeholder-600x338.jpg');
                    }
                    # default small image settings
                    if (!empty($_POST['default_small_image_tb_url'])) {
                        $images['default_small_image_tb_url'] = $_POST['default_small_image_tb_url'];
                    } else {
                        $images['default_small_image_tb_url'] = My::fileURL('/images/.image-placeholder-600x338_s.jpg') . '/';
                    }

                    App::backend()->featured = $featured;
                    App::backend()->style    = $style;
                    App::backend()->images   = $images;
                }

                if (App::backend()->conf_tab === 'links') {
                    $stickers = [];
                    for ($i = 0; $i < count($_POST['sticker_image']); $i++) {
                        $stickers[] = [
                            'label' => $_POST['sticker_label'][$i],
                            'url'   => $_POST['sticker_url'][$i],
                            'image' => $_POST['sticker_image'][$i],
                        ];
                    }

                    $order = [];
                    if (empty($_POST['ds_order']) && !empty($_POST['order'])) {
                        $order = $_POST['order'];
                        asort($order);
                        $order = array_keys($order);
                    }
                    if (!empty($order)) {
                        $new_stickers = [];
                        foreach ($order as $i => $k) {
                            $new_stickers[] = [
                                'label' => $stickers[$k]['label'],
                                'url'   => $stickers[$k]['url'],
                                'image' => $stickers[$k]['image'],
                            ];
                        }
                        $stickers = $new_stickers;
                    }
                    App::backend()->stickers = $stickers;
                }
                App::blog()->settings->themes->put(App::blog()->settings->system->theme . '_featured', serialize(App::backend()->featured));
                App::blog()->settings->themes->put(App::blog()->settings->system->theme . '_style', serialize(App::backend()->style));
                App::blog()->settings->themes->put(App::blog()->settings->system->theme . '_images', serialize(App::backend()->images));
                App::blog()->settings->themes->put(App::blog()->settings->system->theme . '_stickers', serialize(App::backend()->stickers));

                // Blog refresh
                App::blog()->triggerBlog();

                // Template cache reset
                App::cache()->emptyTemplatesCache();

                Notices::message(__('Theme configuration upgraded.'), true, true);
            } catch (Exception $e) {
                App::error()->add($e->getMessage());
            }
        }

        return true;
    }

    /**
     * Renders the page.
     */
    public static function render(): void
    {
        if (!self::status()) {
            return;
        }

        echo
        (new Div('presentation'))
            ->class('multi-part')
            ->title(__('Presentation'))
            ->items([
                (new Form('presentation-form'))
                ->action(App::backend()->url()->get('admin.blog.theme', ['conf' => '1']))
                ->method('post')
                ->fields([
                    
                    (new Para())->items([
                        (new Submit(['opts'], __('Save')))
                            ->accesskey('s'),
                    ]),
                ]),
            ])
        ->render();

        echo
        (new Div('stickers'))
            ->class('multi-part')
            ->title(__('Stickers'))
            ->items([
                (new Form('stickers-form'))
                ->action(App::backend()->url()->get('admin.blog.theme', ['conf' => '1']))
                ->method('post')
                ->fields([
                    
                    (new Para())->items([
                        (new Submit(['opts'], __('Save')))
                            ->accesskey('s'),
                    ]),
                ]),
            ])
        ->render();

        /*if (!App::backend()->standalone_config) {
            echo '</form>';
        }
        echo '<div class="multi-part" id="themes-list' . (App::backend()->conf_tab === 'presentation' ? '' : '-presentation') . '" title="' . __('Presentation') . '">';

        echo '<form id="theme_config" action="' . App::backend()->url()->get('admin.blog.theme', ['conf' => '1']) .
            '" method="post" enctype="multipart/form-data">';

        echo '<div class="fieldset">';

        echo '<h3>' . __('Blog\'s featured publication') . '</h3>';

        echo '<p><label for="featured_post_url" class="classic">' . __('Entry URL:') . '</label> ' .
            form::field('featured_post_url', 30, 255, App::backend()->featured['featured_post_url']) .
            ' <button type="button" id="featured_post_url_selector">' . __('Choose an entry') . '</button>' .
            '</p>' .
            '<p class="form-note info maximal">' . __('Leave this field empty to use the default presentation (latest post)') . '</p> ';
        echo '</div>';
        echo '<div class="fieldset">';
        echo '<h3>' . __('Colors') . '</h3>';

        echo '<p class="field"><label for="main_color">' . __('Links and buttons\' color:') . '</label> ' .
            form::color('main_color', 30, 255, App::backend()->style['main_color']) . '</p>' ;
        echo '</div>';

        echo '<div class="fieldset">';

        echo '<h4 class="pretty-title">' . __('Placeholder images') . '</h4>';

        echo '<div class="box theme">';

        echo '<p>' . __('Big image') . '</p>';

        echo '<p> ' .
        '<img id="default_image_tb_src" alt="' . __('Thumbnail') . '" src="' . App::backend()->images['default_image_url'] . '" width="240" height="160">' .
        '</p>';

        echo '<p class="form-buttons"><button type="button" id="default_image_selector">' . __('Change') . '</button>' .
        '<button class="delete" type="button" id="default_image_selector_reset">' . __('Reset') . '</button>' .
        '</p>' ;

        echo '<p class="sr-only">' . form::field('default_image_url', 30, 255, App::backend()->images['default_image_url']) . '</p>';
        echo '<p class="sr-only">' . form::field('default_image_tb_url', 30, 255, App::backend()->images['default_image_tb_url']) . '</p>';

        echo '</div>';

        echo '<div class="box theme">';

        echo '<p>' . __('Small image') . '</p>';

        echo '<p> ' .
        '<img id="default_small_image_tb_src" alt="' . __('Thumbnail') . '" src="' . App::backend()->images['default_small_image_url'] . '" width="240" height="160">' .
        '</p>';

        echo '<p class="form-buttons"><button type="button" id="default_small_image_selector">' . __('Change') . '</button>' .
        '<button class="delete" type="button" id="default_small_image_selector_reset">' . __('Reset') . '</button>' .
        '</p>' ;

        echo '<p class="sr-only">' . form::field('default_small_image_url', 30, 255, App::backend()->images['default_small_image_url']) . '</p>';
        echo '<p class="sr-only">' . form::field('default_small_image_tb_url', 30, 255, App::backend()->images['default_small_image_tb_url']) . '</p>';

        echo '</div>';

        echo '</div>'; // Close fieldset

        echo '<p><input type="hidden" name="conf_tab" value="presentation"></p>';
        echo '<p class="clear"><input type="submit" value="' . __('Save') . '">' . App::nonce()->getFormNonce() . '</p>';
        echo form::hidden(['base_url'], App::blog()->url);

        echo form::hidden(['theme-url'], My::fileURL(''));

        echo form::hidden(['change-button-id'], '');

        echo '</form>';

        echo '</div>'; // Close tab

        echo '<div class="multi-part" id="themes-list' . (App::backend()->conf_tab === 'links' ? '' : '-links') . '" title="' . __('Stickers') . '">';
        echo '<form id="theme_config" action="' . App::backend()->url()->get('admin.blog.theme', ['conf' => '1']) .
            '" method="post" enctype="multipart/form-data">';
        echo '<div class="fieldset">';
        echo '<h3>' . __('Social links') . '</h3>';

        echo
        '<div class="table-outer">' .
        '<table class="dragable">' . '<caption class="sr-only">' . __('Social links (header)') . '</caption>' .
        '<thead>' .
        '<tr>' .
        '<th scope="col">' . '</th>' .
        '<th scope="col">' . __('Image') . '</th>' .
        '<th scope="col">' . __('Label') . '</th>' .
        '<th scope="col">' . __('URL') . '</th>' .
            '</tr>' .
            '</thead>' .
            '<tbody id="stickerslist">';
        $count = 0;
        foreach (App::backend()->stickers as $i => $v) {
            $count++;
            $v['service'] = str_replace('-link.png', '', $v['image']);
            echo
            '<tr class="line" id="l_' . $i . '">' .
            '<td class="handle">' . form::number(['order[' . $i . ']'], [
                'min'     => 0,
                'max'     => count(App::backend()->stickers),
                'default' => $count,
                'class'   => 'position',
            ]) .
            form::hidden(['dynorder[]', 'dynorder-' . $i], $i) . '</td>' .
            '<td class="linkimg">' . form::hidden(['sticker_image[]'], $v['image']) . '<i class="' . $v['image'] . '" title="' . $v['label'] . '"></i> ' . '</td>' .
            '<td scope="row">' . form::field(['sticker_label[]', 'dsl-' . $i], 20, 255, $v['label']) . '</td>' .
            '<td>' . form::field(['sticker_url[]', 'dsu-' . $i], 40, 255, $v['url']) . '</td>' .
                '</tr>';
        }
        echo
            '</tbody>' .
            '</table></div>';
        echo '</div>';

        echo '<p><input type="hidden" name="conf_tab" value="links"></p>';
        echo '<p class="clear">' . form::hidden('ds_order', '') . '<input type="submit" value="' . __('Save') . '">' . App::nonce()->getFormNonce() . '</p>';
        echo '</form>';

        echo '</div>'; // Close tab

        Page::helpBlock('editorial');

        // Legacy mode
        if (!App::backend()->standalone_config) {
            echo '<form style="display:none">';
        }*/
    }
}
