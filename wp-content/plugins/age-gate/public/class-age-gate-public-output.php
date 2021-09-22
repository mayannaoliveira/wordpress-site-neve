<?php if (! defined('ABSPATH')) {
    exit('No direct script access allowed');
}

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://agegate.io
 * @since      1.0.0
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
class Age_Gate_Output extends Age_Gate_Public
{
    private $messages;
    private $restrictions;
    private $appearance;
    private $age;
    private $js;

    protected $id;
    protected $type;

    protected $post_to;


    public function __construct()
    {
        parent::__construct();

        if (self::$language) {
            $this->_localise_settings();
        }

        if (!class_exists('Parsedown')) {
            require_once AGE_GATE_PATH . 'includes/Parsedown.php';
        }

        $this->parsedown = new Parsedown();

        foreach ($this->settings as $key => $setting) {
            if ($key !== 'access' && $key !== 'advanced') {
                $this->{$key} = (object) $setting;
            }
        }

        $this->messages = $this->_format_options($this->messages);

        $page_type = $this->_screen_type();

        $this->type = $page_type;//($page_type === 'category' || $page_type === 'tag') ? 'term' : 'post';

        $this->post_to = $this->post_to();

        /**
         * Id of the current $post
         * @var int
         */

        $this->id = $this->_get_id($this->type);
        // wp_die($this->id);
        $this->age = $this->get_age($this->id, $this->type);
        $this->js = $this->settings['advanced']['use_js'];

        if (self::$language && self::$language->current['language_code'] !== self::$language->default['language_code']) {
            $lang = self::$language->current['language_code'];
            if (isset($this->restrictions->lang[$lang]['date_format']) && !empty($this->restrictions->lang[$lang]['date_format'])) {
                $this->restrictions->date_format = $this->restrictions->lang[$lang]['date_format'];
            }
        }
    }


    public function post_to()
    {
        // always post to self
        // if ($this->settings['advanced']['post_to_self']) {
        global $wp;
        return home_url($wp->request);
        // } else {
            // return esc_url(admin_url('admin-post.php'));
        // }
    }


    /**
     * Display the HTML for user messaging
     * @return string HTML content
     */
    public function display_messages()
    {
        $html = '';
        if ($this->messages->headline) {
            $html .= '<h2 class="age-gate-subheading">'. sprintf(esc_html(__($this->messages->headline)), $this->age) . '</h2>';
        }
        if ($this->messages->subheadline) {
            $html .= '<p class="age-gate-message">' . sprintf(esc_html(__($this->messages->subheadline)), $this->age) .'</p>';
        }
        // $html .= 'Hi';

        return $html;
    }


    /**
     * Display the Logo registered
     * @return string HTML content
     */
    public function display_logo()
    {
        $logo = wp_get_attachment_url($this->appearance->logo);
        $class = ($logo) ? ' age-gate-logo' : '';
        $content = ($logo ? '<img src="' . $logo . '" alt="'. get_bloginfo('name') .'" class="age-gate-logo-image" />' : get_bloginfo('name'));

        return sprintf('<h1 class="age-gate-heading%s">%s</h1>', $class, $content);
    }

    /**
     * Rendee the final template
     * @return [type] [description]
     */
    public function render()
    {
        include_once AGE_GATE_PATH . 'public/partials/age-gate-public-display.php';
    }

    private function _format_options($options)
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




    private function _check_filtered($data)
    {
        if (gettype($data) !== 'string') {
            return '<p>' . __('Incorrect content type. String expected.', 'age-gate') . '</p>';
        }

        if (strpos($data, 'name="age_gate') !== false) {
            return '<p>' . __('Content contains disallowed inputs. Do not use age_gate as a name.', 'age-gate') . '</p>';
        }

        return $data;
    }

    /**
     * Choose the lanuage for the Settings
     * @return @mixed
     * @since 2.1.0
     */
    private function _localise_settings()
    {
        foreach ($this->settings['messages'] as $key => $value) {
            if ($key !== 'lang') {
                $this->settings['messages'][$key] = $this->_get_translated_setting('messages', $key, self::$language->current['language_code']);
            }
            // code...self::$language->current['language_code']
        }

        // if(!isset($this->settings['messages']['lang'])) return;
    // if(self::$language->current['language_code'] === self::$language->default['language_code']) return;
    //
    // $language = $this->settings['messages']['lang'][self::$language->current['language_code']];
    // unset($this->settings['messages']['lang']);
    // $this->settings['messages'] = array_merge($this->settings['messages'], $language);

    // return $this->settings;
    }
}

$agegate = new Age_Gate_Output;
$agegate->render();
