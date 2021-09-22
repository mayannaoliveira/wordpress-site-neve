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
 * Adds the restriction options to taxonomies
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/admin
 * @author     Phil Baker
 */
class Age_Gate_Taxonomies extends Age_Gate_Common {

  public function __construct() {

		parent::__construct();

	}


  public function register_taxonomies_fields(){

    foreach (get_taxonomies() as $key => $value) {
      add_action( $key . '_add_form_fields', [$this, 'add_form_input'] );
      add_action( $key . '_edit_form_fields', [$this, 'edit_form_input'] );

      add_action ( 'edited_' . $key, array($this, 'test_save_fields'), 10, 2);
      add_action ( 'create_' . $key, array($this, 'test_save_fields'), 10, 2);
    }

  }

  public function test_save_fields($term_id)
  {
    // This can be called at other times
    // e.g. through an importer. This could mean the settings do not exits
    if(!isset($_POST['ag_settings'])) return;

    $post = $this->_filter_values($this->validation->sanitize($_POST['ag_settings']), null);

    // set custom age
    $this->_set_age($post['age'], $term_id);
    $this->_set_restrict($post['restrict'], $term_id);
    $this->_set_bypass($post['bypass'], $term_id);

  }

  public function add_form_input()
  {

    if ($this->settings['restrictions']['restriction_type'] === 'all') {

      include_once AGE_GATE_PATH . '/admin/partials/taxonomy/add-all.php';

    } else {

      include_once AGE_GATE_PATH . '/admin/partials/taxonomy/add-selected.php';
    }

  }

  public function edit_form_input()
  {

    $id = !isset($_GET['tag_ID']) ? null : $_GET['tag_ID'];

    if(!$id){
      _e('ID not found', 'age-gate');
      return;
    }

    $term_meta = [
      'age' => get_term_meta($id, '_age_gate-age', true),
      'bypass' => get_term_meta($id, '_age_gate-bypass', true),
      'restrict' => get_term_meta($id, '_age_gate-restrict', true)
    ];

    if (empty($term_meta['age'])) {
      $term_meta['age'] = $this->settings['restrictions']['min_age'];
    }

    $term_meta['is_restricted'] = $this->settings['restrictions']['restriction_type'] === 'selected' && $term_meta['restrict'] || $this->settings['restrictions']['restriction_type'] !== 'selected' && !$term_meta['bypass'];

    // if ($this->settings['restrictions']['restriction_type'] === 'all') {

      include_once AGE_GATE_PATH . '/admin/partials/taxonomy/edit-all.php';

    // } else {

      // include_once AGE_GATE_PATH . '/admin/partials/taxonomy/edit-selected.php';
    // }
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
    $empties = array_fill_keys([
      'bypass',
      'restrict',
      'age',
    ], $fill);

    return array_merge($empties, $data);
  }

  /**
   * Set the post meta of the age restrictions
   * @param integer $age [description]
   */
  private function _set_age($age, $id)
  {
    if(!current_user_can(AGE_GATE_CAP_SET_CUSTOM_AGE)){
      return false;
    }

    if(!$this->settings['restrictions']['multi_age']){
      return false;
    }

    if(!$age || $age == $this->settings['restrictions']['min_age']){
      delete_term_meta($id, '_age_gate-age');
    } else {
      update_term_meta( $id, '_age_gate-age', $age );

    }
  }

  /**
   * Set the post meta of the age restrictions
   * @param integer $age [description]
   */
  private function _set_restrict($restrict, $id)
  {
    if(!current_user_can(AGE_GATE_CAP_SET_CONTENT)){

      return false;
    }

    if ($this->settings['restrictions']['restriction_type'] !== 'selected') {

      return false;
    }

    if(!$restrict){

      delete_term_meta($id, '_age_gate-restrict');
    } else {

      update_term_meta($id, '_age_gate-restrict', 1);
    }
    // _age_gate-restrict
  }


  /**
   * Set the post meta of the age restrictions
   * @param integer $age [description]
   */
  private function _set_bypass($bypass, $id)
  {
    if(!current_user_can(AGE_GATE_CAP_SET_BYPASS)){

      return false;
    }

    if ($this->settings['restrictions']['restriction_type'] !== 'all') {

      return false;
    }

    if(!$bypass){

      delete_term_meta($id, '_age_gate-bypass');
    } else {

      update_term_meta($id, '_age_gate-bypass', 1);
    }
    // all
    // _age_gate-bypass
  }


}
