<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// constructs a single icon object given a individual icon's raw data object
class SimpleIcon {
	public $title 			= '';
	public $slug 			= '';
	public $color 			= '';
	public $custom_class 	= '';
	public $size 			= null;
	public $html 			= '';
	private $title_tag		= true;


	function __construct($raw_icon_data, $settings) {
		if (is_object($raw_icon_data)) {
			$this->set_property('title', $raw_icon_data);
			$this->set_property('slug', $raw_icon_data);
			$this->title_tag = isset($settings['title_tag']) ? $settings['title_tag'] : $this->title_tag;
			if (isset($settings['class'])) $this->set_custom_class($settings['class']);
			$this->set_size($settings['size']);
			$this->set_color($raw_icon_data, isset($settings['color']) ? $settings['color'] : false);
			$this->set_html($raw_icon_data);

		} else {
			simpleicons_debug("Unable to construct " . get_class($this) . " with variable type: " . gettype($raw_icon_data));
		}
	}

	private function set_property($property, $raw_icon_data) {
		if (property_exists($raw_icon_data, $property)) {
			$this->$property = $raw_icon_data->$property;
		} else {
			simpleicons_debug("Icon object missing '" . $property . "' property.");
		}
	}

	private function set_html($raw_icon_data) {
		if (isset($this->title) && isset($this->slug) && isset($this->color)) {
			// build the output
			$this->html = '<span class="simple-icon-' . sanitize_html_class($this->slug) . $this->custom_class . '"';
			$this->html .= ' style="fill:' . $this->color . ';';
			$this->html .= $this->size ? ' height:' . $this->size . '; width:' . $this->size . ';' : '';
			$this->html .= '">';
				$this->html .= $this->sanitize_svg($raw_icon_data->svg);
			$this->html .= '</span>';
		} else {
			simpleicons_debug("Unable to set icon HTML due to variables missing");
		}
	}

	private function set_custom_class($custom_class) {
		$this->custom_class = $custom_class ? ' ' . sanitize_html_class($custom_class) : '';
	}

	private function set_size($custom_size) {
		if ($custom_size) {
			$this->size = esc_attr($custom_size);
		}
	}

	private function set_color($raw_icon_data, $custom_color) {
		$color = $custom_color ? $custom_color : $raw_icon_data->hex;
		
		if (isset($color)) {			
			// check if all characters are valid hexadecimal digits (ie. ffffff, fff, etc.)
			if (ctype_xdigit($color)) {
				$this->color = esc_attr('#' . $color);
			// characters are not valid hexadecimal (ie. red, blue, #fff, etc.)
			} else {
				$this->color = esc_attr($color);
			}
		} else {			
			simpleicons_debug("Icon object missing hex property.");
		}
	}

	private function sanitize_svg($svg) {
		if (isset($svg)) {
			$wp_kses_settings = array( 
			    'svg' => array(
			        'role' => array(),
			        'viewbox' => array(),
			        'xmlns' => array()
			    ),
			    'title' => array(),
			    'path' => array(
			    	'd' => array()
			    )
			);
			// remove the title tag if needed (for menu items)
			if ($this->title_tag === false) {
				unset($wp_kses_settings['title']);
			}
			return wp_kses($svg, $wp_kses_settings);
		} else {
			simpleicons_debug("Icon object missing svg property.");
		}
	}
}