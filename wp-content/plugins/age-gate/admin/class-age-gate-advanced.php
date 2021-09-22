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
 * The advanced settings of the plugin.
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/admin
 * @author     Phil Baker
 * @todo       Get CSS REF from new Age Gate Website
 */
class Age_Gate_Advanced extends Age_Gate_Common
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add the sub menu for advanced Settings
     * @since 2.0.0
     */
    public function add_settings_page()
    {
        add_submenu_page(
            $this->plugin_name,
            __('Age Gate Advanced Settings', 'age-gate'),
            __('Advanced', 'age-gate'),
            AGE_GATE_CAP_ADVANCED,
            $this->plugin_name . '-advanced',
            array($this, 'display_options_page')
        );
    }

    /**
       * Register the JavaScript for the advanced area.
       *
       * @since    2.0.0
       * @todo     Possibly migrate to CodeMirror as it's bundled from 4.9
       */
    public function enqueue_scripts()
    {
        global $pagenow;
        if ('admin.php' == $pagenow && isset($_GET['page']) && $_GET['page'] == 'age-gate-advanced') {
            wp_enqueue_script('jquery-ui-dialog'); // jquery and jquery-ui should be dependencies, didn't check though...
            wp_enqueue_style('wp-jquery-ui-dialog');

            wp_enqueue_script($this->plugin_name . '-ace', 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.9/ace.js', array(), null, true);
            wp_enqueue_script($this->plugin_name . '-ace-mode', 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.9/mode-css.js', array($this->plugin_name . '-ace'), null, true);
            wp_enqueue_script($this->plugin_name . '-editor', AGE_GATE_URL . 'admin/js/age-gate-editor.js', array($this->plugin_name . '-ace', $this->plugin_name . '-ace-mode'), $this->version, true);
        }
    }


    /**
     * Display advanced settings options
     * @since 2.0.0
     */
    public function display_options_page()
    {
        $values = $this->_filter_values($this->settings['advanced'], null);
        include AGE_GATE_PATH . 'admin/partials/age-gate-admin-advanced.php';
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
        if (! isset($post['nonce']) || ! wp_verify_nonce($post['nonce'], 'age_gate_update_advanced')) {
            $this->_set_admin_notice(array('message' => __('Sorry, your nonce did not verify.', 'age-gate'), 'status' => 'error'));



            // set_transient( 'age_gate_admin_notice',  );
            wp_redirect(add_query_arg(['page' => 'age-gate-advanced'], admin_url('admin.php')));
            exit;
        }

        // set empty values so everything is stored
        // this will fix the issue of some settings getting
        // overwritten on update
        $values = $this->_filter_values($post['ag_settings'], 0);

        $values['custom_bots'] = (is_array($values['custom_bots']) ? array_unique(array_filter($values['custom_bots'])) : []);

        // handle no js css post
        //
        if (!$this->_validate_css($post)) {
            $values['custom_css'] = $this->settings['advanced']['custom_css'];
        }


        if ($values['save_to_file'] && is_writable(AGE_GATE_PATH . 'public/css/age-gate-custom.css')) {
            file_put_contents(AGE_GATE_PATH . 'public/css/age-gate-custom.css', stripslashes(htmlspecialchars_decode(html_entity_decode($values['custom_css']), ENT_QUOTES)));
        }

        update_option('wp_age_gate_advanced', $values);

        $this->_set_admin_notice(array('message' => __('Settings saved successfully.', 'age-gate'), 'status' => 'success'));

        if ($values['use_js']) {
            $this->_set_admin_notice(array('message' => __('You are using the JavaScript implementation of Age Gate, if you have caching enabled ensure you purge it to see your changes.', 'age-gate'), 'status' => 'info'));
        }

        // set_transient( 'age_gate_admin_notice',  );
        wp_redirect(add_query_arg(['page' => 'age-gate-advanced'], admin_url('admin.php')));
    }

    /**
     * Run tests on the CSS
     * @param  mixed $data [description]
     * @return bool       [description]
     * @since  2.0.0
     */
    private function _validate_css($data)
    {
        if (!isset($data['ag_settings']['custom_css'])) {
            // not set FALSE
            return false;
        } elseif (!isset($data['safecss']) || !wp_verify_nonce($data['safecss'], 'age_gate_custom_css')) {
            $this->_set_admin_notice(array(
        'message' => sprintf('%s <button class="button restore-css" disabled>%s</button>', __('There was an issue with the CSS input so this has not been updated to protect your site. If you think this is should not have happened, you can restore the data and review it. ', 'age-gate'), __('Restore', 'age-gate')), 'status' => 'warning hide-if-no-js'));

            // set_transient( 'age_gate_admin_notice_css',  );
            set_transient('age_gate_css_backup', $data['ag_settings']['custom_css']);

            return false;
        }


        return true;
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
        $empties = array_fill_keys($this->config->defaults->advanced, $fill);

        return array_merge($empties, $data);
    }
}
