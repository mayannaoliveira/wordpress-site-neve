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
        <input type="hidden" name="action" value="age_gate_restriction">
        <?php wp_nonce_field('age_gate_update_restrictions', 'nonce'); ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="wp_age_gate_min_age"><?php _e("Default age", 'age-gate'); ?></label></th>
                    <td>
                        <?php /*
            <?php echo form_input(array(
              'name' => 'ag_settings[min_age]',
              'type' => 'number',
              'id' => 'wp_age_gate_min_age'
            ), $values['min_age'], array('class' => 'small-text ltr', 'required' => 'required'));
            ?> <?php _e("years or older to view content", 'age-gate'); ?>
            */ ?>
                        <?php $this->render_language_input('min_age', $values); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="wp_age_gate_restriction_type"><?php _e("Restrict", 'age-gate'); ?></label></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><?php _e("Select", 'age-gate'); ?></legend>
                            <label class="ag-switch">
                                <?php echo form_radio(
                                    [
                                        'name' => 'ag_settings[restriction_type]',
                                        'id' => 'wp_age_gate_restriction_type_all'
                                    ],
                                    'all',
                                    $values['restriction_type'] === 'all'
                                ); ?><span class="ag-switch__slider"></span>
                            </label> <label for="wp_age_gate_restriction_type_all"><?php _e("All content", 'age-gate'); ?></label><br>
                            <label class="ag-switch">
                                <?php echo form_radio(
                                    [
                                        'name' => 'ag_settings[restriction_type]',
                                        'id' => 'wp_age_gate_restriction_type_selected'
                                    ],
                                    'selected',
                                    $values['restriction_type'] === 'selected'
                                ); ?>
                                <span class="ag-switch__slider"></span>
                            </label> <label for="wp_age_gate_restriction_type_selected"><?php _e("Selected Content", 'age-gate'); ?></label><br>
                        </fieldset>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="wp_age_gate_multi_age"><?php _e("Varied ages", 'age-gate'); ?></label></th>
                    <td>
                        <?php if (!$this->settings['advanced']['anonymous_age_gate']) : ?>
                            <label class="ag-switch">
                                <?php echo form_checkbox(
                                    [
                                        'name' => "ag_settings[multi_age]",
                                        'id' => "wp_age_gate_multi_age"
                                    ],
                                    1, // value
                                    $values['multi_age'] // checked
                                ); ?>
                                <span class="ag-switch__slider"></span>
                            </label> <label for="wp_age_gate_multi_age"><?php _e("Ability to add a custom age on a per page level ", 'age-gate'); ?></label>
                        <?php else : ?>
                            <p><?php _e('This setting is unavailable with "Anonymous Age Gate" selected in the advanced tab', 'age-gate'); ?></p>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e("Restrict registration", 'age-gate'); ?></th>
                    <td>
                        <?php $addons = [];
                        $addons = apply_filters('age_gate_addons', $addons);
                        if (isset($addons['age-gate-user-registration'])) :
                            $link = add_query_arg(array(
                                'page' => 'age-gate-addons',
                                'addon' => 'age-gate-user-registration',
                            ));
                        ?>
                            <a href="<?php echo $link; ?>"><?php _e('Visit the addon page for these settings', 'age-gate'); ?></a>
                        <?php else : ?>
                            <p><?php _e('This option is no longer available as default.', 'age-gate'); ?></p>
                            <a href="<?php echo admin_url('/plugin-install.php?s=Age+Gate+User+Registration&tab=search&type=term'); ?>"><?php _e('Visit the addon page for these settings', 'age-gate'); ?></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="wp_age_gate_input_type"><?php _e("Validate age using", 'age-gate'); ?></label></th>
                    <td>
                        <?php echo form_dropdown(
                            array(
                                'name' => 'ag_settings[input_type]',
                                'id' => 'wp_age_gate_input_type'
                            ),
                            array(
                                'inputs' => __("Input fields", 'age-gate'),
                                'selects' => __("Dropdown boxes", 'age-gate'),
                                'buttons' => __("Yes/No", 'age-gate'),
                            ),
                            $values['input_type']
                        ); ?>
                    </td>
                </tr>
                <tr class="ag-option--button-order" style="display:<?php echo ($values['input_type'] === 'buttons') ? 'table-row' : 'none'; ?>">
                    <th scope="row"><label for="wp_age_gate_button_order"><?php _e("Button order", 'age-gate'); ?></label></th>
                    <td>
                        <?php echo form_dropdown(
                            array(
                                'name' => 'ag_settings[button_order]',
                                'id' => 'wp_age_gate_button_order'
                            ),
                            array(
                                'yes-no' => __("Yes then No", 'age-gate'),
                                'no-yes' => __("No then Yes", 'age-gate')
                            ),
                            $values['button_order']
                        ); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="wp_age_gate_remember"><?php _e("Inherit restrictions", 'age-gate'); ?></label></th>
                    <td>
                        <label class="ag-switch">
                            <?php echo form_checkbox(
                                [
                                    'name' => "ag_settings[inherit_category]",
                                    'id' => "wp_age_gate_inherit_category"
                                ],
                                1, // value
                                $values['inherit_category'] // checked
                            ); ?>
                            <span class="ag-switch__slider"></span>
                        </label> <label for="wp_age_gate_inherit_category"><?php _e("Posts will inherit their taxonomies restriction settings", 'age-gate'); ?></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="wp_age_gate_remember"><?php _e("Remember", 'age-gate'); ?></label></th>
                    <td>
                        <label class="ag-switch">
                            <?php echo form_checkbox(
                                array(
                                    'name' => "ag_settings[remember]",
                                    'id' => "wp_age_gate_remember"
                                ),
                                1, // value
                                $values['remember'] // checked
                            ); ?>
                            <span class="ag-switch__slider"></span>
                        </label> <label for="wp_age_gate_remember"><?php _e("Enable \"remember me\" checkbox", 'age-gate'); ?></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="wp_age_gate_remember_days"><?php _e("Remember length", 'age-gate'); ?></label></th>
                    <td>
                        <?php echo form_input(
                            array(
                                'name' => 'ag_settings[remember_days]',
                                'type' => 'number',
                                'id' => 'wp_age_gate_remember_days'
                            ),
                            $values['remember_days'],
                            array('class' => 'small-text ltr')
                        ); ?>
                        <?php $options = array(
                            'days'         => __('Days', 'age-gate'),
                            'hours'        => __('Hours', 'age-gate'),
                            'minutes'      => __('Minutes', 'age-gate')
                        );

                        echo form_dropdown('ag_settings[remember_timescale]', $options, $values['remember_timescale']);

                        ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="wp_age_gate_remember_auto_check"><?php _e("Auto check remember me", 'age-gate'); ?></label></th>
                    <td>
                        <label class="ag-switch">
                            <?php echo form_checkbox(
                                array(
                                    'name' => "ag_settings[remember_auto_check]",
                                    'id' => "wp_age_gate_remember_auto_check"
                                ),
                                1, // value
                                $values['remember_auto_check'] // checked
                            ); ?>
                            <span class="ag-switch__slider"></span>
                    </label> <label for="wp_age_gate_remember_auto_check"><?php _e("\"Remember me\" will be checked by default", 'age-gate'); ?></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="wp_age_gate_date_format"><?php _e("Date format", 'age-gate'); ?></label></th>
                    <td>


                        <?php $this->render_language_input('date_format', $values, 'settings', 'select'); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="wp_age_gate_ignore_logged"><?php _e("Ignore logged in", 'age-gate'); ?></label></th>
                    <td>
                        <label class="ag-switch">
                            <?php echo form_checkbox(
                                array(
                                    'name' => "ag_settings[ignore_logged]",
                                    'id' => "wp_age_gate_ignore_logged"
                                ),
                                1, // value
                                $values['ignore_logged'] // checked
                            ); ?>
                            <span class="ag-switch__slider"></span>
                        </label> <label for="wp_age_gate_ignore_logged"><?php _e("Logged in users will not need to provide their age", 'age-gate'); ?></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="wp_age_gate_rechallenge"><?php _e("Rechallenge", 'age-gate'); ?></label></th>
                    <td>
                        <label class="ag-switch">
                            <?php echo form_checkbox(
                                array(
                                    'name' => "ag_settings[rechallenge]",
                                    'id' => "wp_age_gate_rechallenge"
                                ),
                                1, // value
                                $values['rechallenge'] // checked
                            ); ?>
                            <span class="ag-switch__slider"></span>
                        </label> <label for="wp_age_gate_rechallenge"><?php _e("If someone fails the age test, they can try again.", 'age-gate'); ?></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="wp_age_gate_fail_link"><?php _e("Redirect failures", 'age-gate'); ?></label></th>
                    <td>


                        <?php $this->render_language_input('fail_link', $values, 'settings', 'link-select'); ?>

                        <p><?php _e("If someone fails the age test, redirect them to a page or external site rather than showing errors.", 'age-gate'); ?></p>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php submit_button(); ?>
    </form>
</div>
