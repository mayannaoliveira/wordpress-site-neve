<?php if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://agegate.io
 * @since      1.0.0
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/public/partials
 */
?>
<div class="wrap">
    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>

    <?php //include AGE_GATE_PATH . 'admin/partials/parts/tabs.php'; 
    ?>

    <form class="custom-form-fields" action="admin-post.php" method="post">
        <input type="hidden" name="action" value="age_gate_appearance">
        <?php wp_nonce_field('age_gate_update_appearance', 'nonce'); ?>

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="wp_age_gate_logo"><?php _e('Logo', 'age-gate'); ?></label></th>
                    <td>
                        <div class="image-preview-wrapper" data-option="logo">
                            <?php if ($src = wp_get_attachment_url($values['logo'])) : ?>
                                <img class="image-preview" src="<?php echo $src; ?>">
                            <?php endif; ?>
                        </div>

                        <?php echo form_submit(
                            [
                                'type' => 'button'
                            ],
                            __('Select image', 'age-gate'),
                            [
                                'data-option' => 'logo',
                                'class' => 'button upload_image_button'
                            ]
                        ); ?>

                        <?php
                        if ($values['logo']) {
                            echo form_submit(
                                [
                                    'type' => 'button'
                                ],
                                __('Remove image', 'age-gate'),
                                [
                                    'data-option' => 'logo',
                                    'class' => 'button remove-image'
                                ]
                            );
                        }

                        ?>
                        <?php echo form_input(
                            [
                                'name' => 'ag_settings[logo]',
                                'type' => 'hidden'
                            ], 
                            $values['logo'], 
                            [
                                'class' => 'image_attachment_id',
                                'data-option' => 'logo'
                            ]
                        );
                        ?>

                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="wp_age_gate_background_colour"><?php _e('Background colour', 'age-gate'); ?></label></th>
                    <td>
                        <?php echo form_input(
                            [
                                'name' => 'ag_settings[background_colour]',
                                'id' => 'wp_age_gate_background_colour'
                            ],
                            $values['background_colour'],
                            [
                                'class' => 'colour-picker'
                            ]
                        ); ?>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="wp_age_gate_background_opacity"><?php _e('Background colour opacity', 'age-gate'); ?></label>
                    </th>
                    <td>
                        <div class="slide-wrapper">
                            <div class="label">0%</div>
                            <div class="range-slider">
                                <?php echo form_input(
                                    [
                                        'type' => 'range',
                                        'name' => 'ag_settings[background_opacity]',
                                        'id' => 'wp_age_gate_background_opacity'
                                    ],
                                    $values['background_opacity'],
                                    [
                                        'class' => 'slider age-gate-range-slider',
                                        'min' => 0,
                                        'max' => 1,
                                        'step' => "0.1"
                                    ]
                                ); ?>
                            </div>
                            <div class="label">100%</div>
                        </div>
                        <p class="note"><?php echo __('Please see the notes section in the docs:', 'age-gate'); ?> <a href="https://agegate.io/docs/cms-settings/appearance#notes" title="<?php echo __('Documentation', 'age-gate'); ?>"><?php echo __('Documentation', 'age-gate'); ?> <span class="dashicons dashicons-external"></span></a></p>

                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="wp_age_gate_background_image"><?php _e('Background image', 'age-gate'); ?></label></th>
                    <td>
                        <?php
                        $src = wp_get_attachment_url($values['background_image']);
                        ?>

                        <div class="image-preview-wrapper" data-option="background_image">
                            <?php if ($src = wp_get_attachment_url($values['background_image'])) : ?>
                                <img class="image-preview" src="<?php echo $src; ?>">
                            <?php endif; ?>
                        </div>

                        <?php echo form_submit(
                            [
                                'type' => 'button'
                            ],
                            __('Select image', 'age-gate'),
                            [
                                'data-option' => 'background_image',
                                'class' => 'button upload_image_button'
                            ]
                        ); ?>
                        <?php
                        if ($values['background_image']) {
                            echo form_submit(
                                [
                                    'type' => 'button'
                                ],
                                __('Remove image', 'age-gate'),
                                [
                                    'data-option' => 'background_image',
                                    'class' => 'button remove-image'
                                ]
                            );
                        }

                        ?>
                        <?php echo form_input(
                            [
                                'name' => 'ag_settings[background_image]',
                                'type' => 'hidden'
                            ],
                            $values['background_image'],
                            [
                                'class' => 'image_attachment_id',
                                'data-option' => 'background_image'
                            ]
                        );
                        ?>


                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e('Background position', 'age-gate'); ?>
                    </th>
                    <td>
                        <?php echo form_dropdown(
                            [
                                'name' => 'ag_settings[background_pos_y]',
                                'id' => 'wp_age_gate_background_pos_y'
                            ],
                            [
                                'center' => __("Centre", 'age-gate'),
                                'top' => __("Top", 'age-gate'),
                                'bottom' => __("Bottom", 'age-gate'),
                            ],
                            $values['background_pos_y']
                        ); ?>
                        <?php echo form_dropdown(
                            [
                                'name' => 'ag_settings[background_pos_x]',
                                'id' => 'wp_age_gate_background_pos_x'
                            ],
                            [
                                'center' => __("Centre", 'age-gate'),
                                'left' => __("Left", 'age-gate'),
                                'right' => __("Right", 'age-gate'),
                            ],
                            $values['background_pos_x']
                        ); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="wp_age_gate_background_image_opacity"><?php _e('Background image opacity', 'age-gate'); ?></label>
                    </th>
                    <td>
                        <div class="slide-wrapper">
                            <div class="label">0%</div>
                            <div class="range-slider">
                                <?php echo form_input(
                                    [
                                        'type' => 'range',
                                        'name' => 'ag_settings[background_image_opacity]',
                                        'id' => 'wp_age_gate_background_image_opacity'
                                    ],
                                    $values['background_image_opacity'],
                                    [
                                        'class' => 'slider age-gate-range-slider',
                                        'min' => 0,
                                        'max' => 1,
                                        'step' => "0.1"
                                    ]
                                ); ?>
                            </div>
                            <div class="label">100%</div>
                        </div>
                        <!-- <input type="range" min="0" max="1" step="0.1" id="wp_age_gate_background_opacity" /> -->
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="wp_age_gate_foreground_colour"><?php _e('Foreground colour', 'age-gate'); ?></label></th>
                    <td>
                        <?php echo form_input(
                            [
                                'name' => 'ag_settings[foreground_colour]',
                                'id' => 'wp_age_gate_foreground_colour'
                            ],
                            $values['foreground_colour'],
                            [
                                'class' => 'colour-picker'
                            ]
                        ); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="wp_age_gate_foreground_opacity"><?php _e('Foreground colour opacity', 'age-gate'); ?></label>
                    </th>
                    <td>
                        <div class="slide-wrapper">
                            <div class="label">0%</div>
                            <div class="range-slider">
                                <?php echo form_input(
                                    [
                                        'type' => 'range',
                                        'name' => 'ag_settings[foreground_opacity]',
                                        'id' => 'wp_age_gate_foreground_opacity'
                                    ],
                                    $values['foreground_opacity'],
                                    [
                                        'class' => 'slider age-gate-range-slider',
                                        'min' => 0,
                                        'max' => 1,
                                        'step' => "0.1"
                                    ]
                                ); ?>
                            </div>
                            <div class="label">100%</div>
                        </div>
                        <!-- <input type="range" min="0" max="1" step="0.1" id="wp_age_gate_background_opacity" /> -->
                    </td>
                </tr>
                <tr>

                    <th scope="row"><label for="wp_age_gate_text_colour"><?php _e('Text colour', 'age-gate'); ?></label></th>
                    <td>

                        <?php echo form_input(
                            [
                                'name' => 'ag_settings[text_colour]',
                                'id' => 'wp_age_gate_text_colour'
                            ],
                            $values['text_colour'],
                            [
                                'class' => 'colour-picker'
                            ]
                        ); ?>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="wp_age_gate_styling"><?php _e('Layout', 'age-gate'); ?></label></th>
                    <td>
                        <label class="ag-switch">
                            <?php echo form_checkbox(
                                [
                                    'name' => "ag_settings[styling]",
                                    'id' => "wp_age_gate_styling"
                                ],
                                1, // value
                                $values['styling'] // checked
                            ); ?>
                            <span class="ag-switch__slider"></span>
                        </label> <label for="wp_age_gate_styling"><?php _e('Use plugin style on the front end', 'age-gate'); ?></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="wp_age_gate_transition"><?php _e('Transition', 'age-gate'); ?></label></th>
                    <td>
                        <?php if (!$this->settings['advanced']['use_js']) : ?>
                            <?php _e('Transitions are only available in JavaScript mode', 'age-gate'); ?>
                        <?php else : ?>
                            <label>

                                <?php echo form_dropdown(
                                    [
                                        'name' => "ag_settings[transition]",
                                        'id' => "wp_age_gate_transition"
                                    ],
                                    [
                                        '' => __('No transition', 'age-gate'),
                                        'up' => __('Slide up', 'age-gate'),
                                        'down' => __('Slide down', 'age-gate'),
                                        'left' => __('Slide left', 'age-gate'),
                                        'right' => __('Slide right', 'age-gate'),
                                        'fade' => __('Fade out', 'age-gate')
                                    ], // value
                                    $values['transition'], // checked
                                    [
                                        'class' => 'regular-text ltr'
                                    ]
                                ); ?><br /><p><small><?php _e('In JavaScript transition Age Gate out', 'age-gate'); ?></small></p>
                            </label>
                        <?php endif; ?>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="wp_age_gate_device_width"><?php _e('Viewport meta tag', 'age-gate'); ?></label>
                    </th>

                    <td>
                        <label class="ag-switch">
                            <?php echo form_checkbox(
                                [
                                    'name' => "ag_settings[device_width]",
                                    'id' => "wp_age_gate_device_width"
                                ],
                                1, // value
                                $values['device_width'] // checked
                            ); ?>
                            <span class="ag-switch__slider"></span>
                        </label>
                        <p><small><?php _e('Add viewport meta to Age Gate page', 'age-gate'); ?><br><i>(width=device-width, minimum-scale=1, maximum-scale=1)</i></small></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="wp_age_gate_auto_tab"><?php _e('Auto tab inputs', 'age-gate'); ?></label>
                    </th>
                    <td>
                        <label class="ag-switch">
                            <?php echo form_checkbox(
                                array(
                                    'name' => "ag_settings[auto_tab]",
                                    'id' => "wp_age_gate_auto_tab"
                                ),
                                1, // value
                                $values['auto_tab'] // checked
                            ); ?>
                            <span class="ag-switch__slider"></span>
                        </label> <label for="wp_age_gate_auto_tab"><?php _e('Input fields will automatically tab to the next once filled', 'age-gate'); ?></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="wp_age_gate_switch_title"><?php _e('Change the page title', 'age-gate'); ?></label>
                    </th>
                    <td>
                        <label class="ag-switch">
                            <?php echo form_checkbox(
                                array(
                                    'name' => "ag_settings[switch_title]",
                                    'id' => "wp_age_gate_switch_title"
                                ),
                                1, // value
                                $values['switch_title'] // checked
                            ); ?>
                            <span class="ag-switch__slider"></span>
                        </label> <label for="wp_age_gate_switch_title"><?php _e('Change the page title when Age Gate is shown', 'age-gate'); ?></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="wp_age_gate_custom_title"><?php _e('Custom page title', 'age-gate'); ?></label>
                    </th>
                    <td>

                        <?php $this->render_language_input('custom_title', $values); ?>
                        <p class="note"><?php _e('Customise the title displayed when Age Gate is shown', 'age-gate'); ?></p>

                    </td>
                </tr>

            </tbody>
        </table>
        <?php if (!current_theme_supports('title-tag') && !$this->settings['advanced']['use_js']) : ?>
            <p><?php _e('Your theme does not have the recommended <code>title-tag</code> support', 'age-gate'); ?> <a href="https://codex.wordpress.org/Title_Tag"><span class="dashicons dashicons-external"></span></a></p>
            <p><?php _e('If you are unable to add support, add your preference for the title here', 'age-gate'); ?></p>
            <table class="form-table">
                <tr>
                    <th>
                        <?php _e('Title format', 'age-gate'); ?>
                    </th>
                    <td>
                        <label class="ag-switch">
                            <?php echo form_radio(
                                [
                                    'name' => "ag_settings[title_format]",
                                    'id' => "wp_age_gate_title_format_a"
                                ],
                                'name-page', // value
                                ('name-page' === $values['title_format']) // checked
                            ); ?>
                            <span class="ag-switch__slider"></span>
                        </label> <label for="wp_age_gate_title_format_a"><?php bloginfo('name'); ?> <?php echo $values['title_separator']; ?> <?php _e('Page title', 'age-gate'); ?></label><br /><br />
                        <label class="ag-switch">
                            <?php echo form_radio(
                                [
                                    'name' => "ag_settings[title_format]",
                                    'id' => "wp_age_gate_title_format_b"
                                ],
                                'page-name', // value
                                ('page-name' === $values['title_format']) // checked
                            ); ?>
                            <span class="ag-switch__slider"></span>
                        </label> <label for="wp_age_gate_title_format_b"><?php _e('Page title', 'age-gate'); ?> <?php echo $values['title_separator']; ?> <?php bloginfo('name'); ?></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="wp_age_gate_custom_title_separator"><?php _e('Separator', 'age-gate'); ?></label>
                    </th>
                    <td>
                        <?php echo form_input(
                            [
                                'name' => 'ag_settings[title_separator]',
                                'type' => 'text',
                                'id' => 'wp_age_gate_custom_title_separator'
                            ],
                            $values['title_separator'], 
                            [
                                'class' => 'small-text ltr'
                            ]
                        );
                        ?>
                    </td>
                </tr>
            </table>
        <?php else : ?>
            <input type="hidden" name="ag_settings[title_format]" value="<?php echo $values['title_format'] ?>" />
            <input type="hidden" name="ag_settings[title_separator]" value="<?php echo $values['title_separator'] ?>" />
        <?php endif; ?>
        <?php submit_button(); ?>
    </form>
</div>
