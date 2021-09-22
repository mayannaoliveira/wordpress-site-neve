<?php if (! defined('ABSPATH')) {
    exit('No direct script access allowed');
}

use Jaybizzle\CrawlerDetect\CrawlerDetect;

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
class Age_Gate_Public extends Age_Gate_Common
{
    protected $restricted;

    protected $user_age;
    // protected $ag_serial;
    protected $segment;
    protected $id;
    protected $type;

    protected static $isRestricted = false;
    protected static $errors;
    protected static $submitted;
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    				The version of this plugin.
     */

    public function __construct()
    {
        parent::__construct();


        $this->user_age = $this->_set_user_age();
        // $this->ag_serial = $this->generate_unique_id();

        if (!$this->settings['advanced']['use_js'] && isset($_COOKIE['age_gate_error'])) {
            self::$errors = get_transient($_COOKIE['age_gate_error'] . '_age_gate_error');
            self::$submitted = get_transient($_COOKIE['age_gate_error'] . '_age_gate_submitted');
            setcookie('age_gate_error', '', time() - 3600);
        }
    }

    protected function _get_id($type = 'post')
    {
        switch ($type) {
            case 'category':
            case 'tag':
            case 'tax':
            case 'author':
                return get_queried_object_id();
            break;
            case 'shop':
                return get_option('woocommerce_shop_page_id');
            break;
            case 'home':
                return get_option('page_for_posts');
            break;
            default:
                return get_the_ID();
        }
    }


    protected function _archive_date($type)
    {
        $date = strtotime(get_the_date());

        $archive_date = [
            'year' => date('Y', $date)
        ];

        if ($type !== 'year') {
            $archive_date['month'] = date('m', $date);
        }

        if ($type === 'day') {
            $archive_date['day'] = date('d', $date);
        }
        return $archive_date;
    }


