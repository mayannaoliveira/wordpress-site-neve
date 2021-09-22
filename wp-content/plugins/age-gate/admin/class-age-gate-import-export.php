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
 * The appearance settings of the plugin.
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/admin
 * @author     Phil Baker
 */
class Age_Gate_Import_Export extends Age_Gate_Common {

  private $_exportOptions;

  public function __construct() {

		parent::__construct();

    $default_settings = [
      'restrictions' => [
        'options' => ['wp_age_gate_restrictions'],
        'label' => __('Restrictions settings', 'age-gate')
      ],
      'messages' => [
        'options' => [
          'wp_age_gate_messages',
          'wp_age_gate_validation_messages',
        ],
        'label' => __('Messages settings', 'age-gate')
      ],
      'appearance' => [
        'options' => ['wp_age_gate_appearance'],
        'label' => __('Appearance settings', 'age-gate')
      ],
      'advanced' => [
        'options' => ['wp_age_gate_advanced'],
        'label' => __('Advanced settings', 'age-gate')
      ],
      'access' => [
        'options' => ['wp_age_gate_access'],
        'label' => __('Access settings', 'age-gate')
      ]
    ];

    $custom_settings = [];
    $custom_settings = apply_filters('age_gate_export_settings', $custom_settings);
    $custom_settings = (is_array($custom_settings) ? $custom_settings : []);
    $this->_exportOptions = array_merge($default_settings, $custom_settings, $default_settings);

    if(isset($_POST['ag_import_action']) && $this->settings['advanced']['enable_import_export']){
      $this->_import_export();
    }
	}

  /**
   * Add the sub menu for global Settings
   * @since 2.0.0
   */
  public function add_settings_page()
  {
    if(!$this->settings['advanced']['enable_import_export']) return;
    add_submenu_page(
			$this->plugin_name,
			__('Age Gate Import/Export Settings', 'age-gate'),
			__('Import/Export', 'age-gate'),
			AGE_GATE_CAP_ACCESS,
			$this->plugin_name . '-import-export',
			array($this, 'display_options_page')
		);
  }

  /**
   * Display global settings options
   * @since 2.0.0
   */
  public function display_options_page()
  {
    include AGE_GATE_PATH . 'admin/partials/age-gate-admin-import-export.php';
  }

  public function import_export_tab($tabs)
  {
    if(!$this->settings['advanced']['enable_import_export']) return $tabs;
    return array_merge($tabs, [
      'age-gate-import-export' =>
        array(
          'cap' => AGE_GATE_CAP_ACCESS,
          'title' => _x('Import/Export', 'Admin tab title', 'age-gate')
        )
      ]);
  }

  private function _import_export()
  {
    $post = $this->validation->sanitize($_POST);

    if ('export' === $post['ag_import_action']) {
      $settings = (isset($post['ag_setting'])) ? $post['ag_setting'] : [];
      $this->_export($settings);
    } else {
      $this->_import();
    }

  }

  private function _export($settings = []){

    if(!$settings){
      add_action('admin_notices', function () {
        ?>
        <div class="notice notice-error"><p><?php _e('No Settings selected', 'age-gate') ?></p></div>
        <?php
      });
      return;
    }

    $file_name = 'age-gate-export-' . date('Y-m-d') . '.json';

    $json = $this->_constructResponse($settings);

    header( "Content-Description: File Transfer" );
    header( "Content-Disposition: attachment; filename={$file_name}" );
    header( "Content-Type: application/json; charset=utf-8" );

    // return
    echo json_encode( $json, JSON_PRETTY_PRINT );
    die;
  }
  private function _import(){
    $files = $this->validation->sanitize($_FILES);
    if(!isset($files['ag_json']['type']) || empty($files['ag_json']['type'])){
      add_action('admin_notices', function () {
        ?>
        <div class="notice notice-error"><p><?php _e('No file', 'age-gate') ?></p></div>
        <?php
      });
      return;
    }
    if($files['ag_json']['type'] !== 'application/json'){
      add_action('admin_notices', function () {
        ?>
        <div class="notice notice-error"><p><?php _e('Invalid file type', 'age-gate') ?></p></div>
        <?php
      });
      return;
    }
    // echo "<pre>";
    // wp_die(print_r(json_decode(file_get_contents($files['ag_json']['tmp_name']), true)));
    $import = json_decode(file_get_contents($files['ag_json']['tmp_name']), true);



    $count = 0;
    if($import){
      foreach ($import as $key => $options) {
        if (is_array($options)) {
          foreach ($options as $id => $value) {
            if(isset($this->config->defaults->$key)){
              $value = $this->_filter_values($value, null, $key);
            }
            update_option($id, $value);
            $count++;
          }
        }
      }
    }

    if($count){
      add_action('admin_notices', function () {
        ?>
        <div class="notice notice-success"><p><?php _e('Settings imported', 'age-gate') ?></p></div>
        <?php
      });
      return;
    }
  }

  private function _constructResponse($settings){

    $json = [
      'version' => get_option('wp_age_gate_version')

    ];

    foreach($this->_exportOptions as $id => $setting){
      if(in_array($id, $settings)){
        $json[$id] = [];
        foreach ($setting['options'] as $key => $option) {
          $json[$id][$option] = get_option($option, []);
        }
      }
    }

    return $json;


  }

  /**
   * Filter to ensure all fields get sent to the DB
   * @param  [type] $data [description]
   * @param  [type] $fill [description]
   * @return [type]       [description]
   * @since   2.0.0
   */
  private function _filter_values($data, $fill = null, $type)
  {
    $empties = array_fill_keys($this->config->defaults->$type, $fill);

    return array_merge($empties, $data);
  }
}
