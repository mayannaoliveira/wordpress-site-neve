<?php if (! defined('ABSPATH')) {
    exit('No direct script access allowed');
}

/**
 * Handle any AJAX Requests.
 *
 * @link       https://agegate.io
 * @since      2.0.0
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
 */
class Age_Gate_Ajax extends Age_Gate_Common
{
    public function __construct()
    {
        parent::__construct();
    }

    public function editor_get_css()
    {
        echo $this->_format_css(file_get_contents(AGE_GATE_PATH . 'public/css/age-gate-public.css'));
        wp_die();
    }

    public function get_text_area()
    {
        // set_transient(get_current_user_id() . '');
        echo '<textarea name="ag_settings[custom_css]" id="wp_age_gate_custom_css">' . stripslashes($this->settings['advanced']['custom_css']) . '</textarea>' . wp_nonce_field('age_gate_custom_css', 'safecss', false, false);
        wp_die();
    }

    public function set_ag3_hidden()
    {
        set_transient('age_gate_3_message', 1, WEEK_IN_SECONDS);
        wp_die();
    }

    public function get_stored_css()
    {
        echo stripslashes(get_transient('age_gate_css_backup'));
        wp_die();
    }

    public function regenerate_serial()
    {
        $serial = abs(2147483648 + mt_rand(-2147482448, 2147483647) * mt_rand(-2147482448, 2147483647));
        update_option('age_gate_serial', $serial);

        if ($serial) {
            $message = __('Updated successfully.', 'age-gate');

            if ($this->settings['advanced']['use_js']) {
                $message .= ' ';
                $message .= __('You are using the JavaScript implementation of Age Gate, if you have caching enabled ensure you purge it to see your changes.', 'age-gate');
            }
        } else {
            $message = __('Could not update. ', 'age-gate');
        }

        header("Content-type:application/json");
        echo json_encode(['serial' => $serial, 'message' => $message]);
        wp_die();
    }

    private function _format_css($css)
    {
        $css = str_replace('{', " {\r\n\t", $css);
        $css = str_replace('}', "\r\n}\r\n", $css);
        $css = str_replace(':', ": ", $css);
        $css = str_replace(';', ";\r\n\t", $css);

        return $css;
    }
}