    /**
     * Is this content restricted?
     * @return boolean [description]
     */
    public function is_restricted()
    {
        if (is_customize_preview()) {
            return;
        }

        if (class_exists('\\Elementor\\Plugin')) {
            if (!is_callable([\Elementor\Plugin::$instance->editor, 'is_edit_mode'])) {
                return;
            }

            if (\Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode()) {
                return;
            }
        }


        if ($this->settings['advanced']['use_js']) {
            return;
        }


        $page_type = $this->_screen_type();
        $restriction_type = $this->settings['restrictions']['restriction_type'];
        $will_restrict = $value = ($restriction_type === 'selected');

        $this->type = $page_type;


        /**
         * Id of the current $post
         * @var int
         */
        $this->id = $this->_get_id($this->type);


        /**
         * Properties related to this post
         * @var object
         */
        $this->meta = $this->_get_meta($this->id, $this->type);


        /*
            RETURN FALSE IF

            USER HAS PASSED AGE GATE

            IS A BOT

            ALL CONTENT + BYPASS

            SELECTED CONTENT and NOT RESTRICT

         */
        /*
         Test for use of anonymous mode
         */

        if ($this->settings['advanced']['anonymous_age_gate'] && $this->user_age) {
            $this->age_gate_passed = true;
            $this->restricted = false;
            return;
        }

        if ($this->user_age >= $this->meta->age) {
            $this->age_gate_passed = true;
            $this->restricted = false;
            return;
        }

        if ($this->_is_bot()) {
            $restricted = false;
            $restricted = apply_filters('age_gate_unrestricted_bot', $restricted, $this->meta, ['type' => 'bot']);
            $this->restricted = $restricted;
            // log_message('False from bot');
            return;
        }

        if ($this->settings['restrictions']['restriction_type'] === 'all' && $this->meta->bypass) {
            $restricted = false;
            $restricted = apply_filters('age_gate_unrestricted_bypass', false, $this->meta, ['type' => 'bypass']);
            $this->restricted = $restricted;
            // log_message('False from bypass');
            return;
        }

        // inherit category stuff
        if ($this->settings['restrictions']['restriction_type'] === 'selected' && $this->settings['restrictions']['inherit_category'] && is_single()) {

            // sort so we remove the keys that could confuse
            sort($this->settings['advanced']['inherit_taxonomies']);

            $terms = wp_get_post_terms($this->id, $this->settings['advanced']['inherit_taxonomies']);

            $restrictedTerms = [];
            foreach ($terms as $term) {
                if (get_term_meta($term->term_id, '_age_gate-restrict', true)) {
                    $restrictedTerms[$term->slug] = [
                        'id' => $term->term_id,
                        'slug' => $term->slug,
                        'taxonomy' => $term->taxonomy,
                    ];
                }
            }

            if ($restrictedTerms) {
                $restricted = true;
                $restricted = apply_filters('age_gate_inherited', $restricted, $this->meta, ['type' => 'inherited', 'taxonomies' => $restrictedTerms]);
                $this->restricted = $restricted;
                return;
            }
        }

        if ($this->settings['restrictions']['restriction_type'] === 'all' && $this->settings['restrictions']['inherit_category'] && is_single()) {

            // sort so we remove the keys that could confuse
            sort($this->settings['advanced']['inherit_taxonomies']);

            $terms = wp_get_post_terms($this->id, $this->settings['advanced']['inherit_taxonomies']);

            $bypassTerms = [];
            foreach ($terms as $term) {
                if (get_term_meta($term->term_id, '_age_gate-bypass', true)) {
                    $bypassTerms[$term->slug] = [
                        'id' => $term->term_id,
                        'slug' => $term->slug,
                        'taxonomy' => $term->taxonomy,
                    ];
                }
            }


            if ($bypassTerms) {
                $restricted = false;
                $restricted = apply_filters('age_gate_inherited', $restricted, $this->meta, ['type' => 'inherited', 'taxonomies' => $bypassTerms]);
                $this->restricted = $restricted;
                return;
            }
        }

        if ($this->settings['restrictions']['restriction_type'] === 'selected' && !$this->meta->restrict) {
            $restricted = false;
            $restricted = apply_filters('age_gate_unrestricted_unrestricted', $restricted, $this->meta, ['type' => 'unrestricted']);
            $this->restricted = $restricted;
            // log_message('False from selected and not restricted');
            return;
        }

        if ($this->settings['restrictions']['ignore_logged'] && is_user_logged_in()) {
            $restricted = false;
            $restricted = apply_filters('age_gate_unrestricted_logged_in', $restricted, $this->meta, ['type' => 'logged_in']);
            $this->restricted = $restricted;
            return;
        }



        $restricted = true;
        $restricted = apply_filters('age_gate_restricted', $restricted, $this->meta);
        $this->restricted = $restricted;
        return;
    }

    /**
     * Decide what to show the users
     * @param  string $template The template Wordpress has chosen
     * @return string $template The Template We have chosen
     */
    public function load_template($template)
    {
        // Bypass for divi builder
        if (function_exists('et_fb_enabled') && et_fb_enabled()) {
            return $template;
        }

        // log_message('Entered load_template');

        if ($this->restricted && !$this->settings['advanced']['use_js']) {
            self::$isRestricted = true;
            return AGE_GATE_PATH . 'public/class-age-gate-public-output.php';
        }
        return $template;
    }

    public static function restrictionStatus()
    {
        return self::$isRestricted;
    }


