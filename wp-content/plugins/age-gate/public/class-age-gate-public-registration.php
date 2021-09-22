<?php if ( ! defined('ABSPATH')) exit('No direct script access allowed');

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://agegate.io
 * @since      2.0.0
 * @deprecated 2.0.3
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/public
 * @author     Phil Baker
 */
class Age_Gate_Registration extends Age_Gate_Public {

  public function __construct()
  {
    parent::__construct();

    foreach ($this->settings as $key => $setting) {
      if($key !== 'access' && $key !== 'advanced'){
        $this->{$key} = (object) $setting;
      }
    }
  }

  /**
	 * Add fields to the registration form
	 * @return void Returns nothing
	 * @since 1.0.0
	 * @deprecated 2.0.3
	 */
  public function extend_registration_form()
  {
    if(!$this->settings['restrictions']['restrict_register']) return;

    $day = filter_input( INPUT_POST, 'day', FILTER_SANITIZE_NUMBER_INT );
		$month = filter_input( INPUT_POST, 'month', FILTER_SANITIZE_NUMBER_INT );
		$year = filter_input( INPUT_POST, 'year', FILTER_SANITIZE_NUMBER_INT );
		$settings = $this->settings;

    echo '<fieldset>';
    echo '<legend>' . __('Date of Birth', 'age-gate') .'</legend>';


    include AGE_GATE_PATH . 'public/partials/form/'. ($this->settings['restrictions']['input_type'] !== 'buttons' ? $this->settings['restrictions']['input_type'] : 'inputs') .'.php';

    echo '</fieldset>';
  }


  function register_style() {
    if(!$this->settings['restrictions']['restrict_register']) return;

    wp_enqueue_style( $this->plugin_name . '-reg', plugin_dir_url( __FILE__ ) . 'css/age-gate-registration.css', array(), AGE_GATE_VERSION, 'all' );
    // wp_enqueue_style( 'custom-login', get_stylesheet_directory_uri() . '/style-login.css' );
    // wp_enqueue_script( 'custom-login', get_stylesheet_directory_uri() . '/style-login.js' );
  }

  /**
   * Validate the additional items on the registration form
   * @param  mixed $errors Default errors
   * @param  string $login  Login username
   * @param  string $email  login email
   * @return mixed 	$errors The original errors with custom additions
   * @since 1.0.0
   * @deprecated 2.0.3
   */
  public function extend_registration_form_show_errors($errors, $login, $email)
  {
    if(!$this->settings['restrictions']['restrict_register']) return $errors;

    $validation = new Age_Gate_Validation;
    $data = $validation->sanitize($_POST);

    if(!isset($data['age_gate'])){
      return $errors;
    }

    $validated = Age_Gate_Validation::is_valid($data['age_gate'], array(
    	'd' => 'required|numeric|min_len,2|max_len,2|max_numeric,31',
      'm' => 'required|numeric|min_len,2|max_len,2|max_numeric,12',
      'y' => 'required|numeric|min_len,4|max_len,4|max_numeric,' . date('Y'),
    ));

    if($validated === true) {
      if ( $this->_ageTest($data['age_gate']) < (int) $this->settings['restrictions']['min_age'] ){
        $errors->add( 'toyoungerror', '<strong>' . __('ERROR', 'age-gate') . '</strong>: '. __('Sorry, you are too young', 'age-gate') );
      }
    } else {
      $errors->add( 'toyoungerror', '<strong>' . __('ERROR', 'age-gate') . '</strong>: '. __('Invalid date of birth', 'age-gate') );
    }



    return $errors;
  }


  /**
   * Store users DOB on registration
   * @param  	int 		$user_id The ID of the user
   * @return 	int 		$user_id The ID of the user
   * @since 	1.0.0
   * @deprecated 2.0.3
   */
  public function extend_registration_user_data( $user_id ) {

    if(!$this->settings['restrictions']['restrict_register']) return;

    $validation = new Age_Gate_Validation;
    $data = $validation->sanitize($_POST);

    if(!isset($data['age_gate'])){
      return $user_id;
    }

    $validated = Age_Gate_Validation::is_valid($data['age_gate'], array(
    	'd' => 'required|numeric|min_len,2|max_len,2|max_numeric,31',
      'm' => 'required|numeric|min_len,2|max_len,2|max_numeric,12',
      'y' => 'required|numeric|min_len,4|max_len,4|max_numeric,' . date('Y'),
    ));

    if($validated === true) {
      update_user_meta( $user_id, 'u_db', $data['age_gate']['y'] . '-' . $data['age_gate']['m'] . '-' . $data['age_gate']['d'] );
      update_user_meta( $user_id, 'user_dob', $data['age_gate'] );
    }

    return $user_id;

  }

  /**
	 * Test the age of the user
	 * @param  mixed $dob Post array
	 * @return int   The int value of the age
	 * @since 		1.0.0
	 * @deprecated 2.0.3
	 */
	private function _ageTest($dob)
	{

		$dob = intval($dob['y']). '-' . str_pad(intval($dob['m']), 2, 0, STR_PAD_LEFT) . '-' . str_pad(intval($dob['d']), 2, 0, STR_PAD_LEFT);

		$from = new DateTime($dob);
		$to   = new DateTime('today');
		return $from->diff($to)->y;


	}

}
