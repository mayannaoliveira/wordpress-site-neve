<?php if (!defined('ABSPATH')) exit('No direct script access allowed');

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://agegate.io
 * @since      2.0.0
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
        <input type="hidden" name="action" value="age_gate_access">
        <?php wp_nonce_field('age_gate_update_access', 'nonce'); ?>



        <h3><?php _e('Access management', 'age-gate'); ?></h3>

        <p><?php _e('This section allows management of who can administer the Age Gate. Administrators will always have permission.', 'age-gate'); ?>

            <div class="permissions-wrapper">
                <div class="permissions-container">
                    <h4><?php _e('Manage Restriction settings', 'age-gate'); ?></h4>

                    <fieldset>
                        <legend class="screen-reader-text"><?php _e('User roles', 'age-gate'); ?></legend>
                        <input type="hidden" name="ag_settings[permissions][restrict]">
                        <?php foreach ($roles->roles as $key => $role) : ?>
                            <label class="ag-switch">
                                <?php echo form_checkbox(
                                    [
                                        'name' => "ag_settings[permissions][restrict][]",
                                        'id' => 'ag_settings_permissions_restrict_' . sanitize_title($role['name']),
                                    ],
                                    $key, // value
                                    array_key_exists(AGE_GATE_CAP_RESTRICTIONS, $role['capabilities']) // checked

                                ); ?><span class="ag-switch__slider"></span>
                            </label> <label for="ag_settings_permissions_restrict_<?php echo sanitize_title($role['name']); ?>"><?php echo $role['name']; ?></label>
                        <?php endforeach; ?>
                    </fieldset>
                </div>
                <div class="permissions-container">
                    <h4><?php _e('Manage Messaging settings', 'age-gate'); ?></h4>
                    <fieldset>
                        <legend class="screen-reader-text"><?php _e('User roles', 'age-gate'); ?></legend>
                        <input type="hidden" name="ag_settings[permissions][messaging]">
                        <?php foreach ($roles->roles as $key => $role) : ?>
                            <label class="ag-switch">
                                <?php echo form_checkbox(
                                    array(
                                        'name' => "ag_settings[permissions][messaging][]"
                                    ),
                                    $key, // value
                                    array_key_exists(AGE_GATE_CAP_MESSAGING, $role['capabilities']) // checked

                                ); ?><span class="ag-switch__slider"></span>
                            </label> <label><?php echo $role['name']; ?></label>
                        <?php endforeach; ?>
                    </fieldset>
                </div>
                <div class="permissions-container">
                    <h4><?php _e('Manage Appearance settings', 'age-gate'); ?></h4>
                    <fieldset>
                        <legend class="screen-reader-text"><?php _e('User roles', 'age-gate'); ?></legend>
                        <input type="hidden" name="ag_settings[permissions][appearance]">
                        <?php foreach ($roles->roles as $key => $role) : ?>
                            <label class="ag-switch">
                                <?php echo form_checkbox(
                                    array(
                                        'name' => "ag_settings[permissions][appearance][]"
                                    ),
                                    $key, // value
                                    array_key_exists(AGE_GATE_CAP_APPEARANCE, $role['capabilities']) // checked

                                ); ?><span class="ag-switch__slider"></span>
                            </label> <label><?php echo $role['name']; ?></label>
                        <?php endforeach; ?>
                    </fieldset>
                </div>
                <div class="permissions-container">
                    <h4><?php _e('Manage Advanced settings', 'age-gate'); ?></h4>
                    <fieldset>
                        <legend class="screen-reader-text"><?php _e('User roles', 'age-gate'); ?></legend>
                        <input type="hidden" name="ag_settings[permissions][advanced]">
                        <?php foreach ($roles->roles as $key => $role) : ?>
                            <label class="ag-switch">
                                <?php echo form_checkbox(
                                    array(
                                        'name' => "ag_settings[permissions][advanced][]"
                                    ),
                                    $key, // value
                                    array_key_exists(AGE_GATE_CAP_ADVANCED, $role['capabilities']) // checked

                                ); ?><span class="ag-switch__slider"></span>
                            </label> <label><?php echo $role['name']; ?></label>
                        <?php endforeach; ?>
                    </fieldset>
                </div>
                <div class="permissions-container">

                    <h4><?php _e('Manage access permissions', 'age-gate'); ?></h4>
                    <fieldset>
                        <legend class="screen-reader-text"><?php _e('User roles', 'age-gate'); ?></legend>
                        <input type="hidden" name="ag_settings[permissions][settings]">
                        <?php foreach ($roles->roles as $key => $role) : ?>
                            <label class="ag-switch">
                                <?php echo form_checkbox(
                                    array(
                                        'name' => "ag_settings[permissions][settings][]"
                                    ),
                                    $key, // value
                                    array_key_exists(AGE_GATE_CAP_ACCESS, $role['capabilities']) // checked

                                ); ?><span class="ag-switch__slider"></span>
                            </label> <label><?php echo $role['name']; ?></label>
                        <?php endforeach; ?>
                    </fieldset>
                </div>
                <div class="permissions-container">
                    <h4><?php printf('%s <span>("%s")</span>', __('Restrict indiviual content', 'age-gate'), __("Selected content mode", 'age-gate')); ?></h4>
                    <fieldset>
                        <legend class="screen-reader-text"><?php _e('User roles', 'age-gate'); ?></legend>
                        <input type="hidden" name="ag_settings[permissions][restrict_individual]">
                        <?php foreach ($roles->roles as $key => $role) : ?>
                            <label class="ag-switch">
                                <?php echo form_checkbox(
                                    array(
                                        'name' => "ag_settings[permissions][restrict_individual][]"
                                    ),
                                    $key, // value
                                    array_key_exists(AGE_GATE_CAP_SET_CONTENT, $role['capabilities']) // checked

                                ); ?><span class="ag-switch__slider"></span>
                            </label> <label><?php echo $role['name']; ?></label>
                        <?php endforeach; ?>
                    </fieldset>
                </div>
                <div class="permissions-container">
                    <h4><?php printf('%s <span>("%s")</span>', __('Allow bypass for indiviual content', 'age-gate'),  __('All content mode', 'age-gate')); ?></h4>
                    <fieldset>
                        <legend class="screen-reader-text"><?php _e('User roles', 'age-gate'); ?></legend>
                        <input type="hidden" name="ag_settings[permissions][bypass_individual]">
                        <?php foreach ($roles->roles as $key => $role) : ?>
                            <label class="ag-switch">
                                <?php echo form_checkbox(
                                    array(
                                        'name' => "ag_settings[permissions][bypass_individual][]"
                                    ),
                                    $key, // value
                                    array_key_exists(AGE_GATE_CAP_SET_BYPASS, $role['capabilities']) // checked

                                ); ?><span class="ag-switch__slider"></span>
                            </label> <label><?php echo $role['name']; ?></label>
                        <?php endforeach; ?>
                    </fieldset>
                </div>
                <div class="permissions-container">

                    <h4><?php _e('Change age for indiviual content', 'age-gate'); ?></h4>

                    <fieldset>
                        <legend class="screen-reader-text"><?php _e('User roles', 'age-gate'); ?></legend>
                        <input type="hidden" name="ag_settings[permissions][custom]">
                        <?php foreach ($roles->roles as $key => $role) : ?>
                            <label class="ag-switch">
                                <?php echo form_checkbox(
                                    array(
                                        'name' => "ag_settings[permissions][custom][]"
                                    ),
                                    $key, // value
                                    array_key_exists(AGE_GATE_CAP_SET_CUSTOM_AGE, $role['capabilities']) // checked

                                ); ?><span class="ag-switch__slider"></span>
                            </label> <label><?php echo $role['name']; ?></label>
                        <?php endforeach; ?>
                    </fieldset>
                </div>
            </div>

            <h3><?php _e('Post Types', 'age-gate'); ?></h3>

            <p><?php _e('Do not show Age Gate publish actions for the following post types', 'age-gate'); ?>

                <div class="permissions-wrapper">
                    <div class="permissions-container">
                        <fieldset>
                            <legend class="screen-reader-text"><?php _e('Post Types', 'age-gate'); ?></legend>
                            <input type="hidden" name="ag_settings[post_types]">
                            <?php foreach (get_post_types('', 'objects') as $key => $post_type) : ?>
                                <?php if ($post_type->show_ui) : ?>

                                    <label class="ag-switch">
                                        <?php echo form_checkbox(
                                            array(
                                                'name' => "ag_settings[post_types][$key]"
                                            ),
                                            1, // value
                                            $values[$key] // checked

                                        ); ?><span class="ag-switch__slider"></span>
                                    </label> <label><?php echo ($post_type->labels->name ? $post_type->labels->name : $post_type->label); ?></label>

                                <?php endif; ?>
                            <?php endforeach; ?>
                    </div>
                </div>
                <?php submit_button(); ?>
    </form>



</div>