    /**
   * Get the age for currnt page
   * @return [type] [description]
   * @since 2.0.0
   */
    protected function get_age($id, $type)
    {
        $age = '';


        switch ($type) {
            case 'tag':
            case 'category':
            case 'tax':

                if ($this->settings['restrictions']['multi_age']) {
                    $age = get_term_meta($id, '_age_gate-age', true);
                }
            break;
            case 'home':
            case 'shop':
                if ($this->id) {
                    if ($this->settings['restrictions']['multi_age']) {
                        $age = get_post_meta($id, '_age_gate-age', true);
                    }
                }
            break;
            default:
            if ($this->settings['restrictions']['multi_age'] && !is_home() && !is_archive()) {
                $age = get_post_meta($id, '_age_gate-age', true);
            }
        }

        if (!$age) {
            if (self::$language && self::$language->current['language_code'] !== self::$language->default['language_code']) {
                $age = $this->_get_translated_setting('restrictions', 'min_age', self::$language->current['language_code'], true);
            } else {
                $age = $this->settings['restrictions']['min_age'];
            }
            $age = apply_filters('age_gate_age_output', $age, $this->_screen_type());
            return $age;
        }

        return apply_filters('age_gate_age_output', $age, $this->_screen_type());
    }

    /**
     * [_get_meta description]
     * @return [type] [description]
     */
    protected function _get_meta($id, $type = 'post')
    {
        $meta = [
            'age' => $this->get_age($id, $type),
            'type' => $type
        ];


        switch ($type) {
            case 'tag':
            case 'category':
            case 'tax':
                $meta = array_merge($meta, [
                    'bypass' => get_term_meta($id, '_age_gate-bypass', true),
                    'restrict' => get_term_meta($id, '_age_gate-restrict', true),
                ]);

            break;
            case 'day':
            case 'month':
            case 'year':
                $meta = array_merge($meta, apply_filters(
                    "age_gate_archive_{$type}",
                    [
                        'bypass' => false,
                        'restrict' => false,
                    ],
                    [
                        'date' => $this->_archive_date($type)
                    ]
                ));
            break;
            case 'author':
                $meta = array_merge($meta, apply_filters(
                    "age_gate_archive_{$type}",
                    [
                        'bypass' => false,
                        'restrict' => false,
                    ],
                    [
                        'id' => $id
                    ]
                ));
            break;
            case 'archive':
                $meta = array_merge($meta, apply_filters(
                    "age_gate_archive_{$type}",
                    [
                        'bypass' => false,
                        'restrict' => false,
                    ],
                    []
                ));
            break;
            case 'notfound':
                $meta = array_merge($meta, apply_filters(
                    "age_gate_archive_{$type}",
                    [
                        'bypass' => 1,
                        'restrict' => 0,
                    ],
                    []
                ));
            break;
            case 'home':
                // do we have a page ID?
                // If yes, we take the settings from the page
                if ($this->id) {
                    $meta = array_merge($meta, [
                        'bypass' => get_post_meta($id, '_age_gate-bypass', true),
                        'restrict' => get_post_meta($id, '_age_gate-restrict', true),
                    ]);
                } else {
                    // Otherwise, the posts is also home
                    if ($this->settings['restrictions']['restriction_type'] === 'selected') {
                        $meta = array_merge($meta, [
                            'bypass' => false,
                            'restrict' => false,
                        ]);
                    } else {
                        $meta = array_merge($meta, [
                            'bypass' => false,
                            'restrict' => true,
                        ]);
                    }
                }
            break;
            default:
                $meta = array_merge($meta, [
                    'bypass' => get_post_meta($id, '_age_gate-bypass', true),
                    'restrict' => get_post_meta($id, '_age_gate-restrict', true),
                ]);
        }

        $meta = array_merge($meta, [
            'restrictions' => (object) $this->settings['restrictions']
        ]);


        return (object) $meta;
    }


