<?php if (! defined('ABSPATH')) {
    exit('No direct script access allowed');
}

use Jaybizzle\CrawlerDetect\CrawlerDetect;

class Age_Gate_Shortcode
{
    protected $messages;

    private $types = [
        'inputs',
        'buttons',
        'selects',
    ];

    public function __construct()
    {
        add_shortcode('age-gate', [$this, 'addShortcode']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueShortcode']);
    }

    public function enqueueShortcode()
    {
        wp_enqueue_script('age-gate-sc', AGE_GATE_URL . 'public/js/age-gate-shortcode.js', ['jquery'], AGE_GATE_VERSION, true);
    }

    public function addShortcode($atts, $content)
    {
        $atts = shortcode_atts(
            [
                'age' => false,
                'title' => false,
                'type' => false,
            ],
            $atts,
            'age-gate'
        );

        $html = '';

        $this->settings = $this->getDefaultSettings();

        if ($this->isBot()) {
            return $content;
        }

        $this->messages = $this->formatOptions((object) $this->settings['messages']);
        $this->restrictions = (object) $this->settings['restrictions'];

        if (!$atts['age']) {
            $atts['age'] = $this->settings['restrictions']['min_age'];
        }

        if ($atts['title'] === false) {
            $atts['title'] = sprintf($this->settings['messages']['messaging'], $atts['age']);
        }

        $this->age = $atts['age'];

        if ($atts['type'] && in_array($atts['type'], $this->types)) {
            $this->settings['restrictions']['input_type'] = $atts['type'];
        }

        ob_start();
        include AGE_GATE_PATH . 'public/partials/form/shortcode.php';
        $html = ob_get_clean();

        return $html;
    }

    public function getDefaultSettings()
    {
        $settings = apply_filters('ag_settings', array());
        return array_merge($settings, array(
            'restrictions' => get_option('wp_age_gate_restrictions', array()),
            'messages' => get_option('wp_age_gate_messages', array()),
            'validation' => get_option('wp_age_gate_validation_messages', array()),
            'appearance' => get_option('wp_age_gate_appearance', array()),
            'access' => get_option('wp_age_gate_access', array()),
            'advanced' => get_option('wp_age_gate_advanced', array()),
        ));
    }
    

    private function formatOptions($options)
    {
        $msgs = [
            'headline' => $options->instruction,
            'subheadline' => $options->messaging,
            'errors' => (object) [
                'invalid' => $options->invalid_input_msg,
                'failed' => $options->under_age_msg,
                'generic' => $options->generic_error_msg,
            ],
            'remember' => $options->remember_me_text,
            'buttons' => (object) [
                'message' => $options->yes_no_message,
                'yes' => $options->yes_text,
                'no' => $options->no_text,
            ],
            'labels' => (object) [
                'day' => $options->text_day,
                'month' => $options->text_month,
                'year' => $options->text_year
            ],
            'additional' => $options->additional,
            'submit' => $options->button_text
        ];

        return (object) $msgs;
    }

    protected function isBot()
    {
        // test user defined bots
        if (is_array($this->settings['advanced']['custom_bots']) && isset($_SERVER['HTTP_USER_AGENT'])) {
            if (in_array($_SERVER['HTTP_USER_AGENT'], $this->settings['advanced']['custom_bots'])) {
                return true;
            }
        }
        $CrawlerDetect = new CrawlerDetect;
        return $CrawlerDetect->isCrawler();
    }
}
