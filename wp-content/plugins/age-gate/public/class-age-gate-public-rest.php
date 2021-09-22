<?php if (! defined('ABSPATH')) {
    exit('No direct script access allowed');
}

class Age_Gate_Rest extends \WP_REST_Controller
{
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'registerRestEndpoints']);
    }

    public function registerRestEndpoints()
    {
        register_rest_route('age-gate/v1', '/shortcode', array(
            'methods' => 'GET',
            'callback' => array($this, 'shortCodeTest'),
            'permission_callback' => '__return_true'
        ));

        // rest route for filter
        // register_rest_route('age-gate/v1', '/filter', array(
        //     'methods' => 'GET',
        //     'callback' => array($this, 'age_gate_filters_rest')

        // ));
    }

    public function shortCodeTest($params)
    {
        $validation = new Age_Gate_Validation();
        $submission = $validation->sanitize($params->get_param('age_gate'));

        $type = isset($_GET['confirm_action']) ? 'buttons' : 'inputs';
        $requiredAge = (int) base64_decode(base64_decode($submission['age']));

        switch ($type) {
            case 'buttons':
                $age = $requiredAge;
                $status = $params->get_param('confirm_action') == 1 ? 'pass' : 'fail';
                break;
            default:
                $age = $this->calcAge($submission);

                if ($age === 'invalid') {
                    $status = 'fail';
                } else {
                    $status = $age >= $requiredAge ? 'pass' : 'fail';
                }
                break;
        }

        $messages = get_option('wp_age_gate_messages', []);

        return new \WP_REST_Response(
            [
                'age' => $age,
                'status' => $status,
                'error' => $status === 'fail' ? ($age === 'invalid' ? $messages['invalid_input_msg'] : $messages['under_age_msg']) : '',
            ]
        );
    }

    public function calcAge($date)
    {
        if (intval($date['y']) >= date('Y')) {
            return 0;
        }

        // wp_die(date_default_timezone_get());

        $dob = intval($date['y']). '-' . str_pad(intval($date['m']), 2, 0, STR_PAD_LEFT) . '-' . str_pad(intval($date['d']), 2, 0, STR_PAD_LEFT);

        $tz = get_option('timezone_string');

        if (empty($tz)) {
            $tz = date_default_timezone_get();
        }

        $timezone = new DateTimeZone($tz);

        try {
            $from = new DateTime($dob, $timezone);
            $to   = new DateTime('today', $timezone);
            return $from->diff($to)->y;
        } catch (Exception $e) {
            return 'invalid';
        }
    }
}
