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
 * The messaging settings of the plugin.
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/admin
 * @author     Phil Baker
 */
class Age_Gate_Messaging extends Age_Gate_Common
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add the sub menu for messaging Settings
     * @since 2.0.0
     */
    public function add_settings_page()
    {
        add_submenu_page(
            $this->plugin_name,
            __('Age Gate Messaging Settings', 'age-gate'),
            __('Messaging', 'age-gate'),
            AGE_GATE_CAP_MESSAGING,
            $this->plugin_name . '-messaging',
            array($this, 'display_options_page')
        );
    }

    /**
     * Display messaging settings options
     * @since 2.0.0
     */
    public function display_options_page()
    {
        $language_values = (isset($this->settings['messages']['lang']) ? $this->settings['messages']['lang'] : []);
        if (isset($this->settings['messages']['lang'])) {
            unset($this->settings['messages']['lang']);
        }
        $values = $this->_filter_values($this->settings['messages'], null);

        $language_validation = (isset($this->settings['validation']['lang']) ? $this->settings['validation']['lang'] : []);
        if (isset($this->settings['validation']['lang'])) {
            unset($this->settings['validation']['lang']);
        }
        $validation = $this->_filter_validation($this->settings['validation'], null);

        if (self::$language) {
            $values['lang'] = $this->_filter_language_values($language_values);
            $validation['lang'] = $this->_filter_language_validation($language_validation);
        }
        include AGE_GATE_PATH . 'admin/partials/age-gate-admin-messaging.php';
    }

    /**
     * Handle settings post from form
     * @return mixed
     * @since 2.0.0
     */
    public function handle_form_submission()
    {
        /**
         * Remove additional from post before sanitizing
         * to maintain it's html
         * @var string
         */

        $additional = $this->sanitize_content($_POST['ag_settings']['additional']);

        $additional_langs = [];
        // remove any languages ones
        if (isset($_POST['ag_settings']['lang'])) {
            foreach ($_POST['ag_settings']['lang'] as $code => $value) {
                $additional_langs[$code] = $this->sanitize_content($value['additional']);
            }
        }


        // Sanitize the post data
        $post = $this->validation->sanitize($this->stripslashes_deep($_POST));

        $post['ag_settings']['additional'] = esc_html($additional);

        foreach ($additional_langs as $lang => $value) {
            $post['ag_settings']['lang'][$lang]['additional'] = $value;
        }

        if (! isset($post['nonce']) || ! wp_verify_nonce($post['nonce'], 'age_gate_update_messages')) {
            $this->_set_admin_notice(array('message' => __('Sorry, your nonce did not verify.', 'age-gate'), 'status' => 'error'));

            // set_transient( 'age_gate_admin_notice',  );
            wp_redirect(add_query_arg(['page' => 'age-gate-messaging'], admin_url('admin.php')));
            exit;
        }

        // set empty values so everything is stored
        // this will fix the issue of some settings getting
        // overwritten on update
        $values = $this->_filter_values($post['ag_settings'], 0);

        if (isset($values['lang']) && $values['lang']) {
            $values['lang'] = $this->_filter_language_values($values['lang']);
        }

        update_option('wp_age_gate_messages', $values);

        $validation_messages = array_merge(get_option('wp_age_gate_validation_messages', array()), $post['ag_validation']);
        update_option('wp_age_gate_validation_messages', $validation_messages);

        $this->_set_admin_notice(array('message' => __('Settings saved successfully.', 'age-gate'), 'status' => 'success'));

        if ($this->settings['advanced']['use_js']) {
            $this->_set_admin_notice(array('message' => __('You are using the JavaScript implementation of Age Gate, if you have caching enabled ensure you purge it to see your changes.', 'age-gate'), 'status' => 'info'));
        }

        wp_redirect(add_query_arg(['page' => 'age-gate-messaging'], admin_url('admin.php')));
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
        $empties = array_fill_keys($this->config->defaults->messages, $fill);

        return array_merge($empties, $data);
    }

    private function sanitize_content($content)
    {
        $content = str_replace("\\", "", $this->stripslashes_deep($content));
        $content = urldecode($content);
        $content = html_entity_decode($content);

        $allowed = include AGE_GATE_PATH . 'admin/config/additional-content.php';
        $allowed = apply_filters('age_gate/presentation/allowed_tags', $allowed);
        return addslashes(wp_kses($content, $allowed));
    }

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

    private function _filter_language_validation($values)
    {
        foreach (self::$language->languages as $code => $value) {
            if ($code !== self::$language->default['language_code']) {
                $data = isset($values[$code]) ? $values[$code] : [];
                $values[$code] = $this->_filter_validation($data, null);
            }
        }

        return $values;
    }

    /**
     * Filter to ensure all fields get sent to the DB
     * @param  [type] $data [description]
     * @param  [type] $fill [description]
     * @return [type]       [description]
     * @since   2.0.0
     */
    private function _filter_validation($data, $fill)
    {
        $empties = array_fill_keys([
      'validate_required',
      'validate_max_len',
      'validate_min_len',
      'validate_max_numeric',
      'validate_numeric',
      'validate_min_numeric',
      'validate_date'
    ], $fill);

        return array_merge($empties, $data);
    }

    /**
       * Callback to display remove fields from tinymce
       * @param  mixed $args
       * @since 1.0.0
       */
    public function customise_tinymce($buttons)
    {
        global $hook_suffix;
        // return the orginal array if not on Age Gate
        if ('age-gate_page_age-gate-messaging' !== $hook_suffix) {
            return $buttons;
        }

        $removeButtons = array('formatselect','blockquote','alignleft','aligncenter','alignright','wp_more','fullscreen','wp_adv');

        foreach ($buttons as $button_key => $button_value) {
            if (in_array($button_value, $removeButtons)) {
                unset($buttons[$button_key]);
            }
        }

        return $buttons;
    }
}
