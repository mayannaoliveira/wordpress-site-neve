<?php

/*
Plugin Name: Popular Brand SVG Icons - Simple Icons
Plugin URI: https://thememason.com/plugins/popular-brand-svg-icons-simple-icons/
Description: An easy to use SVG icons plugin with over 500+ brand icons. Use these icons in your menus, widgets, posts, or pages.
Version: 2.8.3
Author: Theme Mason
Author URI: https://thememason.com/
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// global vars
define ( 'SIMPLE_ICONS_VERSION', '2.8.3');
define ( 'SIMPLE_ICONS_DEBUG', false);
define ( 'SIMPLE_ICONS_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define ( 'SIMPLE_ICONS_PLUGIN_PATH', plugin_dir_path( __FILE__ ));
$simple_icons_is_ajax = false;
$simple_icons_ajax_debug = array();
$simple_icons_debug = array();

include( SIMPLE_ICONS_PLUGIN_PATH . 'inc/welcome-screen.php');
include( SIMPLE_ICONS_PLUGIN_PATH . 'inc/admin-page.php');
include( SIMPLE_ICONS_PLUGIN_PATH . 'inc/classes/SimpleIcons.php');
include( SIMPLE_ICONS_PLUGIN_PATH . 'inc/classes/SimpleIcon.php');


// For debugging purposes (clear cache)
// wp_cache_flush();
// delete_transient('simpleicons_version');
// add_action('init', 'simpleicons_delete_all_transients');
// add_action('wp', 'simpleicons_display_all_icons');

if (SIMPLE_ICONS_DEBUG === true ) {
    function debug($data) {
    	echo '<pre>';
    		print_r($data);
    	echo '</pre>';
    }

    function simpleicons_delete_all_transients() {     
        global $wpdb;
     
        $sql = 'DELETE FROM ' . $wpdb->options . ' WHERE option_name LIKE "_transient_%"';
        $wpdb->query($sql);
    }

    function simpleicons_display_all_icons() {
        $json_url = SIMPLE_ICONS_PLUGIN_PATH . "icons.json";
        $json = file_get_contents($json_url);
        $all_icons_data = json_decode($json);

        foreach ($all_icons_data as $key => $value) {
            echo do_shortcode('[simple_icon name="' . $value->slug . '"]');
        }
    }
}


// trigger shortcodes in custom HTML widgets
add_filter( 'widget_text', 'do_shortcode' );

function simpleicons_debug($message) {
    global $simple_icons_is_ajax;
    global $simple_icons_ajax_debug;
    global $simple_icons_debug;
	if (!is_admin() && SIMPLE_ICONS_DEBUG === true && !$simple_icons_is_ajax) {
    	$simple_icons_debug[] = '<h2>' . esc_html($message) . '</h2>';
	} else if (SIMPLE_ICONS_DEBUG === true && $simple_icons_is_ajax) {
        $simple_icons_ajax_debug[] = esc_html($message);
    }
}

add_action('wp_head', 'simpleicons_css');
add_action('admin_head', 'simpleicons_css');

function simpleicons_css() {
    ?>
        <style>
            span[class*="simple-icon-"] {
            	width: 1.5rem;
            	height: 1.5rem;
            	display: inline-block;

            }
            span[class*="simple-icon-"] svg {
            	display: inline-block;
            	vertical-align: middle;
                height: inherit;
                width: inherit;
            }
        </style>
    <?php
}

add_action( 'wp_ajax_simpleicons_search_icons', 'simpleicons_search_icons' );
add_action( 'wp_ajax_nopriv_simpleicons_search_icons', 'simpleicons_search_icons' );

function simpleicons_search_icons() {
    global $simple_icons_is_ajax;
    global $simple_icons_ajax_debug;
    $simple_icons_is_ajax = true;

	$page = intval( $_POST['page'] );
	$search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

	$output = SimpleIcons::search_icons($search, $page);

    if (SIMPLE_ICONS_DEBUG === true) {
        $output['debug'] = $simple_icons_ajax_debug;
    }

    echo json_encode($output);

	wp_die(); // this is required to terminate immediately and return a proper response
}


function simpleicons_shortcode_func($atts) {
    global $simple_icons_debug;  
    // reset debug  
    $simple_icons_debug = array();

    $a = shortcode_atts( array(
        'name'      => null,    // icon slug (ie: facebook)
        'color'     => false,   // (ie: blue, #fff, etc.)
        'size'      => null,    // size of icon (ie: 24px, 1.5rem)
        'class'     => false,   // custom CSS class name
        'cache'     => true,    // cache the icon
        'title_tag' => true     // <title> tag inside svg
    ), $atts );

    if (isset($a['name'])) {
        $icon = new SimpleIcons();
		$output = $icon->display_single_icon($a);  
    } else {
    	$output = false;
    }

    // loop through and output debug messages
    if (SIMPLE_ICONS_DEBUG && $simple_icons_debug) {
        foreach ($simple_icons_debug as $message) {
            $output .= $message;
        }
    }

    return $output;
}
add_shortcode( 'simple_icon', 'simpleicons_shortcode_func' );


// Filters all menu item titles for a #placeholder# 
function simpleicons_menu_items( $menu_items ) {
    foreach ( $menu_items as $menu_item ) {
    	// check if string contains two hashtags
        if ( substr_count($menu_item->title, '#') === 2 ) {
        	// get all shortcodes
            global $shortcode_tags;

            if ( array_key_exists( 'simple_icon', $shortcode_tags ) ) {
                // check if the title attribute is set
                $has_title = strlen($menu_item->post_excerpt) > 0 ? false : true;

            	// call the shortcode within the title
                $menu_item->title = call_user_func( 
                    $shortcode_tags['simple_icon'], 
                	array(
                		'name' => str_replace('#', '', $menu_item->title),
                        'title_tag' => $has_title
                	)
                );
                // add a simple-icon class to the menu-item
                array_push($menu_item->classes, 'simple-icon');
            }
        }

    }
    return $menu_items;
}
add_filter( 'wp_nav_menu_objects', 'simpleicons_menu_items' );

// add css and js files for admin page
function simpleicons_admin_page_scripts() {
	$screen = get_current_screen();

	if ($screen->id === 'settings_page_simpleicons') {
		wp_enqueue_style( 'simple_icons_css', SIMPLE_ICONS_PLUGIN_URL . 'inc/css/simple-icons-admin.css', false, SIMPLE_ICONS_VERSION );
		wp_enqueue_script( 'simple_icons_js', SIMPLE_ICONS_PLUGIN_URL . 'inc/js/simple-icons-admin.js', $deps = array('jquery'), SIMPLE_ICONS_VERSION, $in_footer = true );
        wp_localize_script( 'simple_icons_js', 'simple_icons_settings', 
            array(
                'debug' => SIMPLE_ICONS_DEBUG
            ) 
        );
	}
}
add_action('admin_enqueue_scripts', 'simpleicons_admin_page_scripts');

// add links to plugin under settings > plugin
function simple_icons_add_action_links ( $links ) {
	$mylinks = array(
    	'<a href="' . admin_url( 'index.php?page=simpleicons-welcome' ) . '">Get Started</a>',
		'<a href="' . admin_url( 'options-general.php?page=simpleicons' ) . '">View Icons</a>',
	);
	return array_merge( $links, $mylinks );
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'simple_icons_add_action_links' );


register_activation_hook( __FILE__, 'simple_icons_activate' );
function simple_icons_activate() {
  set_transient( '_simple_icons_activation_redirect', true, 30 );
}

