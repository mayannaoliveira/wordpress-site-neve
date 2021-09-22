<?php if (! defined('ABSPATH')) {
    exit('No direct script access allowed');
}


class Age_Gate_Common
{

  /**
   * The ID of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $plugin_name    The ID of this plugin.
   */
    protected $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    protected $version;

    /**
     * The settings of this plugin.
     *
     * @since    2.0.0
     * @access   private
     * @var      string    $settings    The current settings of this plugin.
     */
    protected $settings;

    /**
     * The config of this plugin.
     *
     * @since    2.0.0
     * @access   private
     * @var      string    $config    The config of this plugin.
     */
    protected $config;

    /**
     * The custom form validation/sanitizer of this plugin.
     *
     * @since    2.0.0
     * @access   private
     * @var      string    $custom form validation/sanitizer    The custom form validation/sanitizer of this plugin.
     */
    protected $validation;

    /**
     * To check what copy to output
     * @var [type]
     */
    protected static $language;

    protected $meta;
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct()
    {
        $this->plugin_name = AGE_GATE_NAME;
        $this->version = AGE_GATE_VERSION;

        $this->config = $this->_get_config();
        $this->settings = $this->_get_settings();

        $this->validation = new Age_Gate_Validation;
    }

    private function _get_config()
    {
        return (!is_admin() ? (object) array() : include AGE_GATE_PATH . 'admin/config/config.php');
    }

    private function _get_settings($type = null)
    {
        $settings = apply_filters('ag_settings', array());
        $settings = array_merge($settings, array(
            'restrictions' => get_option('wp_age_gate_restrictions', array()),
            'messages' => get_option('wp_age_gate_messages', array()),
            'validation' => get_option('wp_age_gate_validation_messages', array()),
            'appearance' => get_option('wp_age_gate_appearance', array()),
            'access' => get_option('wp_age_gate_access', array()),
            'advanced' => get_option('wp_age_gate_advanced', array()),
        ));

        if (defined('WP_CACHE') && WP_CACHE) {
            $settings['advanced']['use_js'] = 1;
        }
        return $settings;
    }

    protected function _set_admin_notice($notice = array())
    {
        if (!$notices = get_transient('age_gate_admin_notice')) {
            $notices = array();
        }

        $notices[] = $notice;

        set_transient('age_gate_admin_notice', $notices);
    }

    protected function is_dev()
    {
        $data = get_plugin_data(AGE_GATE_PATH . 'age-gate.php');
        $sub = explode('-', ($data['Version']));

        if (isset($sub[1]) && !empty($sub[1])) {
            return $sub[1];
        }

        return false;
    }

    protected function get_cookie_name()
    {
        return ($this->settings['advanced']['cookie_name']) ? 'ag_' . $this->settings['advanced']['cookie_name'] : 'age_gate';
    }


    /**
     * Strip slashes "usefully" added by WP
     */
    protected function stripslashes_deep($value)
    {
        $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
        return $value;
    }

    /**
     * Shortcut helper for rendering multilingial fields
     * @param  [type] $field  [description]
     * @param  [type] $values [description]
     * @param  string $prefix [description]
     * @param  string $type   [description]
     * @return [type]         [description]
     * @since  2.1.0
     */
    public function render_language_input($field, $values, $prefix = 'settings', $type = 'inputs')
    {
        include AGE_GATE_PATH . 'admin/partials/language/language-'. $type .'.php';
    }

    /**
     *
     */
    public function _get_translated_setting($setting, $key, $language, $force_default = false)
    {
        $use_default = (!$force_default) ? $this->settings['advanced']['use_default_lang'] : true;
        if (isset($this->settings[$setting]['lang'][$language][$key]) && !empty($this->settings[$setting]['lang'][$language][$key])) {
            return $this->settings[$setting]['lang'][$language][$key];
        } else {
            return ($use_default || $this->_is_default_lang()) ? $this->settings[$setting][$key] : false;
        }
    }

    private function _is_default_lang()
    {
        return (self::$language->current['language_code'] === self::$language->default['language_code']);
    }

    /**
     *
     */
    protected function array_insert($array, $index, $insert)
    {
        return array_slice($array, 0, $index, true) + $insert +
    array_slice($array, $index, count($array) - $index, true);
    }
}
