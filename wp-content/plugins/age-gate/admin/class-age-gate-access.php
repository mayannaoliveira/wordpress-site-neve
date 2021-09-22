<?php if (! defined('ABSPATH')) {
    exit('No direct script access allowed');
}

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://agegate.io
 * @since      2.0.0
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/admin
 */

/**
 * The access settings of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/admin
 * @author     Phil Baker
 */
class Age_Gate_Access extends Age_Gate_Common
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add the sub menu for global Settings
     * @since 2.0.0
     */
    public function add_settings_page()
    {
        add_submenu_page(
            $this->plugin_name,
            __('Age Gate Access Settings', 'age-gate'),
            __('Access', 'age-gate'),
            AGE_GATE_CAP_ACCESS,
            $this->plugin_name . '-access',
            array($this, 'display_options_page')
        );
    }

    /**
     * Display global settings options
     * @since 2.0.0
     */
    public function display_options_page()
    {
        $roles = wp_roles();
        // $roles = apply_filters('editable_roles', $all_roles);
        $values = $this->_filter_values($this->settings['access'], 0);


        // echo "</";
        // wp_die();
        include AGE_GATE_PATH . 'admin/partials/age-gate-admin-access.php';
    }

    /**
     * Handle settings post from form
     * @return mixed
     * @since 2.0.0
     */
    public function handle_form_submission()
    {
        // Sanitize the post data
        $post = $this->validation->sanitize($_POST);

        if (! isset($post['nonce']) || ! wp_verify_nonce($post['nonce'], 'age_gate_update_access')) {
            $this->_set_admin_notice(array('message' => __('Sorry, your nonce did not verify.', 'age-gate'), 'status' => 'error'));
            // set_transient( 'age_gate_admin_notice',  );
            wp_redirect(add_query_arg(['page' => 'age-gate-access'], admin_url('admin.php')));
            exit;
        }

        // ALWAYS add Admin
        foreach ($post['ag_settings']['permissions'] as $key => $type) {
            $post['ag_settings']['permissions'][$key] = $this->_prep_roles($post['ag_settings']['permissions'][$key]);
        }

        $this->_update_caps($post['ag_settings']['permissions']['restrict'], AGE_GATE_CAP_RESTRICTIONS);
        $this->_update_caps($post['ag_settings']['permissions']['messaging'], AGE_GATE_CAP_MESSAGING);
        $this->_update_caps($post['ag_settings']['permissions']['appearance'], AGE_GATE_CAP_APPEARANCE);
        $this->_update_caps($post['ag_settings']['permissions']['advanced'], AGE_GATE_CAP_ADVANCED);
        $this->_update_caps($post['ag_settings']['permissions']['settings'], AGE_GATE_CAP_ACCESS);
        $this->_update_caps($post['ag_settings']['permissions']['restrict_individual'], AGE_GATE_CAP_SET_CONTENT);
        $this->_update_caps($post['ag_settings']['permissions']['bypass_individual'], AGE_GATE_CAP_SET_BYPASS);
        $this->_update_caps($post['ag_settings']['permissions']['custom'], AGE_GATE_CAP_SET_CUSTOM_AGE);




        // set empty values so everything is stored
        // this will fix the issue of some settings getting
        // overwritten on update
        //
        // At this point, only post types are stored
        $values = $this->_filter_values($post['ag_settings']['post_types'], 0);


        update_option('wp_age_gate_access', $values);
        $this->_set_admin_notice(array('message' => __('Settings saved successfully.', 'age-gate'), 'status' => 'success'));
        // set_transient( 'age_gate_admin_notice',  );

        wp_redirect(add_query_arg(['page' => 'age-gate-access'], admin_url('admin.php')));
    }

    /**
     * Filter to ensure all fields get sent to the DB
     * @param  [type] $data [description]
     * @param  [type] $fill [description]
     * @return [type]       [description]
     * @since   2.0.0
     */
    private function _filter_values($data, $fill)
    {
        $data = (!$data) ? [] : $data;
        $keys = [];

        foreach (get_post_types() as $key => $post_type) {
            $keys[] = $post_type;
        }

        $empties = array_fill_keys($keys, $fill);
        return array_merge($empties, $data);
    }

    /**
     * Update the selected roles with age gate permissions
     * @param  array $roles   Array of user roles
     * @param  string $cap    The capability to assign or remove
     * @since  2.0.0
     */
    private function _update_caps($roles, $cap)
    {
        global $wp_roles;

        foreach ($wp_roles->roles as $key => $value) {
            if (in_array($key, $roles)) {
                $wp_roles->add_cap($key, $cap);
            } else {
                $wp_roles->remove_cap($key, $cap);
            }
        }
    }

    /**
     * Always make Admin role have the cap
     * @param  array $roles Selected roles
     * @return array
     */
    private function _prep_roles($roles)
    {
        // forces ADMIN to be selected
        return (!$roles) ? array('administrator') : array_unique(array_merge($roles, array('administrator')));
    }
}
