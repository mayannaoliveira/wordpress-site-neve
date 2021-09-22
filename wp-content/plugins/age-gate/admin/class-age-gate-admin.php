<?php if (! defined('ABSPATH')) {
    exit('No direct script access allowed');
}

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://agegate.io
 * @since      1.0.0
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/admin
 * @author     Phil Baker
 */
class Age_Gate_Admin extends Age_Gate_Common
{
    public function __construct()
    {
        parent::__construct();
        $this->_update_check();
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles($hook)
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Age_Gate_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Age_Gate_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        $hooks = [
            'edit-tags.php',
            'post.php',
            'post-new.php',
            'edit.php',
            'term.php',
        ];
        if (strrpos($hook, 'age-gate') !== false || in_array($hook, $hooks)) {
            wp_enqueue_style($this->plugin_name, AGE_GATE_URL . 'admin/css/age-gate-admin.css', array(), $this->version, 'all');
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts($hook)
    {
        if (is_customize_preview()) {
            return;
        }

        if (strrpos($hook, 'age-gate') !== false) {

            /**
             * This function is provided for demonstration purposes only.
             *
             * An instance of this class should be passed to the run() function
             * defined in Age_Gate_Loader as all of the hooks are defined
             * in that particular class.
             *
             * The Age_Gate_Loader will then create the relationship
             * between the defined hooks and the functions defined in this
             * class.
             */
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wplink');
            wp_enqueue_script($this->plugin_name, AGE_GATE_URL . 'admin/js/age-gate-admin.js', array( 'jquery', 'wp-color-picker' ), $this->version, false);
            wp_localize_script($this->plugin_name, 'ag', $this->_localize());
        }



        if ('edit.php' === $hook) {
            // if ( 'edit.php' === $hook &&  ) {
            wp_enqueue_script($this->plugin_name . '-edit', AGE_GATE_URL . 'admin/js/age-gate-admin-edit.js', array( 'jquery' ), $this->version, true);
        }
    }


    /**
     * Register global plugin menu item
     *
     * @since 1.0.0 Displays the menu
     */
    public function add_menu_section()
    {
        $this->plugin_page_hook_suffix = add_menu_page(
            __('Age Gate', 'age-gate'),
            __('Age Gate', 'age-gate'),
            AGE_GATE_CAP_RESTRICTIONS,
            $this->plugin_name,
            '__return_false',//array($this, 'display_options_page'),
            'dashicons-lock',
            60
        );
    }


    public function age_gate_admin_notice()
    {
        if ($messages = get_transient('age_gate_admin_notice')) {
            foreach ($messages as $key => $message) {
                echo '<div id="message" class="notice notice-' . $message['status'] . ' is-dismissible"><p>'. $message['message'] .'</p></div>';
            }
        }
        
        if (version_compare(PHP_VERSION, '7.0.0') < 0) {
            if (!get_transient('wp_age_gate_php_7')) {
                echo '<div id="message" class="age-gate-php-warning notice notice-warning is-dismissible"><p>'. __('Your PHP version is out dated, the next major version of Age Gate may not support this version') .'</p></div>';
                set_transient('wp_age_gate_php_7', 1, MONTH_IN_SECONDS);
            }
        }


        if (current_user_can(AGE_GATE_CAP_ACCESS)) {
            $this->_dev_notices();
        }

        // if (!get_transient('age_gate_3_message')) {
        //     echo '<div id="ag-3-message" class="notice notice-info is-dismissible"><p>' . __('Age Gate 3 is coming soon. <a href="' . add_query_arg(['page' => 'age-gate-v3'], admin_url('/admin.php')) . '">Find out more</a>', 'age-gate') . '</p></div>';
        // }

        delete_transient('age_gate_admin_notice');
        delete_transient('age_gate_admin_notice_css');
    }

    private function _dev_notices()
    {
        $data = get_plugin_data(AGE_GATE_PATH . 'age-gate.php');
        $sub = explode('-', ($data['Version']));

        if (isset($sub[1]) && !empty($sub[1]) && !$this->settings['advanced']['dev_hide_warning']) {
            if ($this->settings['advanced']['dev_notify'] && $new = $this->_checkLatest($sub[1])) {
                $messageText = sprintf(__('A new development version of Age Gate has been released. <b>%s</b> is the latest build, you have <b>%s</b>.', 'age-gate'), $new, $sub[1]);
                echo '<div id="message" class="notice notice-info is-dismissible"><p>' . $messageText . ' <a href="https://agegate.io/downloads" target="_blank">'. __('View downloads', 'age-gate') .'</a></p></div>';
            }

            $messageText = sprintf(__('You are using the <b>%s</b> version of Age Gate <b>%s</b>. This may not be suitable for production websites.', 'age-gate'), $sub[1], AGE_GATE_VERSION);
            echo '<div id="message" class="notice notice-error"><p>' . $messageText . '</p></div>';
        }
    }

    public function editor_scripts()
    {
        global $pagenow;
        if (isset($_REQUEST['page']) && strpos($_REQUEST['page'], 'age-gate') !== false) {
            if (! class_exists('_WP_Editors') and (! defined('DOING_AJAX') or ! DOING_AJAX)) {
                require_once ABSPATH.WPINC.'/class-wp-editor.php';
                wp_print_styles('editor-buttons');
                _WP_Editors::wp_link_dialog();
            }
        }
    }

    /**
     * Create a settings link in the plugins screen
     *
     * @param  mixed $links The standard links
     * @return mixed $links	The links updated with our settings
     * @since 1.0.0
     */
    public function plugin_action_links($links)
    {
        $addons = [];
        $addons = apply_filters('age_gate_addons', $addons);
        $addons = array_unique($addons, SORT_REGULAR);


        $settings_link = '<a href="admin.php?page='. $this->plugin_name .'">' . __('Settings', 'age-gate') . '</a>';
        $donate_link = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=donate%40wordpressagegate%2ecom&lc=GB&item_name=Age%20Gate&item_number=Age%20Gate%20Donation&no_note=0&currency_code=GBP&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest" target="_blank">' . __('Donate', 'age-gate') . '</a>';
        array_unshift($links, $settings_link);
        array_push($links, $donate_link);

        if ($addons) {
            unset($links['deactivate']);
        }

        return $links;
    }

    /**
     * Create a link to website
     *
     * @param  mixed $plugin_meta The standard links
     * @return mixed $plugin_file	The links updated with our settings
     * @since 2.0.0
     */
    public function website_link($plugin_meta, $plugin_file)
    {
        $basename = plugin_basename(AGE_GATE_PATH) . '/age-gate.php';

        if ($basename === $plugin_file) {
            $plugin_meta[] = sprintf(
                '<a href="%s">%s</a>',
                'https://agegate.io/docs',
                esc_html__('Documentation', 'age-gate')
            );

            $plugin_meta[] = sprintf(
                '<a href="%s">%s</a>',
                'https://agegate.io/release-notes',
                esc_html__('What&rsquo;s new?', 'age-gate')
            );

            $addons = [];
            $addons = apply_filters('age_gate_addons', $addons);
            $addons = array_unique($addons, SORT_REGULAR);

            if ($addons) {
                $message = '<style>input[value="age-gate/age-gate.php"] {pointer-events: none;cursor: default; opacity:0.3;}</style><br><br>Age Gate cannot be disabled as the following addons are activated:';
                foreach ($addons as $key => $value) {
                    $message .= ' <b>' . $value['name'] . "<b>,";
                }
                $plugin_meta[] = substr($message, 0, -1);
            }
        }

        return $plugin_meta;
    }

    private function _localize()
    {
        return array(
            'uploader' => array(
                'title' => _x('Select an image to upload', 'Image uploader text', 'age-gate'),
                'button' => _x('Use this image', 'Image uploader button', 'age-gate'),
                'remove_image' => __('Remove image', 'age-gate'),

            ),
            'link' => array(
                'remove_link' => __('Remove link', 'age-gate'),
                'custom' => __('Custom', 'age-gate')
            ),
            'css' => array(
                'warning' => __('There are warnings in your CSS. You can save your changes, but there may be display issues.', 'age-gate'),
                'error' => __('There are errors in your CSS. You can save your changes, but there may be display issues.', 'age-gate'),
            ),
            'update_confirm' => array(
                'warning' => _x('I have read the warning and understand updating could cause unexpected results.', 'Warning message presented when trying to update between major versions', 'age-gate'),
                'confirm' => _x('You can now proceed with the update', 'Confirm message presented on enabling update', 'age-gate')
            )
        );
    }

    /**
     * Checks the plugin version against the stored version
     * and updates the settings if mismatched
     *
     * @since 1.1.0
     *
     */
    private function _update_check()
    {
        if (AGE_GATE_VERSION !== get_option('wp_age_gate_version')) {
            require_once AGE_GATE_PATH . 'includes/class-age-gate-activator.php';
            Age_Gate_Activator::activate();
            $this->_set_admin_notice(array('message' => __('It looks like you\'ve recently updated Age Gate, please clear any caches.', 'age-gate'), 'status' => 'warning'));
            // update_option('wp_age_gate_version', AGE_GATE_VERSION);
        }
    }

    /**
     * Forces use of JS age gate in some cases
     * @return [type] [description]
     */
    public static function force_js()
    {
        if (!function_exists('is_plugin_active')) {
            // wp_die(ABSPATH);
            require_once(ABSPATH . "wp-admin/includes/plugin.php");
        }
        return is_plugin_active('wp-e-commerce/wp-shopping-cart.php');
    }

    private function _checkLatest($current)
    {
        return false;
        $x = @file_get_contents('https://agegate.io/api/downloads/v1/latest');
        if ($x) {
            $x = str_replace('"', '', $x);
        }

        if (version_compare($x, $current, ">")) {
            return $x;
        }
    }

    /**
     * Purges the expired Age Gate $transients
     *
     * From 2.1.0 this method only fires if WP Cron is diabled and when hitting admin
     *
     * @return void
     * @since 2.1.0
     *
     */
    public function purge_transients()
    {
        global $pagenow;

        if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON === true && $pagenow === 'admin.php') {
            global $wpdb;
            $options = $wpdb->options;

            $t  = esc_sql("_transient_timeout_%_age_gate%");

            $sql = $wpdb->prepare(
                "
	      SELECT option_name, option_value
	      FROM $options
	      WHERE option_name LIKE '%s'
	    ",
                $t
            );

            $transients = $wpdb->get_results($sql);

            foreach ($transients as $transient) {
                if ($transient->option_value < time()) {
                    // Strip away the WordPress prefix in order to arrive at the transient key.
                    $key = str_replace('_transient_timeout_', '', $transient->option_name);

                    // Now that we have the key, use WordPress core to the delete the transient.
                    delete_transient($key);
                }
            }
        }
    }

    public function ag_toggle()
    {
        if (isset($_REQUEST['ag_switch'])) {
            global $pagenow;
            if ($this->_can_age_gate() &&  isset($_REQUEST['page']) && $_REQUEST['page'] === 'age-gate' && wp_verify_nonce($_REQUEST['_wpnonce'], 'age-gate-toggle')) {
                $cookieName = $this->get_cookie_name();
                if ($_REQUEST['ag_switch'] !== 'hide') {
                    setcookie($cookieName, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN);
                } else {
                    setcookie($cookieName, 99, time() + 3600, COOKIEPATH, COOKIE_DOMAIN);
                }
            }
            wp_redirect($_SERVER['HTTP_REFERER']);
        }
    }

    private function _can_age_gate()
    {
        if (current_user_can(AGE_GATE_CAP_RESTRICTIONS)) {
            return true;
        } elseif (current_user_can(AGE_GATE_CAP_MESSAGING)) {
            return true;
        } elseif (current_user_can(AGE_GATE_CAP_APPEARANCE)) {
            return true;
        } elseif (current_user_can(AGE_GATE_CAP_ADVANCED)) {
            return true;
        } elseif (current_user_can(AGE_GATE_CAP_ACCESS)) {
            return true;
        }

        return false;
    }

    /**
     * Add new header bar
     *
     * @return void
     */
    public function in_admin_header()
    {
        include AGE_GATE_PATH . 'admin/partials/general/admin-header.php';
    }
}
