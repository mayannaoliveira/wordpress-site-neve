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
 * The restriction settings of the plugin.
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/admin
 * @author     Phil Baker
 */
class Age_Gate_Restriction extends Age_Gate_Common
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add the sub menu for restriction Settings
     * @since 2.0.0
     */
    public function add_settings_page()
    {
        add_submenu_page(
            $this->plugin_name,
            __('Age Gate Restriction Settings', 'age-gate'),
            __('Restrictions', 'age-gate'),
            AGE_GATE_CAP_RESTRICTIONS,
            $this->plugin_name,
            array($this, 'display_options_page')
        );
    }

    /**
     * Display restriction settings options
     * @since 2.0.0
     */
    public function display_options_page()
    {
        $language_values = (isset($this->settings['restrictions']['lang']) ? $this->settings['restrictions']['lang'] : []);
        if (isset($this->settings['restrictions']['lang'])) {
            unset($this->settings['restrictions']['lang']);
        }

        $values = $this->_filter_values(get_option('wp_age_gate_restrictions', array()), null);

        if (self::$language) {
            $this->settings['restrictions']['lang'] = $values['lang'] = $this->_filter_language_values($language_values);

            foreach ($values['lang'] as $lang => $value) {
                $values['lang'][$lang]['min_age'] = $this->_get_translated_setting('restrictions', 'min_age', $lang, true);
            }
        }
        include AGE_GATE_PATH . 'admin/partials/age-gate-admin-restriction.php';
    }

    /**
     * Handle settings post from form
     * @return mixed
     * @since 2.0.0
     */
    public function handle_form_submission()
    {
        $lang = false;
        // Sanitize the post data
        if (self::$language && isset($_POST['lang'])) {
            $lang = $this->validation->sanitize($_POST['lang']);
        }
        $post = $this->validation->sanitize($_POST);

        if ($lang) {
            $post['lang'] = $lang;
        }

        if (! isset($post['nonce']) || ! wp_verify_nonce($post['nonce'], 'age_gate_update_restrictions')) {
            $this->_set_admin_notice(array('message' => __('Sorry, your nonce did not verify.', 'age-gate'), 'status' => 'error'));

            // set_transient( 'age_gate_admin_notice',  );
            wp_redirect(add_query_arg(['page' => 'age-gate'], admin_url('admin.php')));
            exit;
        }

        // set empty values so everything is stored
        // this will fix the issue of some settings getting
        // overwritten on update
        $values = $this->_filter_values($post['ag_settings'], 0);

        update_option('wp_age_gate_restrictions', $values);

        $this->_set_admin_notice(array('message' => __('Settings saved successfully.', 'age-gate'), 'status' => 'success'));

        if ($this->settings['advanced']['use_js']) {
            $this->_set_admin_notice(array('message' => __('You are using the JavaScript implementation of Age Gate, if you have caching enabled ensure you purge it to see your changes.', 'age-gate'), 'status' => 'info'));
        }

        // set_transient( 'age_gate_admin_notice',  );
        wp_redirect(add_query_arg(['page' => 'age-gate'], admin_url('admin.php')));
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
        $empties = array_fill_keys($this->config->defaults->restrictions, $fill);


        return array_merge($empties, $data);
    }

    /**
     * Filter the language versions of the settings
     * @param  [type] $values [description]
     * @return [type]         [description]
     */
    private function _filter_language_values($values)
    {
        foreach (self::$language->languages as $code => $value) {
            if ($code !== self::$language->default['language_code']) {
                $data = isset($values[$code]) ? $values[$code] : [];
                $values[$code] = $this->_filter_values($data, null);
            }
        }

        return $values;
    }
}
