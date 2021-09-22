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
        <input type="hidden" name="action" value="age_gate_advanced">
        <?php wp_nonce_field('age_gate_update_advanced', 'nonce'); ?>

        <h3><?php _e('Caching', 'age-gate'); ?></h3>
        <p><?php _e('If you have a caching solution, it is best to use a JavaScript triggered version of the age gate as this won’t be adversely affected by the cache. If you don’t have caching, the standard method is recommended.', 'age-gate'); ?></p>
        <table class="form-table">
            <tbody>
                <tr <?php echo (defined('WP_CACHE') && WP_CACHE) ? 'class="ag-switch-disabled"' : ''; ?>>
                    <th scope="row">
                        <label for="wp_age_gate_use_standard"><?php _e('Use uncachable version', 'age-gate'); ?></label>
                    </th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><?php _e('Select an option', 'age-gate'); ?></legend>
                            <label class="ag-switch">
                                <?php
                                echo form_checkbox(
        [
                                        'name' => 'ag_settings[use_js]',
                                        'id' => 'wp_age_gate_use_standard'
                                    ],
        1,
        $values['use_js']
    ); ?><span class="ag-switch__slider"></span>
                            </label> <label for="wp_age_gate_use_standard"><?php _e('Use JavaScript Age Gate', 'age-gate'); ?></label>
                            <?php if (defined('WP_CACHE') && WP_CACHE) : ?>
                                <br /><small><?php echo sprintf(__('JavaScript mode has been enabled by default as %s is set to true'), '<code>WP_CACHE</code>'); ?></small>
                            <?php endif; ?>
                        </fieldset>
                    </td>
                </tr>
                <tr class="ep" <?php echo(!$values['use_js'] ? ' style="display: none;"' : '') ?>>
                    <th scope="row">
                        <label for="wp_age_gate_method"><?php _e('AJAX endpoint', 'age-gate'); ?></label>
                    </th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><?php _e('Select an option', 'age-gate'); ?></legend>
                            <label class="ag-switch">
                                <?php echo form_radio(
        array(
                                        'name' => 'ag_settings[endpoint]',
                                        'id' => 'wp_age_gate_endpoint_ajax'
                                    ),
        'ajax',
        ($values['endpoint'] === 'ajax')
    ); ?><span class="ag-switch__slider"></span>
                            </label> <label for="wp_age_gate_endpoint_ajax"><?php _e('Admin Ajax', 'age-gate'); ?></label><br />

                            <label class="ag-switch">
                                <?php echo form_radio(
                                    array(
                                        'name' => 'ag_settings[endpoint]',
                                        'id' => 'wp_age_gate_endpoint_rest'
                                    ),
                                    'rest',
                                    ($values['endpoint'] === 'rest')
                                ); ?><span class="ag-switch__slider"></span>
                            </label> <label for="wp_age_gate_endpoint_rest"><?php _e('REST API', 'age-gate'); ?></label>
                            <p class="note"><?php _e('Where to send the AJAX request', 'age-gate'); ?></p>

                        </fieldset>
                    </td>
                </tr>

                <tr class="ep" <?php echo(!$values['use_js'] ? ' style="display: none;"' : '') ?>>
                    <th scope="row"><?php _e('JS Hooks', 'age-gate'); ?></th>
                    <td>
                        <label class="ag-switch">
                            <?php echo form_checkbox(
                                    array(
                                    'name' => 'ag_settings[js_hooks]',
                                    'id' => 'wp_age_gate_js_hooks'
                                ),
                                    1,
                                    $values['js_hooks']
                                ); ?><span class="ag-switch__slider"></span>
                        </label> <label><?php echo sprintf(__('%s and %s filters available in JS files', 'age-gate'), '<code>age_gate_restricted</code>', '<code>age_gate_set_cookie</code>') ?></label>

                    </td>

                </tr>
                <tr class="ep" <?php echo(!$values['use_js'] ? ' style="display: none;"' : '') ?>>
                    <th scope="row"><?php _e('Hook query string', 'age-gate'); ?></th>
                    <td>
                        <label class="ag-switch">
                            <?php echo form_checkbox(
                                    array(
                                    'name' => 'ag_settings[filter_qs]',
                                    'id' => 'wp_age_gate_filter_qs'
                                ),
                                    1,
                                    $values['filter_qs']
                                ); ?><span class="ag-switch__slider"></span>
                        </label> <label><?php echo sprintf(__('Send query string to %s filter', 'age-gate'), '<code>age_gate_restricted</code>') ?></label>

                    </td>

                </tr>
                <tr class="ep" <?php echo(!$values['use_js'] ? ' style="display: none;"' : '') ?>>
                    <th scope="row"><?php _e('Munge options', 'age-gate'); ?></th>
                    <td>
                        <label class="ag-switch">
                            <?php echo form_checkbox(
                                array(
                                    'name' => 'ag_settings[munge_options]',
                                    'id' => 'wp_age_gate_munge_options'
                                ),
                                1,
                                $values['munge_options']
                            ); ?><span class="ag-switch__slider"></span>
                        </label> <label><?php echo __('Output options as a string to bypass concat scripts like Litespeed', 'age-gate') ?></label>

                    </td>

                </tr>
            </tbody>
        </table>
        <hr />
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="wp_age_gate_anonymous_age_gate"><?php _e('Anonymous Age Gate', 'age-gate'); ?></label>
                    </th>
                    <td>
                        <?php if (!$this->settings['restrictions']['multi_age']) : ?>
                            <fieldset>
                                <legend class="screen-reader-text"><?php _e('Select an option', 'age-gate'); ?></legend>
                                <label class="ag-switch">
                                    <?php echo form_checkbox(
                                array(
                                            'name' => 'ag_settings[anonymous_age_gate]',
                                            'id' => 'wp_age_gate_anonymous_age_gate'
                                        ),
                                1,
                                $values['anonymous_age_gate']
                            ); ?><span class="ag-switch__slider"></span>
                                </label> <label><?php _e('Use anonymous Age Gate', 'age-gate'); ?></label>
                                <p class="note"><?php _e('This option makes Age Gate only store if a user has passed the challange and not an age for extra privacy', 'age-gate'); ?></p>
                            </fieldset>
                        <?php else : ?>
                            <p><?php _e('This setting is unavailable with "Varied ages" selected in the restrictions tab', 'age-gate'); ?></p>
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <hr />
        <table class="form-table">
            <tr>
                <th>Inheritance taxonomies</th>
                <td>
                    <?php $tax = get_taxonomies(['public' => true, 'show_ui' => true], 'objects'); ?>
                    <?php foreach ($tax as $key => $taxonomy) : ?>
                        <label class="ag-switch">
                            <?php echo form_checkbox(
                                array(
                                    'name' => 'ag_settings[inherit_taxonomies][' . $taxonomy->name . ']',
                                    'id' => 'wp_age_gate_inherit_taxonomies'
                                ),
                                $taxonomy->name,
                                isset($values['inherit_taxonomies'][$taxonomy->name])
                            ); ?><span class="ag-switch__slider"></span>
                        </label> <label><?php echo $taxonomy->label; ?></label>
                    <?php endforeach; ?>

                </td>
            </tr>
        </table>
        <hr />
        <h3><?php _e('Import/Export', 'age-gate'); ?></h3>
        <table class="form-table">
            <tr>
                <th scope="row">Enable import/export</th>
                <td>
                    <label class="ag-switch">
                        <?php echo form_checkbox(
                                        array(
                                'name' => 'ag_settings[enable_import_export]',
                                'id' => 'wp_age_gate_enable_import_export'
                            ),
                                        1,
                                        $values['enable_import_export']
                                    ); ?><span class="ag-switch__slider"></span>
                    </label> <label><?php _e('Enable settings import/export', 'age-gate'); ?></label>
                </td>
            </tr>
        </table>
        <hr />
        <h3><?php _e('RTA Meta Tag', 'age-gate'); ?></h3>
        <table class="form-table">
            <tr>
                <th scope="row">Enable RTA tag</th>
                <td>
                    <label class="ag-switch">
                        <?php echo form_checkbox(
                                array(
                                'name' => 'ag_settings[rta_tag]',
                                'id' => 'wp_age_gate_rta_tag'
                            ),
                                1,
                                $values['rta_tag']
                            ); ?><span class="ag-switch__slider"></span>
                    </label>
                </td>
            </tr>
        </table>
        <hr />
        <h3><?php _e('Toolbar options', 'age-gate'); ?></h3>
        <table class="form-table">
            <tr>
                <th scope="row">Show in front end toolbar</th>
                <td>
                    <label class="ag-switch">
                        <?php echo form_radio(
                            array(
                                'name' => 'ag_settings[full_nav]',
                                'id' => 'wp_age_gate_full_nav_off'
                            ),
                            'off',
                            ($values['full_nav'] === 'off')
                        ); ?><span class="ag-switch__slider"></span>
                    </label> <label><?php _e('Off - don&rsquo;t show in toolbar', 'age-gate'); ?></label><br /><br />
                    <label class="ag-switch">
                        <?php echo form_radio(
                            array(
                                'name' => 'ag_settings[full_nav]',
                                'id' => 'wp_age_gate_full_nav_toggle'
                            ),
                            'toggle',
                            ($values['full_nav'] === 'toggle')
                        ); ?><span class="ag-switch__slider"></span>
                    </label> <label><?php _e('Toggle - menu option to switch Age Gate on/off. Only displays on restriced content', 'age-gate'); ?></label>
                </td>
            </tr>
        </table>
        <hr />
        <h3><?php _e('Editor options', 'age-gate'); ?></h3>
        <table class="form-table">
            <tbody>
                <?php /*
        <tr>
          <th scope="row">
            <label for="wp_age_gate_use_standard"><?php _e('Display as meta box', 'age-gate'); ?></label>
          </th>
          <td>
            <fieldset>
              <legend class="screen-reader-text"><?php _e('Select an option', 'age-gate'); ?></legend>
              <label>
                <?php echo form_checkbox(
                  array(
                    'name' => 'ag_settings[use_meta_box]',
                    'id' => 'wp_age_gate_use_meta_box'
                  ),
                  1,
                  $values['use_meta_box']
                ); ?> <?php _e('Display Age Gate post settings in a meta box', 'age-gate'); ?>. <?php _e('Tick if using Gutenberg', 'age-gate'); ?>
              </label>
            </fieldset>
          </td>
        </tr> */ ?>
                <tr>
                    <th scope="row">
                        <label for="wp_age_gate_quick_tags"><?php _e('Enable quicktags', 'age-gate'); ?></label>
                    </th>
                    <td>
                        <label class="ag-switch">
                            <?php echo form_checkbox(
                            array(
                                    'name' => 'ag_settings[enable_quicktags]',
                                    'id' => 'wp_age_gate_enable_quicktags'
                                ),
                            1,
                            $values['enable_quicktags']
                        ); ?><span class="ag-switch__slider"></span>
                        </label> <label><?php _e('Enable quicktags in messaging WYSIWYG', 'age-gate'); ?></label>
                    </td>
                </tr>
            </tbody>
        </table>
        <hr />
        <?php if (self::$language) : ?>
            <h3><?php _e('Translations', 'age-gate'); ?></h3>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="wp_age_gate_use_default_lang"><?php _e('Use default language', 'age-gate'); ?></label>
                    </th>
                    <td>
                        <label class="ag-switch">
                            <?php echo form_checkbox(
                            array(
                                    'name' => 'ag_settings[use_default_lang]',
                                    'id' => 'wp_age_gate_use_default_lang'
                                ),
                            1,
                            $values['use_default_lang']
                        ); ?><span class="ag-switch__slider"></span>
                        </label> <label><?php _e('Use the default language if a translation is missing') ?></label>
                    </td>
                </tr>
            </table>
            <hr />
        <?php else : ?>
            <?php echo form_hidden('ag_settings[use_default_lang]', $values['use_default_lang']) ?>
        <?php endif; ?>
        <?php if ($this->is_dev()) : ?>
            <h3><?php _e('Dev builds', 'age-gate'); ?></h3>
            <p><?php _e('Get notifications of new development builds. These will not be installed for you and you will only see notifications if you are already using a development version.', 'age-gate'); ?></p>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="wp_age_gate_dev_notify"><?php _e('Development Versions', 'age-gate'); ?></label>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><?php _e('Select an option', 'age-gate'); ?></legend>
                                <label class="ag-switch">
                                    <?php echo form_checkbox(
                                array(
                                            'name' => 'ag_settings[dev_notify]',
                                            'id' => 'wp_age_gate_dev_notify'
                                        ),
                                1,
                                $values['dev_notify']
                            ); ?><span class="ag-switch__slider"></span>
                                </label> <label><?php _e('Show messages about new development versions', 'age-gate'); ?></label>
                            </fieldset>
                        </td>

                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="wp_age_gate_dev_hide_warning"><?php _e('Development warning', 'age-gate'); ?></label>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><?php _e('Select an option', 'age-gate'); ?></legend>
                                <label class="ag-switch">
                                    <?php echo form_checkbox(
                                array(
                                            'name' => 'ag_settings[dev_hide_warning]',
                                            'id' => 'wp_age_gate_dev_hide_warning'
                                        ),
                                1,
                                $values['dev_hide_warning']
                            ); ?><span class="ag-switch__slider"></span>
                                </label> <label><?php _e('Hide the development warning message', 'age-gate'); ?></label>
                            </fieldset>
                        </td>

                    </tr>
                </tbody>
            </table>
            <hr />
        <?php endif; ?>
        <h3><?php _e('Cookie name', 'age-gate'); ?></h3>
        <p><?php _e('You can can set a custom name for the Age Gate cookie. Note it will be prepended with <code>ag_</code> to attempt to avoid conflicts', 'age-gate'); ?></p>
        <p><em><?php _e('You should only change this setting in exceptional circumstances, e.g. you have removed remember me from the site.', 'age-gate'); ?></em></p>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <?php echo __('Cookie name', 'age-gate'); ?>
                    </th>
                    <td>
                        <?php echo form_input(array(
                            'name' => 'ag_settings[cookie_name]',
                            'type' => 'text',
                            // 'id' => 'wp_age_gate_' . $field
                        ), $values['cookie_name'], array('class' => 'regular-text ltr', 'pattern' => "[a-z_]+"));
                        ?>
                        <p class="note"><?php _e('May contain lowercase letters and underscores only', 'age-gate'); ?></p>
                    </td>
                </tr>
            </tbody>
        </table>
        <hr />
        <h3><?php _e('Custom bots', 'age-gate'); ?></h3>
        <p><?php _e('You can add the user agent string of any bots that are not automatically picked up', 'age-gate'); ?></p>
        <table class="form-table">
            <tbody>
                <?php if (is_array($values['custom_bots'])) : foreach ($values['custom_bots'] as $key => $bot) : ?>
                        <tr>
                            <td>
                                <?php echo form_input(array(
                                    'name' => 'ag_settings[custom_bots][]',
                                    'type' => 'text',
                                    // 'id' => 'wp_age_gate_' . $field
                                ), $bot, array('class' => 'large-text ltr'));
                                ?>
                            </td>
                        </tr>
                <?php endforeach;
                endif; ?>
                <tr>
                    <td><button type="button" class="button-secondary ag-add-bot"><?php _e('Add custom bot', 'age-gate'); ?></button></td>
                </tr>
            </tbody>
        </table>

        <hr />
        <h3><?php _e('Custom Styling', 'age-gate'); ?></h3>
        <p><?php _e('You can add custom CSS for the Age Gate here.', 'age-gate'); ?></p>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="wp_age_gate_store_css"><?php _e('Write custom CSS to file', 'age-gate'); ?></label>
                    </th>
                    <td>
                        <?php if (!is_writable(AGE_GATE_PATH . 'public/css/age-gate-custom.css')) : ?>
                            <?php
                            _e('Unable to write to custom CSS file. Custom style will be enqueued inline.', 'age-gate');
                            echo form_hidden('ag_settings[save_to_file]', 0);
                            ?>
                        <?php else : ?>
                            <label class="ag-switch">
                                <?php echo form_checkbox(
                                array(
                                        'name' => "ag_settings[save_to_file]",
                                        'id' => "wp_age_gate_store_css"
                                    ),
                                1, // value
                                    $values['save_to_file'] // checked
                            ); ?><span class="ag-switch__slider"></span>
                            </label> <label><?php _e('Will save custom CSS to file and enqueue it on the front-end', 'age-gate'); ?></label>
                        <?php endif; ?>

                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e('Custom CSS', 'age-gate'); ?>
                    </th>
                    <td>
                        <div class="ag-css-editor">
                            <img src="<?php echo admin_url('images/spinner-2x.gif'); ?>" alt="Loading..." class="hide-if-no-js" />
                            <div id="css-editor">
                                <noscript>
                                    <p><?php _e('Sorry, JavaScript is required for this feature', 'age-gate'); ?></p>
                                </noscript>
                            </div>
                        </div>

                        <a href="https://agegate.io/docs/styling/css-reference" target="_blank" class="button" title="<?php _e('CSS Reference', 'age-gate'); ?>"><?php _e('CSS Reference', 'age-gate'); ?></a>
                        <button type="button" class="button load-default-css hide-if-no-js" title="<?php _e('Load Default CSS', 'age-gate'); ?>"><?php _e('Load Default CSS', 'age-gate'); ?></button>

                    </td>
                </tr>
            </tbody>
        </table>

        <div class="css-warning"></div>
        <?php submit_button(); ?>
    </form>
</div>
