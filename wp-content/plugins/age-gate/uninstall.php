<?php if ( ! defined('ABSPATH')) exit('No direct script access allowed');

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://agegate.io
 * @since      1.0.0
 *
 * @package    Age_Gate
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}



delete_option('wp_age_gate_version');

/* General is the V1 setting. Keeping it to be thorough */
delete_option('wp_age_gate_general');

/* V2 settings */
delete_option('wp_age_gate_access');
delete_option('wp_age_gate_advanced');
delete_option('wp_age_gate_appearance');
delete_option('wp_age_gate_messages');
delete_option('wp_age_gate_restrictions');
delete_option('wp_age_gate_serial');
delete_option('wp_age_gate_validation_messages');
delete_option('age_gate_serial');
delete_option('wp_age_gate_api');

// transients
global $wpdb;
$options = $wpdb->options;

$t  = esc_sql( "_transient_timeout_%_age_gate%" );

$sql = $wpdb->prepare (
"
	SELECT option_name, option_value
	FROM $options
	WHERE option_name LIKE '%s'
",
$t
);

$transients = $wpdb->get_results( $sql );

foreach ($transients as $transient) {
	// Strip away the WordPress prefix in order to arrive at the transient key.
	$key = str_replace( '_transient_timeout_', '', $transient->option_name );

	// Now that we have the key, use WordPress core to the delete the transient.
	delete_transient( $key );
}


// Remove capabilties
global $wp_roles;

foreach([
	'ag_manage_restrictions',
	'ag_manage_appearance',
	'ag_manage_advanced',
	'ag_manage_messaging',
	'ag_manage_settings',
	'ag_manage_set_content_restriction',
	'ag_manage_set_content_bypass',
	'ag_manage_set_custom_age'
] as $cap){

	foreach ($wp_roles->roles as $key => $value) {
		$wp_roles->remove_cap( $key, $cap );
	}
}

wp_cache_flush();
