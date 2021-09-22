<?php if (! defined('ABSPATH')) {
    exit('No direct script access allowed');
}

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://agegate.io
 * @since             1.0.0
 * @package           Age_Gate
 *
 * @wordpress-plugin
 * Plugin Name:       Age Gate
 * Plugin URI:        https://agegate.io/
 * Description:       A customisable age gate to block content from younger people
 * Version:           2.17.0
 * Author:            Phil Baker
 * Author URI:        https://agegate.io/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       age-gate
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

/**
 * Current plugin version.
 */
define( 'AGE_GATE_VERSION', '2.17.0' );

define('AGE_GATE_PATH', plugin_dir_path(__FILE__));
define('AGE_GATE_URL', plugin_dir_url(__FILE__));

/* Permissions */
if (!defined('AGE_GATE_CAP_RESTRICTIONS')) {
    define('AGE_GATE_CAP_RESTRICTIONS', 'ag_manage_restrictions');
}

if (!defined('AGE_GATE_CAP_APPEARANCE')) {
    define('AGE_GATE_CAP_APPEARANCE', 'ag_manage_appearance');
}

if (!defined('AGE_GATE_CAP_ADVANCED')) {
    define('AGE_GATE_CAP_ADVANCED', 'ag_manage_advanced');
}

if (!defined('AGE_GATE_CAP_MESSAGING')) {
    define('AGE_GATE_CAP_MESSAGING', 'ag_manage_messaging');
}

if (!defined('AGE_GATE_CAP_ACCESS')) {
    define('AGE_GATE_CAP_ACCESS', 'ag_manage_settings');
}

if (!defined('AGE_GATE_CAP_SET_CONTENT')) {
    define('AGE_GATE_CAP_SET_CONTENT', 'ag_manage_set_content_restriction');
}

if (!defined('AGE_GATE_CAP_SET_BYPASS')) {
    define('AGE_GATE_CAP_SET_BYPASS', 'ag_manage_set_content_bypass');
}

if (!defined('AGE_GATE_CAP_SET_CUSTOM_AGE')) {
    define('AGE_GATE_CAP_SET_CUSTOM_AGE', 'ag_manage_set_custom_age');
}


/* Text domain */
define('AGE_GATE_TEXT_DOMAIN', 'age-gate');
define('AGE_GATE_NAME', 'age-gate');


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-age-gate-activator.php
 */

function activate_age_gate($networkwide)
{
    if (is_multisite() && $networkwide) {
        foreach (get_sites() as $site) {
            switch_to_blog($site->blog_id);
            Age_Gate_Activator::activate();
            restore_current_blog();
        }
    } else {
        require_once plugin_dir_path(__FILE__) . 'includes/class-age-gate-activator.php';
        Age_Gate_Activator::activate();
    }
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-age-gate-deactivator.php
 */
function deactivate_age_gate()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-age-gate-deactivator.php';
    Age_Gate_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_age_gate');
register_deactivation_hook(__FILE__, 'deactivate_age_gate');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-age-gate.php';


/**
 * Custom function for logging
 */
 if (! function_exists('log_message')) {
     function log_message($log)
     {
         if (!defined('WP_DEBUG') || WP_DEBUG !== true || !defined('WP_DEBUG_LOG') || WP_DEBUG_LOG !== true) {
             return false;
         }
         if (is_array($log) || is_object($log)) {
             error_log(print_r($log, true));
         } else {
             error_log($log);
         }
     }
 }

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_age_gate()
{
    $plugin = new Age_Gate();
    $plugin->run();
}
run_age_gate();
