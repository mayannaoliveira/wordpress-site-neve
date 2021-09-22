<?php if (! defined('ABSPATH')) {
    exit('No direct script access allowed');
}

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://agegate.io
 * @since      2.0.1
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/admin
 */

/**
 * The advanced settings of the plugin.
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/admin
 * @author     Phil Baker
 * @todo       Get CSS REF from new Age Gate Website
 */
class Age_Gate_Addons extends Age_Gate_Common
{
    private $addons;
    private $get;
    private $addon;

    public function __construct()
    {
        parent::__construct();

        $this->addons = [];

        $this->addons = apply_filters('age_gate_addons', $this->addons);
        $this->addons = array_unique($this->addons, SORT_REGULAR);
        $this->get = $this->validation->sanitize($_GET);

        if (isset($this->get['addon']) && isset($this->addons[$this->get['addon']])) {
            $this->addon = $this->addons[$this->get['addon']];
            $this->addon['id'] = $this->get['addon'];
        }
    }

    public function register_addons()
    {
        if ($this->addons) {
            add_action('admin_menu', [$this, 'addons_page'], 11);
            add_filter('age_gate_admin_tabs', [$this, 'addons_tab']);
        }
    }

    public function addons_tab($tabs)
    {
        return array_merge($tabs, [
        'age-gate-addons' =>
        array(
          'cap' => 'ag_manage_appearance',
          'title' => _x('Addons', 'Admin tab title', 'age-gate')
        ),
      ]);
    }

    public function addons_page()
    {
        add_submenu_page(
            'age-gate',
            __('Age Gate Addons', 'age-gate'),
            __('Addons', 'age-gate'),
            'ag_manage_appearance',
            'age-gate-addons',
            [$this, 'addon_page']
        );
    }

    public function addon_page()
    {
        include AGE_GATE_PATH . 'admin/partials/age-gate-admin-addons.php';
    }

    public function _check_cap()
    {
        if (is_admin()
      && isset($this->get['page']) && $this->get['page'] === 'age-gate-addons'
      && isset($this->get['addon']) && !empty($this->get['addon'])
    ) {
            $id = $this->get['addon'];

            if (!isset($this->addons[$id]['has_options']) || $this->addons[$id]['has_options'] !== true) {
                $this->_deny_access();
            }

            if (!isset($this->addons[$id]['cap'])) {
                $this->_deny_access();
            }

            if (!current_user_can($this->addons[$id]['cap'])) {
                $this->_deny_access();
            }
        }
    }

    /**
     * Handle settings post from form
     * @return mixed
     * @since 2.0.0
     */
    public function handle_form_submission()
    {
        $post = $this->validation->sanitize($_POST);
        if (! isset($post['nonce']) || ! wp_verify_nonce($post['nonce'], 'age_gate_addon')) {
            $this->_set_admin_notice(array('message' => __('Sorry, your nonce did not verify.', 'age-gate'), 'status' => 'error'));



            // set_transient( 'age_gate_admin_notice',  );
            wp_redirect(add_query_arg(['page' => 'age-gate-addons'], admin_url('admin.php')));
            exit;
        }


        $validation = [];
        $validation = apply_filters("age_gate_addon_{$post['addon']}_validation", $validation);
        $is_valid = $this->validation->is_valid($post['ag_settings'], $validation);
        if ($is_valid !== true) {
            $errors = '';
            foreach ($is_valid as $key => $value) {
                $errors .= $value['message'] . '<br />';
            }
            $this->_set_admin_notice(array('message' => __($errors, 'age-gate'), 'status' => 'error'));
        } else {
            update_option("wp_age_gate_addon_{$post['addon']}", $post['ag_settings']);
            $this->_set_admin_notice(array('message' => __('Settings saved successfully.', 'age-gate'), 'status' => 'success'));
        }

        wp_redirect(add_query_arg(['page' => 'age-gate-addons'], admin_url('admin.php')));
        exit;

        // add validation rules
    }

    private function _addon_icon($addon)
    {
        if (isset($addon['icon']) && !empty($addon['icon'])) {
            $img = '<img src="%s" alt="%s" />';
            return sprintf($img, $addon['icon'], $addon['name']);
        }
    }

    private function _deny_access()
    {
        do_action('admin_page_access_denied');
        wp_die(__('Sorry, you are not allowed to access this page.'), 403);
    }
}