    /**
     * Add script tag for when in customiser
     */
    public function customiser_js_disable()
    {
        if (is_customize_preview()) {
            echo "<script>var ag_customiser = true;</script>";
        }

        if (class_exists('\\Elementor\\Plugin') && !is_customize_preview()) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode()) {
                echo "<script>var ag_customiser = true;</script>";
            }
        }
    }



    /**
     * [generate_unique description]
     * @return [type] [description]
     * @deprecated 2.1.0
     */
    public function generate_unique_id()
    {
        if (!$serial = get_option('age_gate_serial')) {
            $serial = abs(2147483648 + mt_rand(-2147482448, 2147483647) * mt_rand(-2147482448, 2147483647));
            update_option('age_gate_serial', $serial);
        }


        return $serial;
    }


    /**
     * [_is_bot description]
     * @return boolean [description]
     * @since 2.0.0
     */
    protected function _is_bot()
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


    /**
     * Action hook for failures
     * @return [type] [description]
     */
    public function age_gate_failed()
    {
        if (!$this->settings['restrictions']['rechallenge']) {
            setcookie('age_gate_failed', 1, 0, COOKIEPATH, COOKIE_DOMAIN);
        }
    }

    private function _set_user_age()
    {
        $cookieName = $this->get_cookie_name();
        return (isset($_COOKIE[$cookieName])) ? $_COOKIE[$cookieName] : null;
    }


    public function toolbar_link_to_mypage($wp_admin_bar)
    {

        // Don't run in admin or if the admin bar isn't showing
        if (is_admin() || ! is_admin_bar_showing()) {
            return;
        }

        if ($this->settings['advanced']['full_nav'] === 'off') {
            return;
        }

        if (current_user_can(AGE_GATE_CAP_RESTRICTIONS)) {
            $page = 'age-gate';
        } elseif (current_user_can(AGE_GATE_CAP_MESSAGING)) {
            $page = 'age-gate-messaging';
        } elseif (current_user_can(AGE_GATE_CAP_APPEARANCE)) {
            $page = 'age-gate-appearance';
        } elseif (current_user_can(AGE_GATE_CAP_ADVANCED)) {
            $page = 'age-gate-advanced';
        } elseif (current_user_can(AGE_GATE_CAP_ACCESS)) {
            $page = 'age-gate-access';
        } else {
            return;
        }

        $type = $this->_screen_type();
        $id = $this->_get_id($type);
        $meta = $this->_get_meta($id, $type);

        if ($this->settings['restrictions']['restriction_type'] === 'all' && $meta->bypass || $this->settings['restrictions']['restriction_type'] === 'selected' && !$meta->restrict) {
            return;
        }

        // add a parent/top-level node
        $args = array('id' => 'age-gate', 'title' => '<span class="screen-reader-text">' . __('Age Gate', 'age-gate') . '</span>', 'href' => add_query_arg('page', $page, admin_url('admin.php')));
        $wp_admin_bar->add_node($args);

        $cookieName = $this->get_cookie_name();
        $toggle = (isset($_COOKIE[$cookieName]) && $_COOKIE[$cookieName] ? 'show' : 'hide');
        $toggleText = (isset($_COOKIE[$cookieName]) && $_COOKIE[$cookieName] ? __('Show Age Gate', 'age-gate') : __('Hide Age Gate', 'age-gate'));


        $args = array(
            'id' => 'age-gate-toggle',
            'title' => $toggleText,
            'parent' => 'age-gate',
            'href' => add_query_arg(
                [
                    'page' => 'age-gate',
                    'ag_switch' => $toggle,
                    '_wpnonce' => wp_create_nonce('age-gate-toggle')
                ],
                admin_url('admin.php')
            )
        );

        $wp_admin_bar->add_node($args);
    }

    protected function _screen_type()
    {
        $type = 'notfound';

        if (is_page()) {
            $type = is_front_page() ? 'front' : 'page';
        } elseif (is_home()) {
            $type = 'home';
        } elseif (is_single()) {
            if (function_exists('is_product')) {
                $type = (is_attachment()) ? 'attachment' : 'product';
            } else {
                $type = (is_attachment()) ? 'attachment' : 'single';
            }
        } elseif (is_category()) {
            $type = 'category';
        } elseif (is_tag()) {
            $type = 'tag';
        } elseif (is_tax()) {
            $type = 'tax';
        } elseif (is_archive()) {
            if (is_day()) {
                $type = 'day';
            } elseif (is_month()) {
                $type = 'month';
            } elseif (is_year()) {
                $type = 'year';
            } elseif (is_author()) {
                $type = 'author';
            } elseif (function_exists('is_shop') && is_shop()) {
                $type = 'shop';
            } else {
                $type = 'archive';
            }
        } elseif (is_search()) {
            $type = 'search';
        } elseif (is_404()) {
            $type = 'notfound';
        }

        return $type;
    }



    /**
     * Aplpy filters to our $errors
     * @param  [type] $errors [description]
     * @return [type]         [description]
     */
    protected function _filter_errors($errors)
    {
        $new_errors = [];
        if (!class_exists('Parsedown')) {
            require_once AGE_GATE_PATH . 'includes/Parsedown.php';
        }

        $parsedown = new Parsedown();
        foreach ($errors as $field => $error) {
            $new_errors[$field] = apply_filters('age_gate_error_' . $field, $parsedown->line($error['message']), $error['rule']);
        }

        return $new_errors;
    }

    public static function age_gate_error($key)
    {
        $opt = get_option('wp_age_gate_advanced', array());

        if (!class_exists('Parsedown')) {
            require_once AGE_GATE_PATH . 'includes/Parsedown.php';
        }

        $parsedown = new Parsedown();

        if (!$opt['use_js'] && is_array(self::$errors) && array_key_exists($key, self::$errors)) {
            return '<p class="age-gate-error-message">' . $parsedown->line(strip_tags(__(self::$errors[$key]))) . '</p>';
        }

        if ($opt['use_js']) {
            return '<div class="age-gate-error" data-error-field="'.$key.'"></div>';
        }
    }

    public static function age_gate_set_value($key)
    {
        $opt = get_option('wp_age_gate_advanced', array());

        if (!$opt['use_js'] && is_array(self::$submitted) && array_key_exists($key, self::$submitted)) {
            return self::$submitted[$key];
        }
    }

    protected function getRedirectUrl()
    {
        $refererUrl = wp_get_referer();
        $url = parse_url($refererUrl);

        $refererUrl = isset($url['path']) ? home_url($url['path']) : home_url();
        $redirect = false;

        if ($id = url_to_postid($refererUrl)) {
            $redirect = get_permalink($id);
        }

        if ($redirect) {
            return wp_validate_redirect($redirect, home_url());
        }

        foreach (get_terms() as $term) {
            $termLink = @get_term_link($term->term_id);
            if ($termLink === $refererUrl) {
                $redirect = $termLink;
            }
        }

        if ($redirect) {
            return wp_validate_redirect($redirect, home_url());
        }

        $postTypes = get_post_types(
            [
                'public' => true
            ]
        );

        foreach ($postTypes as $postType) {
            $ptUrl = get_post_type_archive_link($postType);
            if ($ptUrl === $refererUrl) {
                $redirect = $ptUrl;
            }
        }

        if ($redirect) {
            return wp_validate_redirect($redirect, home_url());
        }

        preg_match('/' . str_replace('/', '\/', trailingslashit(site_url())) . '([0-9]{4})\/?([0-9]{2})?\/?([0-9]+)?/', $refererUrl, $matches);

        if ($matches) {
            $redirect = $matches[0];
        }

        if ($redirect) {
            return wp_validate_redirect($redirect, home_url());
        }

        return home_url();
    }
}


if (!function_exists('age_gate_error')) {
    function age_gate_error($key)
    {
        return Age_Gate_Public::age_gate_error($key);
    }
}

if (!function_exists('age_gate_set_value')) {
    function age_gate_set_value($key)
    {
        return Age_Gate_Public::age_gate_set_value($key);
    }
}

if (!function_exists('age_gate_status')) {
    function age_gate_status()
    {
        return Age_Gate_Public::restrictionStatus();
    }
}
