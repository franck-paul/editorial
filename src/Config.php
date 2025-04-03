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
use Dotclear\Core\Process;
use Dotclear\Helper\Html\Form\Button;
use Dotclear\Helper\Html\Form\Color;
use Dotclear\Helper\Html\Form\Div;
use Dotclear\Helper\Html\Form\Field;
use Dotclear\Helper\Html\Form\Fieldset;
use Dotclear\Helper\Html\Form\Form;
use Dotclear\Helper\Html\Form\Hidden;
use Dotclear\Helper\Html\Form\Image;
use Dotclear\Helper\Html\Form\Input;
use Dotclear\Helper\Html\Form\Label;
use Dotclear\Helper\Html\Form\Legend;
use Dotclear\Helper\Html\Form\Number;
use Dotclear\Helper\Html\Form\Note;
use Dotclear\Helper\Html\Form\Para;
use Dotclear\Helper\Html\Form\Submit;
use Dotclear\Helper\Html\Form\Table;
use Dotclear\Helper\Html\Form\Tbody;
use Dotclear\Helper\Html\Form\Td;
use Dotclear\Helper\Html\Form\Text;
use Dotclear\Helper\Html\Form\Th;
use Dotclear\Helper\Html\Form\Thead;
use Dotclear\Helper\Html\Form\Tr;
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

        //Presentation tab
        echo
        (new Div('presentation'))
            ->class('multi-part')
            ->title(__('Presentation'))
            ->items([
                (new Form('theme_config'))
                ->action(App::backend()->url()->get('admin.blog.theme', ['conf' => '1', 'conf_tab' => 'presentation']))
                ->method('post')
                ->fields([
                    (new Fieldset())->class('fieldset')->legend((new Legend(__('Blog\'s featured publication'))))->fields([
                        (new Para())->items([
                            (new Label(__('Entry URL:'), Label::INSIDE_LABEL_BEFORE))->for('featured_post_url')->class('classic')
                                ->class('classic'),
                            (new Input('featured_post_url'))
                                ->size(50)
                                ->maxlength(255)
                                ->value(App::backend()->featured['featured_post_url']),
                            (new Button('featured_post_url_selector', __('Choose an entry')))
                                ->class('button')
                                ->type('button')
                                ->id('featured_post_url_selector'),
                        ]),
                        (new Note())
                            ->class(['form-note', 'info'])
                            ->text(__('Leave this field empty to use the default presentation (latest post)')),

                    ]),
                    (new Fieldset())->class('fieldset')->legend((new Legend(__('Colors'))))->fields([
                        (new Para())->items([
                            (new Label(__('Links and buttons\' color:'), Label::INSIDE_LABEL_BEFORE))->for('main_color'),
                            (new Color('main_color'))
                                ->size(30)
                                ->maxlength(255)
                                ->value(App::backend()->style['main_color']),
                        ]),
                    ]),
                    (new Fieldset())->class('fieldset')->legend((new Legend(__('Placeholder images'))))->fields([
                        (new Div())
                            ->class(['box', 'theme'])->items([
                                (new Para())->items([
                                    (new Label(__('Big image'), Label::INSIDE_LABEL_BEFORE))->for('default_image_tb_url')
                                    ->class('classic'),
                                ]),
                                (new Para())->items([
                                    (new Image(App::backend()->images['default_image_tb_url'], 'default_image_tb_src'))
                                    ->alt(__('Thumbnail'))
                                    ->width(240)
                                    ->height(160)
                                    ->disabled(true),
                                ]),
                                (new Para())->items([
                                    (new Button('default_image_selector', __('Change')))
                                        ->type('button')
                                        ->id('default_image_selector'),
                                    (new Text('span', ' ')),
                                    (new Button('default_image_selector_reset', __('Reset')))
                                        ->class('delete')
                                        ->type('button')
                                        ->id('default_image_selector_reset'),
                                ]),
                                (new Hidden('default_image_url'))
                                    ->value(App::backend()->images['default_image_url']),
                                (new Hidden('default_image_tb_url'))
                                    ->value(App::backend()->images['default_image_tb_url']),
                            ]),
                        (new Div())
                            ->class(['box', 'theme'])->items([
                                (new Para())->items([
                                    (new Label(__('Small image'), Label::INSIDE_LABEL_BEFORE))->for('default_small_image_tb_url')
                                    ->class('classic'),
                                ]),
                                (new Para())->items([
                                    (new Image(App::backend()->images['default_small_image_tb_url'], 'default_small_image_tb_src'))
                                    ->alt(__('Thumbnail'))
                                    ->width(240)
                                    ->height(160)
                                    ->disabled(true),
                                ]),
                                (new Para())->items([
                                    (new Button('default_small_image_selector', __('Change')))
                                        ->type('button')
                                        ->id('default_small_image_selector'),
                                    (new Text('span', ' ')),
                                    (new Button('default_small_image_selector_reset', __('Reset')))
                                        ->class('delete')
                                        ->type('button')
                                        ->id('default_small_image_selector_reset'),
                                ]),
                                (new Hidden('default_small_image_url'))
                                    ->value(App::backend()->images['default_small_image_url']),
                                (new Hidden('default_small_image_tb_url'))
                                    ->value(App::backend()->images['default_small_image_tb_url']),
                            ]),
                    ]),
                    (new Para())->items([
                        (new Input('base_url'))
                            ->type('hidden')
                            ->value(App::blog()->url),
                        (new Input('theme-url'))
                            ->type('hidden')
                            ->value(My::fileURL('')),
                        (new Input('change-button-id'))
                            ->type('hidden')
                            ->value(''),

                    ]),
                    (new Para())->items([
                        (new Submit(['opts'], __('Save'))),
                        App::nonce()->formNonce(),

                    ]),
                ]),
            ])
        ->render();
        
        //Stickers tab
        echo
        (new Div('stickers'))
            ->class('multi-part')
            ->title(__('Stickers'))
            ->items([
                (new Form('stickers-form'))
                ->action(App::backend()->url()->get('admin.blog.theme', ['conf' => '1', 'conf_tab' => 'links']))
                ->method('post')
                ->fields([
                    (new Fieldset())->class('fieldset')->legend((new Legend(__('Social links'))))->fields([
                        
                    ]),
                    (new Para())->items([
                        (new Submit(['opts'], __('Save'))),
                            App::nonce()->formNonce(),
                    ]),
                ]),
            ])
        ->render();

        
        /*
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
            '<td class="handle">' . form::hidden(['order[' . $i . ']'], [
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

        echo '</div>'; // Close tab*/

        Page::helpBlock('editorial');
    }
}
