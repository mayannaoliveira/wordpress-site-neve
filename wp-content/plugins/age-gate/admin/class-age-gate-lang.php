<?php if ( ! defined('ABSPATH')) exit('No direct script access allowed');

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
class Age_Gate_Multi_Lingual extends Age_Gate_Common {

  public function __construct() {

		parent::__construct();

	}

  public function is_multi_lingual(){
    // WPML / Polylang
    if(function_exists('icl_get_current_language')){
      $all = icl_get_languages();
      $current = icl_get_current_language();
      $default = icl_get_default_language();

      // no languages set
      if(!$all) return;

      if (!is_admin()) {
        $this->_get_fe_lang_info($all, $current, $default);
      } else {
        $this->_get_admin_lang_info($all, $current, $default);
      }


    }
    // WP Multilang
    if(function_exists('wpm_get_user_language')){
      $all = $this->_uniform_values(wpm_get_languages());
      $default = wpm_get_default_language();
      $current = (!is_admin()) ? wpm_get_language() : wpm_get_user_language();

      // no languages set
      if(!$all) return;

      if (!is_admin()) {
        $this->_get_fe_lang_info($all, $current, $default);
      } else {
        $this->_get_admin_lang_info($all, $current, $default);
      }
    }
  }

  private function _get_admin_lang_info($all, $current, $default)
  {
    // set the current to be the default is none exists
    // $current = ($current === 'all' || !$current ? $default : $current);
    self::$language = (object) array(
      'current' => ($current === 'all' || !$current ? [] : $all[$current]),
      'default' => $all[$default],
    );

    unset($all[$default]);
    self::$language->languages = $all;

  }

  private function _get_fe_lang_info($all, $current, $default)
  {
    // There's no current, is there a cookie?

    if(!$current){
      // attempt to determine the post ID
      $post_id = url_to_postid( $_SERVER['REQUEST_URI'] , '_wpg_def_keyword', true );

      if($post_id){
        $current = pll_get_post_language($post_id);
      } else {
        $current = (isset($_COOKIE['pll_language'])) ? $_COOKIE['pll_language'] : $default;
      }
    }
    $current = apply_filters('age_gate_language', $current);
    self::$language = (object) array(
      'current' => ($current === 'all' || !$current ? [] : $all[$current]),
      'default' => $all[$default],
    );

    unset($all[$default]);
    self::$language->languages = $all;
  }

  private function _uniform_values($all)
  {

    foreach ($all as $lang => $arr) {
      $all[$lang]['language_code'] = $lang;
      $all[$lang]['country_flag_url'] = wpm_get_flags_dir() . $arr['flag'];
    }

    return $all;
  }

}
