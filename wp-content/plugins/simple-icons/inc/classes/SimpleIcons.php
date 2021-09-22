<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/* 	
	Organized functions for fetching single or multiple icons (search by param);
	Public functions:
		display_single_icon();
		search_icons();
*/
class SimpleIcons {
	public static function display_single_icon($settings) {		
		$slug 			= self::slugify_name($settings['name']);
		$use_cache 		= $settings['cache'] == 'true' ? true : false; // needs tested
		$ver_changed 	= self::version_changed();
		$icon_data 		= get_transient( 'simpleicons_icon_' . $slug);

		// check if the icon is cached, or if the plugin version has changed
		if ( $icon_data === false || $ver_changed === true ) {
			$all_icons_data = self::get_all_icons_data();

			// check if the icon exists
			if ( !empty($all_icons_data) && array_key_exists($slug, $all_icons_data)) {
				$icon_data = $all_icons_data[$slug];
				// cache the icon data and version number
				if ($use_cache === true) {
					set_transient( 'simpleicons_icon_' . $slug, $icon_data  ); // cache icon data permanently
					simpleicons_debug($slug . ' icon cached permanently');
				}
			} else {
				simpleicons_debug('Icon "' . $slug . '" does not exist in the array');
			}
		}

		if ($icon_data) {
			$single_icon = new SimpleIcon($icon_data, $settings);
			return $single_icon->html;
		} else {
			return false;
		}
	}

	private static function version_changed() {
		$version = get_transient( 'simpleicons_version' );
		$ver_has_changed = wp_cache_get('simpleicons_version_changed');

		if ($version === false) {
			// version transient never set (occurs first time loading the plugin)
			set_transient( 'simpleicons_version', SIMPLE_ICONS_VERSION  ); 
			simpleicons_debug('Version transient set (occurs first time loading the plugin)');
			return true;
		} else {
			if ($version != SIMPLE_ICONS_VERSION || $ver_has_changed) {
				// version has changed, update transient and cache
				set_transient( 'simpleicons_version', SIMPLE_ICONS_VERSION  ); 
				// set a cache variable so additional checks will return true until page reload
				wp_cache_set('simpleicons_version_changed', true);

				simpleicons_debug('Version has changed');
				return true;
			} else {
				// version did not changed
				return false;
			}
		}
	}

	private static function get_all_icons_data() {
		$all_icons_data = wp_cache_get('simpleicons_all_icons_data');

		// check to see if the json file has already been cached during this php page load, or if the plugin version has changed
		if ( $all_icons_data === false ) {
			simpleicons_debug('Icon not cached, version change, or backend loaded');
			$json_url = SIMPLE_ICONS_PLUGIN_PATH . "icons.json";
			$json = file_get_contents($json_url);
			$all_icons_data = json_decode($json);

			// slugify all keys
			$all_icons_data_modified = array();
			foreach ($all_icons_data as $key => $value) {
				$all_icons_data_modified[self::slugify_name($value->slug)] = $value;
			}
			$all_icons_data = $all_icons_data_modified;

			if( !empty( $all_icons_data ) ) {
				// cache the json data for repeat calls until page reload
				wp_cache_add( 'simpleicons_all_icons_data', $all_icons_data);

				simpleicons_debug('JSON file cached (This message should appear one time on the front end if an icon was not cached)');
			}				
		}

		return $all_icons_data;
	}

	public static function search_icons($search_phrase = '', $page = 0) {
		$all_icons_data = self::get_all_icons_data();
		$search_phrase = self::slugify_name($search_phrase);
		$results_per_page = 150;
		$results = array('icons' => array());
		$priority_icons = array(
			'facebook',
			'twitter',
			'youtube',
			'instagram',
			'linkedin',
			'tiktok',
			'pinterest',
			'snapchat'
		);
		$results_priority = array();

		if ($all_icons_data) {
			// loop through each icon and search
			foreach ($all_icons_data as $icon_data) {
				if ($search_phrase === '' || strpos($icon_data->slug, $search_phrase) !== false) {
					$icon = new SimpleIcon($icon_data, array(
						'name' => $icon_data->title,
						'size' => '40px'
					));
					$return = array(
						'slug' => $icon->slug,
						'svg' => $icon->html
					);

					// check if icon is listed in the priority icons array
					if (in_array($icon_data->slug, $priority_icons)) {
						// retain the original order "key"
						$order = array_search($icon_data->slug, $priority_icons);
						$results_priority[$order] = $return;
					} else {
						$results['icons'][] = $return;
					}
				}
			}
			// sort by the order "key"
			ksort($results_priority);
			// prepend the priority icons to the total results
			$results['icons'] = array_merge($results_priority, $results['icons']);

			$results['total_icons'] = number_format(count($results['icons']));

			if (count($results['icons']) > ($page + 1) * $results_per_page) {
				$results['more_items'] = true;
			} else {
				$results['more_items'] = false;
			}

			// slice results by pagination
			$results['icons'] = array_slice($results['icons'], $results_per_page * $page, $results_per_page);
		} else {
			$results['error'] = 'Unable to fetch all icons.';
		}

		// debug($results);
		return $results;

	}

	// slugify name to allow easier names used as shortcode or placeholders
	// ie: user can use WordPress, Wordpress, or wordpress
	private static function slugify_name($name) {
		// make name lowercase and remove spaces and hashes
		$name = str_replace(array(' ', '#', '!', '’'), '', strtolower($name));

		$name = str_replace('+', 'plus', $name);

		$name = str_replace('&', '-and-', $name);

		// rare case for del.icio.us icon
		if ($name === 'del.icio.us') {
			$name = str_replace('.', '', $name);
		}

		$name = str_replace('.', '-dot-', $name);

		// remove first and last character if it is a dash (.NET icon)
		if (substr($name, 0, 1) === '-') { 
			$name = ltrim($name, '-');
		}		
		if (substr($name, -1) === '-') {
			$name = rtrim($name, '-');
		}

		return $name;
	}
}
