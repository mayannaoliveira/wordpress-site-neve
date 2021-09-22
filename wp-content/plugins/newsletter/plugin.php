<?php

/*
  Plugin Name: Newsletter
  Plugin URI: https://www.thenewsletterplugin.com/plugins/newsletter
  Description: Newsletter is a cool plugin to create your own subscriber list, to send newsletters, to build your business. <strong>Before update give a look to <a href="https://www.thenewsletterplugin.com/category/release">this page</a> to know what's changed.</strong>
  Version: 7.2.7
  Author: Stefano Lissa & The Newsletter Team
  Author URI: https://www.thenewsletterplugin.com
  Disclaimer: Use at your own risk. No warranty expressed or implied is provided.
  Text Domain: newsletter
  License: GPLv2 or later
  Requires at least: 4.6
  Requires PHP: 5.6
 
  Copyright 2009-2021 The Newsletter Team (email: info@thenewsletterplugin.com, web: https://www.thenewsletterplugin.com)

  Newsletter is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 2 of the License, or
  any later version.

  Newsletter is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with Newsletter. If not, see https://www.gnu.org/licenses/gpl-2.0.html.

 */

if (version_compare(phpversion(), '5.6', '<')) {
    add_action('admin_notices', function () {
        echo '<div class="notice notice-error"><p>PHP version 5.6 or greater is required for Newsletter. Ask your provider to upgrade. <a href="https://www.php.net/supported-versions.php" target="_blank">Read more on PHP versions</a></p></div>';
    });
    return;
}

define('NEWSLETTER_VERSION', '7.2.7');

global $newsletter, $wpdb;

if (!defined('NEWSLETTER_DARK'))
    define('NEWSLETTER_DARK', false);

if (!defined('NEWSLETTER_BETA'))
    define('NEWSLETTER_BETA', false);

// For acceptance tests, DO NOT CHANGE
if (!defined('NEWSLETTER_DEBUG'))
    define('NEWSLETTER_DEBUG', false);

if (!defined('NEWSLETTER_EXTENSION_UPDATE'))
    define('NEWSLETTER_EXTENSION_UPDATE', true);

if (!defined('NEWSLETTER_EMAILS_TABLE'))
    define('NEWSLETTER_EMAILS_TABLE', $wpdb->prefix . 'newsletter_emails');

if (!defined('NEWSLETTER_USERS_TABLE'))
    define('NEWSLETTER_USERS_TABLE', $wpdb->prefix . 'newsletter');

if (!defined('NEWSLETTER_STATS_TABLE'))
    define('NEWSLETTER_STATS_TABLE', $wpdb->prefix . 'newsletter_stats');

if (!defined('NEWSLETTER_SENT_TABLE'))
    define('NEWSLETTER_SENT_TABLE', $wpdb->prefix . 'newsletter_sent');

define('NEWSLETTER_SLUG', 'newsletter');

define('NEWSLETTER_DIR', __DIR__);
define('NEWSLETTER_INCLUDES_DIR', __DIR__ . '/includes');

define('NEWSLETTER_URL', WP_PLUGIN_URL . '/newsletter');

if (!defined('NEWSLETTER_LIST_MAX'))
    define('NEWSLETTER_LIST_MAX', 40);

if (!defined('NEWSLETTER_PROFILE_MAX'))
    define('NEWSLETTER_PROFILE_MAX', 20);

if (!defined('NEWSLETTER_FORMS_MAX'))
    define('NEWSLETTER_FORMS_MAX', 10);

if (!defined('NEWSLETTER_CRON_INTERVAL'))
    define('NEWSLETTER_CRON_INTERVAL', 300);

if (!defined('NEWSLETTER_HEADER'))
    define('NEWSLETTER_HEADER', true);

require_once NEWSLETTER_INCLUDES_DIR . '/module.php';
require_once NEWSLETTER_INCLUDES_DIR . '/TNP.php';

class Newsletter extends NewsletterModule {

    // Limits to respect to avoid memory, time or provider limits
    var $time_start;
    var $time_limit;
    var $email_limit = 10; // Per run, every 5 minutes
    var $limits_set = false;
    var $max_emails = 20;

    /**
     * @var PHPMailer
     */
    var $mailer;
    // Message shown when the interaction is inside a WordPress page
    var $message;
    var $user;
    var $error;
    var $theme;
    // Theme autocomposer variables
    var $theme_max_posts;
    var $theme_excluded_categories; // comma separated ids (eventually negative to exclude)
    var $theme_posts; // WP_Query object
    var $action = '';

    /**  @var Newsletter */
    static $instance;

    const MAX_CRON_SAMPLES = 100;
    const STATUS_NOT_CONFIRMED = 'S';
    const STATUS_CONFIRMED = 'C';

    /**
     * @return Newsletter
     */
    static function instance() {
        if (self::$instance == null) {
            self::$instance = new Newsletter();
        }
        return self::$instance;
    }

    function __construct() {

        // Grab it before a plugin decides to remove it.
        if (isset($_GET['na'])) {
            $this->action = $_GET['na'];
        }
        if (isset($_POST['na'])) {
            $this->action = $_POST['na'];
        }

        $this->time_start = time();

        // Here because the upgrade is called by the parent constructor and uses the scheduler
        add_filter('cron_schedules', function ($schedules) {
            $schedules['newsletter'] = array(
                'interval' => NEWSLETTER_CRON_INTERVAL, // seconds
                'display' => 'Every ' . NEWSLETTER_CRON_INTERVAL . ' seconds by Newsletter'
            );
            return $schedules;
        }, 1000);

        parent::__construct('main', '1.6.6', null, array('info', 'smtp'));

        $max = $this->options['scheduler_max'];
        if (!is_numeric($max)) {
            $max = 100;
        }
        $this->max_emails = max(floor($max / (3600 / NEWSLETTER_CRON_INTERVAL)), 1);

        add_action('plugins_loaded', array($this, 'hook_plugins_loaded'));
        add_action('init', array($this, 'hook_init'), 1);
        add_action('wp_loaded', array($this, 'hook_wp_loaded'), 1);

        add_action('newsletter', array($this, 'hook_newsletter'), 1);

        $this->update_cron_stats();

        register_activation_hook(__FILE__, array($this, 'hook_activate'));
        register_deactivation_hook(__FILE__, array($this, 'hook_deactivate'));

        add_action('admin_init', array($this, 'hook_admin_init'));

        if (is_admin()) {
            add_action('admin_head', array($this, 'hook_admin_head'));

            // Protection against strange schedule removal on some installations
            if (!wp_next_scheduled('newsletter') && (!defined('WP_INSTALLING') || !WP_INSTALLING)) {
                wp_schedule_event(time() + 30, 'newsletter', 'newsletter');
            }

            add_action('admin_menu', array($this, 'add_extensions_menu'), 90);

            add_filter('display_post_states', array($this, 'add_notice_to_chosen_profile_page_hook'), 10, 2);

            if ($this->is_admin_page()) {
                // Remove the emoji replacer to save to database the original emoji characters (see even woocommerce for the same problem)
                remove_action('admin_print_scripts', 'print_emoji_detection_script');
                add_action('admin_enqueue_scripts', array($this, 'hook_wp_admin_enqueue_scripts'));
            }

            add_action('wp_ajax_tnp_hide_promotion', function () {
                update_option('newsletter_promotion', $_POST['id']);
                die();
            });
        }
    }
    
    function hook_plugins_loaded() {

        do_action('newsletter_loaded', NEWSLETTER_VERSION);

        if (function_exists('load_plugin_textdomain')) {
            load_plugin_textdomain('newsletter', false, plugin_basename(dirname(__FILE__)) . '/languages');
        }
    }    

    function hook_init() {
        global $wpdb;

        if (isset($this->options['debug']) && $this->options['debug'] == 1) {
            ini_set('log_errors', 1);
            ini_set('error_log', WP_CONTENT_DIR . '/logs/newsletter/php-' . date('Y-m') . '-' . get_option('newsletter_logger_secret') . '.txt');
        }

        add_shortcode('newsletter_replace', array($this, 'shortcode_newsletter_replace'));

        add_filter('site_transient_update_plugins', array($this, 'hook_site_transient_update_plugins'));

        if (is_admin()) {
            if (!class_exists('NewsletterExtensions')) {

                add_filter('plugin_row_meta', function ($plugin_meta, $plugin_file) {

                    static $slugs = array();
                    if (empty($slugs)) {
                        $addons = $this->getTnpExtensions();
                        if ($addons) {
                            foreach ($addons as $addon) {
                                $slugs[] = $addon->wp_slug;
                            }
                        }
                    }
                    if (array_search($plugin_file, $slugs) !== false) {

                        $plugin_meta[] = '<a href="admin.php?page=newsletter_main_extensions" style="font-weight: bold">Newsletter Addons Manager required</a>';
                    }
                    return $plugin_meta;
                }, 10, 2);
            }

            add_action('in_admin_header', array($this, 'hook_in_admin_header'), 1000);

            if ($this->is_admin_page()) {

                $dismissed = get_option('newsletter_dismissed', array());

                if (isset($_GET['dismiss'])) {
                    $dismissed[$_GET['dismiss']] = 1;
                    update_option('newsletter_dismissed', $dismissed);
                    wp_redirect($_SERVER['HTTP_REFERER']);
                    exit();
                }
            }
        } else {
            add_action('wp_enqueue_scripts', array($this, 'hook_wp_enqueue_scripts'));
        }

        do_action('newsletter_init');
    }

    function hook_wp_loaded() {
        if (empty($this->action)) {
            return;
        }

        if ($this->action == 'test') {
            // This response is tested, do not change it!
            echo 'ok';
            die();
        }

        if ($this->action === 'nul') {
            $this->dienow('This link is not active on newsletter preview', 'You can send a test message to test subscriber to have the real working link.');
        }

        $user = $this->get_user_from_request();
        $email = $this->get_email_from_request();
        do_action('newsletter_action', $this->action, $user, $email);
    }

    function update_cron_stats() {
        if (defined('DOING_CRON') && DOING_CRON) {
            $calls = get_option('newsletter_diagnostic_cron_calls', []);
            if (!is_array($calls)) $calls = []; // Protection agains scrambled options or bad written caching DB caching plugin
            $calls[] = time();
            if (count($calls) > self::MAX_CRON_SAMPLES) {
                array_shift($calls);
            }
            update_option('newsletter_diagnostic_cron_calls', $calls, false);

            if (count($calls) > 50) {
                $mean = 0;
                $max = 0;
                $min = 0;
                for ($i = 1; $i < count($calls); $i++) {
                    $diff = $calls[$i] - $calls[$i - 1];
                    $mean += $diff;
                    if ($min == 0 || $min > $diff) {
                        $min = $diff;
                    }
                    if ($max < $diff) {
                        $max = $diff;
                    }
                }
                $mean = $mean / count($calls) - 1;
                update_option('newsletter_diagnostic_cron_data', array('mean' => $mean, 'max' => $max, 'min' => $min), false);
            } else {
                update_option('newsletter_diagnostic_cron_data', '', false);
            }
        }
    }

    function hook_activate() {
        // Ok, why? When the plugin is not active WordPress may remove the scheduled "newsletter" action because
        // the every-five-minutes schedule named "newsletter" is not present.
        // Since the activation does not forces an upgrade, that schedule must be reactivated here. It is activated on
        // the upgrade method as well for the user which upgrade the plugin without deactivte it (many).
        if (!wp_next_scheduled('newsletter')) {
            wp_schedule_event(time() + 30, 'newsletter', 'newsletter');
        }

        $install_time = get_option('newsletter_install_time');
        if (!$install_time) {
            update_option('newsletter_install_time', time(), false);
        }

        touch(NEWSLETTER_LOG_DIR . '/index.html');

        Newsletter::instance()->upgrade();
        NewsletterUsers::instance()->upgrade();
        NewsletterEmails::instance()->upgrade();
        NewsletterSubscription::instance()->upgrade();
        NewsletterStatistics::instance()->upgrade();
        NewsletterProfile::instance()->upgrade();
    }

    function first_install() {
        parent::first_install();
        update_option('newsletter_show_welcome', '1', false);
    }

    function upgrade() {
        global $wpdb, $charset_collate;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        parent::upgrade();


        $sql = "CREATE TABLE `" . $wpdb->prefix . "newsletter_emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(10) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `message` longtext,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('new','sending','sent','paused','error') NOT NULL DEFAULT 'new',
  `total` int(11) NOT NULL DEFAULT '0',
  `last_id` int(11) NOT NULL DEFAULT '0',
  `sent` int(11) NOT NULL DEFAULT '0',
  `track` int(11) NOT NULL DEFAULT '1',
  `list` int(11) NOT NULL DEFAULT '0',
  `type` varchar(50) NOT NULL DEFAULT '',
  `query` longtext,
  `editor` tinyint(4) NOT NULL DEFAULT '0',
  `sex` varchar(20) NOT NULL DEFAULT '',
  `theme` varchar(50) NOT NULL DEFAULT '',
  `message_text` longtext,
  `preferences` longtext,
  `send_on` int(11) NOT NULL DEFAULT '0',
  `token` varchar(10) NOT NULL DEFAULT '',
  `options` longtext,
  `private` tinyint(1) NOT NULL DEFAULT '0',
  `click_count` int(10) unsigned NOT NULL DEFAULT '0',
  `version` varchar(10) NOT NULL DEFAULT '',
  `open_count` int(10) unsigned NOT NULL DEFAULT '0',
  `unsub_count` int(10) unsigned NOT NULL DEFAULT '0',
  `error_count` int(10) unsigned NOT NULL DEFAULT '0',
  `stats_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) $charset_collate;";

        dbDelta($sql);

        // WP does not manage composite primary key when it tries to upgrade a table...
        $suppress_errors = $wpdb->suppress_errors(true);

        dbDelta("CREATE TABLE " . $wpdb->prefix . "newsletter_sent (
            email_id int(10) unsigned NOT NULL DEFAULT '0',
            user_id int(10) unsigned NOT NULL DEFAULT '0',
            status tinyint(1) unsigned NOT NULL DEFAULT '0',
            open tinyint(1) unsigned NOT NULL DEFAULT '0',
            time int(10) unsigned NOT NULL DEFAULT '0',
            error varchar(255) NOT NULL DEFAULT '',
	    ip varchar(100) NOT NULL DEFAULT '',
            PRIMARY KEY (email_id,user_id),
            KEY user_id (user_id),
            KEY email_id (email_id)
          ) $charset_collate;");
        $wpdb->suppress_errors($suppress_errors);

        // Some setting check to avoid the common support request for mis-configurations
        $options = $this->get_options();

        if (empty($options['scheduler_max']) || !is_numeric($options['scheduler_max'])) {
            $options['scheduler_max'] = 100;
            $this->save_options($options);
        }

        wp_clear_scheduled_hook('newsletter');
        wp_schedule_event(time() + 30, 'newsletter', 'newsletter');

        wp_clear_scheduled_hook('newsletter_extension_versions');
        // No more required
        //wp_schedule_event(time() + 30, 'daily', 'newsletter_extension_versions');

        $subscription_options = get_option('newsletter', array());

        // Settings migration
        if (empty($this->options['page'])) {
            if (isset($subscription_options['page']))
                $this->options['page'] = $subscription_options['page'];
            $this->save_options($this->options);
        }

        if (empty($this->options['css']) && !empty($subscription_options['css'])) {
            $this->options['css'] = $subscription_options['css'];
            $this->save_options($this->options);
        }

        // Migration of "info" options
        $info_options = $this->get_options('info');
        if (!empty($this->options['header_logo']) && empty($info_options['header_logo'])) {
            $info_options = $this->options;
            $this->save_options($info_options, 'info');
        }

        if (!empty($this->options['editor'])) {
            if (empty($this->options['roles'])) {
                $this->options['roles'] = array('editor');
                unset($this->options['editor']);
            }
            $this->save_options($this->options);
        }

        // Clear the addons and license caches
        delete_transient('newsletter_license_data');
        delete_transient("tnp_extensions_json");

        touch(NEWSLETTER_LOG_DIR . '/index.html');

        return true;
    }

    function is_allowed() {
        if (current_user_can('administrator')) {
            return true;
        }
        if (!empty($this->options['roles'])) {
            foreach ($this->options['roles'] as $role) {
                if (current_user_can($role)) {
                    return true;
                }
            }
        }
        return false;
    }

    function admin_menu() {
        if (!$this->is_allowed()) {
            return;
        }

        add_menu_page('Newsletter', 'Newsletter', 'exist', 'newsletter_main_index', '', plugins_url('newsletter') . '/images/menu-icon.png', '30.333');

        $this->add_menu_page('index', __('Dashboard', 'newsletter'));
        $this->add_admin_page('info', __('Company info', 'newsletter'));

        if (current_user_can('administrator')) {
            $this->add_menu_page('welcome', __('Welcome', 'newsletter'));
            $this->add_menu_page('main', __('Settings', 'newsletter'));
            
            // Pages not on menu
            $this->add_admin_page('smtp', 'SMTP');
            $this->add_admin_page('status', __('Status', 'newsletter'));
            $this->add_admin_page('delivery', __('Delivery Diagnostic', 'newsletter'));
            $this->add_admin_page('logs', __('Logs', 'newsletter'));
            $this->add_admin_page('diagnostic', __('Diagnostic', 'newsletter'));
            $this->add_admin_page('test', __('Test', 'newsletter'));
        }
    }

    function add_extensions_menu() {
        if (!class_exists('NewsletterExtensions')) {
            $this->add_menu_page('extensions', '<span style="color:#27AE60; font-weight: bold;">' . __('Addons', 'newsletter') . '</span>');
        }
    }

    function hook_in_admin_header() {
        if (!$this->is_admin_page()) {
            add_action('admin_notices', array($this, 'hook_admin_notices'));
            return;
        }
        remove_all_actions('admin_notices');
        remove_all_actions('all_admin_notices');
        add_action('admin_notices', array($this, 'hook_admin_notices'));
    }

    function hook_admin_notices() {
        // Check of Newsletter dedicated page
        if (!empty($this->options['page'])) {
            if (get_post_status($this->options['page']) !== 'publish') {
                echo '<div class="notice notice-error"><p>The Newsletter dedicated page is not published. <a href="', site_url('/wp-admin/post.php') . '?post=', $this->options['page'], '&action=edit"><strong>Edit the page</strong></a> or <a href="admin.php?page=newsletter_main_main"><strong>review the main settings</strong></a>.</p></div>';
            }
        }

        if (isset($this->options['debug']) && $this->options['debug'] == 1) {
            echo '<div class="notice notice-warning"><p>The Newsletter plugin is in <strong>debug mode</strong>. When done change it on Newsletter <a href="admin.php?page=newsletter_main_main"><strong>main settings</strong></a>. Do not keep the debug mode active on production sites.</p></div>';
        }
    }

    function hook_wp_enqueue_scripts() {
        if (empty($this->options['css_disabled']) && apply_filters('newsletter_enqueue_style', true)) {
            wp_enqueue_style('newsletter', plugins_url('newsletter') . '/style.css', [], NEWSLETTER_VERSION);
            if (!empty($this->options['css'])) {
                wp_add_inline_style('newsletter', $this->options['css']);
            }
        } else {
            if (!empty($this->options['css'])) {
                add_action('wp_head', function () {
                    echo '<style>', $this->options['css'], '</style>';
                });
            }
        }
    }

    function hook_wp_admin_enqueue_scripts() {

        $newsletter_url = plugins_url('newsletter');
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_script('jquery-ui-tooltip');
        wp_enqueue_script('jquery-ui-draggable');
        wp_enqueue_media();
        
        wp_enqueue_script('tnp-admin', $newsletter_url . '/admin/admin.js', ['jquery'], NEWSLETTER_VERSION);

        wp_enqueue_style('tnp-select2', $newsletter_url . '/vendor/select2/css/select2.min.css', [], NEWSLETTER_VERSION);
        wp_enqueue_script('tnp-select2', $newsletter_url . '/vendor/select2/js/select2.min.js', ['jquery'], NEWSLETTER_VERSION);

        wp_enqueue_style('tnp-modal', $newsletter_url . '/admin/modal.css', [], NEWSLETTER_VERSION);
        wp_enqueue_script('tnp-modal', $newsletter_url . '/admin/modal.js', ['jquery'], NEWSLETTER_VERSION, true);
        
        wp_enqueue_style('tnp-toast', $newsletter_url . '/admin/toast.css', [], NEWSLETTER_VERSION);
        wp_enqueue_script('tnp-toast', $newsletter_url . '/admin/toast.js', ['jquery'], NEWSLETTER_VERSION);
        
        wp_enqueue_style('tnp-admin-font', 'https://use.typekit.net/jlj2wjy.css');
        wp_enqueue_style('tnp-admin-fontawesome', $newsletter_url . '/vendor/fa/css/all.min.css', [], NEWSLETTER_VERSION);
        wp_enqueue_style('tnp-admin-jquery-ui', $newsletter_url . '/vendor/jquery-ui/jquery-ui.min.css', [], NEWSLETTER_VERSION);
        wp_enqueue_style('tnp-admin', $newsletter_url . '/admin/admin.css', [], NEWSLETTER_VERSION);
        wp_enqueue_style('tnp-admin-dropdown', $newsletter_url . '/css/dropdown.css', [], NEWSLETTER_VERSION);
        wp_enqueue_style('tnp-admin-tabs', $newsletter_url . '/css/tabs.css', [], NEWSLETTER_VERSION);
        wp_enqueue_style('tnp-admin-controls', $newsletter_url . '/css/controls.css', [], NEWSLETTER_VERSION);
        wp_enqueue_style('tnp-admin-fields', $newsletter_url . '/css/fields.css', [], NEWSLETTER_VERSION);
        wp_enqueue_style('tnp-admin-widgets', $newsletter_url . '/css/widgets.css', [], NEWSLETTER_VERSION);
        wp_enqueue_style('tnp-admin-extensions', $newsletter_url . '/css/extensions.css', [], NEWSLETTER_VERSION);

        if (NEWSLETTER_DARK) {
            wp_enqueue_style('tnp-admin-dark', $newsletter_url . '/admin/admin-dark.css', ['tnp-admin'], NEWSLETTER_VERSION);
            wp_enqueue_style('tnp-admin-controls-dark', $newsletter_url . '/css/controls-dark.css', ['tnp-admin-controls'], NEWSLETTER_VERSION);
        }

        $translations_array = array(
            'save_to_update_counter' => __('Save the newsletter to update the counter!', 'newsletter')
        );
        wp_localize_script('tnp-admin', 'tnp_translations', $translations_array);

        wp_enqueue_script('tnp-jquery-vmap', $newsletter_url . '/vendor/jqvmap/jquery.vmap.min.js', ['jquery'], NEWSLETTER_VERSION);
        wp_enqueue_script('tnp-jquery-vmap-world', $newsletter_url . '/vendor/jqvmap/jquery.vmap.world.js', ['tnp-jquery-vmap'], NEWSLETTER_VERSION);
        wp_enqueue_style('tnp-jquery-vmap', $newsletter_url . '/vendor/jqvmap/jqvmap.min.css', [], NEWSLETTER_VERSION);

        wp_register_script('tnp-chart', $newsletter_url . '/vendor/chartjs/Chart.min.js', ['jquery'], NEWSLETTER_VERSION);

        wp_enqueue_script('tnp-color-picker', $newsletter_url . '/vendor/spectrum/spectrum.min.js', ['jquery']);
        wp_enqueue_style('tnp-color-picker', $newsletter_url . '/vendor/spectrum/spectrum.min.css', [], NEWSLETTER_VERSION);
    }

    function shortcode_newsletter_replace($attrs, $content) {
        $content = do_shortcode($content);
        $content = $this->replace($content, $this->get_user_from_request(), $this->get_email_from_request());
        return $content;
    }

    function is_admin_page() {
        if (!isset($_GET['page'])) {
            return false;
        }
        $page = $_GET['page'];
        return strpos($page, 'newsletter_') === 0;
    }

    function hook_admin_init() {
        // Verificare il contesto
        if (isset($_GET['page']) && $_GET['page'] === 'newsletter_main_welcome')
            return;
        if (get_option('newsletter_show_welcome')) {
            delete_option('newsletter_show_welcome');
            wp_redirect(admin_url('admin.php?page=newsletter_main_welcome'));
        }
    }

    function hook_admin_head() {
        // Small global rule for sidebar menu entries
        echo '<style>';
        echo '.tnp-side-menu { color: #E67E22!important; }';
        echo '</style>';
    }

    function relink($text, $email_id, $user_id, $email_token = '') {
        return NewsletterStatistics::instance()->relink($text, $email_id, $user_id, $email_token);
    }

    /**
     * Runs every 5 minutes and look for emails that need to be processed.
     */
    function hook_newsletter() {
        global $wpdb;

        $this->logger->debug(__METHOD__ . '> Start');

        // Do not accept job activation before at least 4 minutes are elapsed from the last run.
        if (!$this->check_transient('engine', NEWSLETTER_CRON_INTERVAL)) {
            return;
        }

        // Retrieve all emails in "sending" status
        $emails = $this->get_results("select * from " . NEWSLETTER_EMAILS_TABLE . " where status='sending' and send_on<" . time() . " order by id asc");
        $this->logger->debug(__METHOD__ . '> Emails found in sending status: ' . count($emails));

        foreach ($emails as $email) {
            $this->logger->info(__METHOD__ . '> Start newsletter ' . $email->id);
            $r = $this->send($email);
            if ($this->limits_exceeded()) {
                break;
            }
            $this->logger->info(__METHOD__ . '> End newsletter ' . $email->id);
        }
        // Remove the semaphore so the delivery engine can be activated again
        $this->delete_transient('engine');

        $this->logger->debug(__METHOD__ . '> End');
    }

    /**
     * Sends an email to targeted users or to given users. If a list of users is given (usually a list of test users)
     * the query inside the email to retrieve users is not used.
     *
     * @global wpdb $wpdb
     * @global type $newsletter_feed
     * @param TNP_Email $email
     * @param array $users
     * @return boolean|WP_Error True if the process completed, false if limits was reached. On false the caller should no continue to call it with other emails.
     */
    function send($email, $users = null, $test = false) {
        //TODO proposte:
        //  - Separare funzione che spedisce normalmente da quella che spedisce dei test
        //  - Utilizzare le eccezioni per evitare di controllare se il risultato della funzione è un errore oppure no
        //  - Le eccezioni dovrebbero essere tipizzate
        global $wpdb;

        ignore_user_abort(true);

        if (is_array($email)) {
            $email = (object) $email;
        }

        // Could be a test
        if (empty($email->id)) {
            $email->id = 0;
        }

        $this->logger->info(__METHOD__ . '> Start run for email ' . $email->id);

        // This stops the update of last_id and sent fields since
        // it's not a scheduled delivery but a test or something else (like an autoresponder)
        $supplied_users = $users != null;

        if ($users == null) {

            $skip_this_run = apply_filters('newsletter_send_skip', false, $email);
            if ($skip_this_run) {
                return false;
            }

            //If query is empty send to all confirmed subscribers
            if (empty($email->query)) {
                $email->query = "select * from " . NEWSLETTER_USERS_TABLE . " where status='C'";
            }

            // TODO: Ask the max emails per hour/run (to be decided) to the mailer

            $email->options = maybe_unserialize($email->options);
            // Retrieve max shipping emails per run
            $max_emails = apply_filters('newsletter_send_max_emails', $this->max_emails, $email);
            $this->logger->debug(__METHOD__ . '> Max emails per run: ' . $max_emails);

            if (empty($max_emails)) {
                $this->logger->debug(__METHOD__ . '> Max emails empty after the filter');
                $max_emails = $this->max_emails;
            }

            // Add limit to query
            //$query = apply_filters('newsletter_send_query', $email->query, $email);
            $query = $email->query;
            $query .= " and id>" . $email->last_id . " order by id limit " . $max_emails;

            $this->logger->debug(__METHOD__ . '> Query: ' . $query);

            //Retrieve subscribers
            $users = $this->get_results($query);

            $this->logger->debug(__METHOD__ . '> Loaded users: ' . count($users));

            // If there was a database error, return error
            if ($users === false) {
                return new WP_Error('1', 'Unable to query subscribers, check the logs');
            }

            if (empty($users)) {
                $this->logger->info(__METHOD__ . '> No more users, set as sent');
                $wpdb->query("update " . NEWSLETTER_EMAILS_TABLE . " set status='sent', total=sent where id=" . $email->id . " limit 1");
                do_action('newsletter_ended_sending_newsletter', $email);
                return true;
            }
        } else {
            $this->logger->info(__METHOD__ . '> Subscribers supplied');
        }

        $start_time = microtime(true);
        $count = 0;
        $result = true;

        $mailer = $this->get_mailer();

        $batch_size = $mailer->get_batch_size();

        $this->logger->debug(__METHOD__ . '> Batch size: ' . $batch_size);

        // For batch size == 1 (normal condition) we optimize
        if ($batch_size == 1) {

            foreach ($users as $user) {

                if (!$supplied_users && !$test && $this->limits_exceeded()) {
                    $result = false;
                    break;
                }

                $this->logger->debug(__METHOD__ . '> Processing user ID: ' . $user->id);
                $user = apply_filters('newsletter_send_user', $user);
                $message = $this->build_message($email, $user);

                // Save even test emails since people wants to see some stats even for test emails. Stats are reset upon the real "send" of a newsletter
                $this->save_sent_message($message);

                //Se non è un test incremento il contatore delle email spedite. Perchè incremento prima di spedire??
                if (!$test) {
                    $wpdb->query("update " . NEWSLETTER_EMAILS_TABLE . " set sent=sent+1, last_id=" . $user->id . " where id=" . $email->id . " limit 1");
                }

                $r = $mailer->send($message);

                //Perchè dentro al messaggio vado a impostare un errore (viene fatto dentro alla $mailer->send())??? boh
                //in ogni caso, se ho un messaggio di errore loggo e salvo nella sent che la spedizione mi ha dato un errore
                if (!empty($message->error)) {
                    $this->logger->error($message);
                    $this->save_sent_message($message);
                }

                //Se la send mi ritorna un WP_Error loggo e ritorno al chiamante l'errore
                if (is_wp_error($r)) {
                    $this->logger->error($r);

                    // For fatal error, the newsletter status i changed to error (and the delivery stopped)
                    if (!$test && $r->get_error_code() == NewsletterMailer::ERROR_FATAL) {
                        $this->set_error_state_of_email($email, $r->get_error_message());
                    }

                    return $r;
                }
            }

            // TODO: Review if they're useful
            $this->email_limit--; //Diminuisce il limite delle email?? differenza tra $this->email_limit e $this->max_emails??
            $count++;
        } else {

            $chunks = array_chunk($users, $batch_size);

            foreach ($chunks as $chunk) {

                //Se gli utenti non sono stati forniti esternamente
                //e se non è un test
                //e se è stato superato il limite ALLORA imposto $result-false e interrompo il ciclo
                if (!$supplied_users && !$test && $this->limits_exceeded()) {
                    $result = false;
                    break;
                }

                $messages = array();

                foreach ($chunk as $user) {

                    $this->logger->debug(__METHOD__ . '> Processing user ID: ' . $user->id);
                    $user = apply_filters('newsletter_send_user', $user);
                    $message = $this->build_message($email, $user);
                    $this->save_sent_message($message);
                    $messages[] = $message;

                    if (!$test) {
                        $wpdb->query("update " . NEWSLETTER_EMAILS_TABLE . " set sent=sent+1, last_id=" . $user->id . " where id=" . $email->id . " limit 1");
                    }
                    $this->email_limit--;
                    $count++;
                }

                $r = $mailer->send_batch($messages);

                foreach ($messages as $message) {
                    if (!empty($message->error)) {
                        $this->save_sent_message($message);
                    }
                }

                if (is_wp_error($r)) {

                    if (!$test && $r->get_error_code() == NewsletterMailer::ERROR_FATAL) {
                        $this->set_error_state_of_email($email, $r->get_error_message());
                    }

                    $this->logger->error($r);
                    return $r;
                }
            }
        }

        $end_time = microtime(true);
        
        // We sent to all supplied users, but warning that no more should be processed
        if (!$test && $supplied_users && $this->limits_exceeded()) {
            $result = false;
        }

        // Stats only for newsletter with enough emails in a batch (we exclude the Autoresponder since it send one email per call)
        if (!$test && !$supplied_users && $count > 5) {

            

            $send_calls = get_option('newsletter_diagnostic_send_calls', []);
            $send_calls[] = array($start_time, $end_time, $count, $result);

            if (count($send_calls) > self::MAX_CRON_SAMPLES)
                array_shift($send_calls);

            update_option('newsletter_diagnostic_send_calls', $send_calls, false);
        }
        
        // Cached general statistics are reset
        if (!$test) {
            NewsletterStatistics::instance()->reset_stats_time($email->id);
        }

        $this->logger->info(__METHOD__ . '> End run for email ' . $email->id);

        return $result;
    }

    /**
     * @param TNP_Email $email
     */
    private function set_error_state_of_email($email, $message = '') {
        // Handle only message type at the moment
        if ($email->type !== 'message') {
            return;
        }

        do_action('newsletter_error_on_sending', $email, $message);

        $edited_email = new TNP_Email();
        $edited_email->id = $email->id;
        $edited_email->status = TNP_Email::STATUS_ERROR;
        $edited_email->options = $email->options;
        $edited_email->options['error_message'] = $message;

        $this->save_email($edited_email);
    }

    /**
     *
     * @param TNP_Email $email
     * @param TNP_User $user
     * @return \TNP_Mailer_Message
     */
    function build_message($email, $user) {

        $message = new TNP_Mailer_Message();

        $message->to = $user->email;

        $message->headers = [];
        $message->headers['Precedence'] = 'bulk';
        $message->headers['X-Newsletter-Email-Id'] = $email->id;
        $message->headers['X-Auto-Response-Suppress'] = 'OOF, AutoReply';
        $message->headers = apply_filters('newsletter_message_headers', $message->headers, $email, $user);

        $message->body = preg_replace('/data-json=".*?"/is', '', $email->message);
        $message->body = preg_replace('/  +/s', ' ', $message->body);
        $message->body = $this->replace($message->body, $user, $email);
        if ($this->options['do_shortcodes']) {
            $message->body = do_shortcode($message->body);
        }
        $message->body = apply_filters('newsletter_message_html', $message->body, $email, $user);

        $message->body_text = $this->replace($email->message_text, $user, $email);
        $message->body_text = apply_filters('newsletter_message_text', $message->body_text, $email, $user);

        if ($email->track == 1) {
            $message->body = $this->relink($message->body, $email->id, $user->id, $email->token);
        }

        $message->subject = $this->replace($email->subject, $user);
        $message->subject = apply_filters('newsletter_message_subject', $message->subject, $email, $user);

        if (!empty($email->options['sender_email'])) {
            $message->from = $email->options['sender_email'];
        } else {
            $message->from = $this->options['sender_email'];
        }

        if (!empty($email->options['sender_name'])) {
            $message->from_name = $email->options['sender_name'];
        } else {
            $message->from_name = $this->options['sender_name'];
        }

        $message->email_id = $email->id;
        $message->user_id = $user->id;

        return apply_filters('newsletter_message', $message, $email, $user);
    }

    /**
     *
     * @param TNP_Mailer_Message $message
     * @param int $status
     * @param string $error
     */
    function save_sent_message($message) {
        global $wpdb;

        if (!$message->user_id || !$message->email_id) {
            return;
        }
        $status = empty($message->error) ? 0 : 1;

        $error = mb_substr($message->error, 0, 250);

        $this->query($wpdb->prepare("insert into " . $wpdb->prefix . 'newsletter_sent (user_id, email_id, time, status, error) values (%d, %d, %d, %d, %s) on duplicate key update time=%d, status=%d, error=%s', $message->user_id, $message->email_id, time(), $status, $error, time(), $status, $error));
    }

    /**
     * This function checks is, during processing, we are getting to near to system limits and should stop any further
     * work (when returns true).
     */
    function limits_exceeded() {
        global $wpdb;

        if (!$this->limits_set) {
            $this->logger->debug(__METHOD__ . '> Setting the limits for the first time');

            @set_time_limit(NEWSLETTER_CRON_INTERVAL + 30);

            $max_time = (int) (@ini_get('max_execution_time') * 0.95);
            if ($max_time == 0 || $max_time > NEWSLETTER_CRON_INTERVAL) {
                $max_time = (int) (NEWSLETTER_CRON_INTERVAL * 0.95);
            }

            $this->time_limit = $this->time_start + $max_time;

            $this->logger->info(__METHOD__ . '> Max time set to ' . $max_time);

            $max = (int) $this->options['scheduler_max'];
            if (!$max) {
                $max = 100;
            }
            $this->email_limit = max(floor($max / 12), 1);
            $this->logger->debug(__METHOD__ . '> Max number of emails can send: ' . $this->email_limit);

            $wpdb->query("set session wait_timeout=300");
            // From default-constants.php
            if (function_exists('memory_get_usage') && ( (int) @ini_get('memory_limit') < 128 )) {
                @ini_set('memory_limit', '256M');
            }

            $this->limits_set = true;
        }

        // The time limit is set on constructor, since it has to be set as early as possible
        if (time() > $this->time_limit) {
            $this->logger->info(__METHOD__ . '> Max execution time limit reached');
            return true;
        }

        if ($this->email_limit <= 0) {
            $this->logger->info(__METHOD__ . '> Max emails limit reached');
            return true;
        }
        return false;
    }

    /**
     * @deprecated since version 6.0.0
     * @param callback $callable
     */
    function register_mail_method($callable) {
        $this->mailer = new NewsletterMailMethodWrapper($callable);
    }

    function register_mailer($mailer) {
        //$this->logger->debug($mailer);
        if (!$mailer)
            return;
        if ($mailer instanceof NewsletterMailer) {
            $this->mailer = $mailer;
        } else {
            $this->logger->debug('Wrapping mailer: ' . get_class($mailer));
            $this->mailer = new NewsletterOldMailerWrapper($mailer);
        }
    }

    /**
     * Returns the current registered mailer which must be used to send emails.
     *
     * @return NewsletterMailer
     */
    function get_mailer() {
        if ($this->mailer) {
            return $this->mailer;
        }

        do_action('newsletter_register_mailer');
        
        if (!$this->mailer) {
            // Compatibility
            $smtp = $this->get_options('smtp');
            if (!empty($smtp['enabled'])) {
                $this->mailer = new NewsletterDefaultSMTPMailer($smtp);
            } else {
                $this->mailer = new NewsletterDefaultMailer();
            }
        }
        return $this->mailer;
    }

    /**
     *
     * @param TNP_Mailer_Message $message
     * @return type
     */
    function deliver($message) {
        $mailer = $this->get_mailer();
        if (empty($message->from))
            $message->from = $this->options['sender_email'];
        if (empty($message->from_name))
            $mailer->from_name = $this->options['sender_name'];
        return $mailer->send($message);
    }

    /**
     *
     * @param type $to
     * @param type $subject
     * @param string|array $message If string is considered HTML, is array should contains the keys "html" and "text"
     * @param type $headers
     * @param type $enqueue
     * @param type $from
     * @return boolean
     */
    function mail($to, $subject, $message, $headers = array(), $enqueue = false, $from = false) {

        if (empty($subject)) {
            $this->logger->error('mail> Subject empty, skipped');
            return true;
        }

        $mailer_message = new TNP_Mailer_Message();
        $mailer_message->to = $to;
        $mailer_message->subject = $subject;
        $mailer_message->from = $this->options['sender_email'];
        $mailer_message->from_name = $this->options['sender_name'];

        if (!empty($headers)) {
            $mailer_message->headers = $headers;
        }
        $mailer_message->headers['X-Auto-Response-Suppress'] = 'OOF, AutoReply';

        // Message carrige returns and line feeds clean up
        if (!is_array($message)) {
            $mailer_message->body = $this->clean_eol($message);
        } else {
            if (!empty($message['text'])) {
                $mailer_message->body_text = $this->clean_eol($message['text']);
            }

            if (!empty($message['html'])) {
                $mailer_message->body = $this->clean_eol($message['html']);
            }
        }

        $this->logger->debug($mailer_message);

        $mailer = $this->get_mailer();

        $r = $mailer->send($mailer_message);

        return !is_wp_error($r);
    }

    function hook_deactivate() {
        wp_clear_scheduled_hook('newsletter');
    }

    function find_file($file1, $file2) {
        if (is_file($file1))
            return $file1;
        return $file2;
    }

    function hook_site_transient_update_plugins($value) {
        static $extra_response = array();

        //$this->logger->debug('Update plugins transient called');

        if (!$value || !is_object($value)) {
            //$this->logger->info('Empty object');
            return $value;
        }

        if (!isset($value->response) || !is_array($value->response)) {
            $value->response = array();
        }

        // Already computed? Use it! (this filter is called many times in a single request)
        if ($extra_response) {
            //$this->logger->debug('Already updated');
            $value->response = array_merge($value->response, $extra_response);
            return $value;
        }

        $extensions = $this->getTnpExtensions();

        // Ops...
        if (!$extensions) {
            return $value;
        }

        foreach ($extensions as $extension) {
            unset($value->response[$extension->wp_slug]);
            unset($value->no_update[$extension->wp_slug]);
        }

        // Someone doesn't want our addons updated, let respect it (this constant should be defined in wp-config.php)
        if (!NEWSLETTER_EXTENSION_UPDATE) {
            //$this->logger->info('Updates disabled');
            return $value;
        }

        include_once(ABSPATH . 'wp-admin/includes/plugin.php');

        // Ok, that is really bad (should we remove it? is there a minimum WP version?)
        if (!function_exists('get_plugin_data')) {
            //$this->logger->error('No get_plugin_data function available!');
            return $value;
        }

        $license_key = $this->get_license_key();

        // Here we prepare the update information BUT do not add the link to the package which is privided
        // by our Addons Manager (due to WP policies)
        foreach ($extensions as $extension) {

            // Patch for names convention
            $extension->plugin = $extension->wp_slug;

            //$this->logger->debug('Processing ' . $extension->plugin);
            //$this->logger->debug($extension);

            $plugin_data = false;
            if (file_exists(WP_PLUGIN_DIR . '/' . $extension->plugin)) {
                $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $extension->plugin, false, false);
            } else if (file_exists(WPMU_PLUGIN_DIR . '/' . $extension->plugin)) {
                $plugin_data = get_plugin_data(WPMU_PLUGIN_DIR . '/' . $extension->plugin, false, false);
            }

            if (!$plugin_data) {
                //$this->logger->debug('Seems not installed');
                continue;
            }

            $plugin = new stdClass();
            $plugin->id = $extension->id;
            $plugin->slug = $extension->slug;
            $plugin->plugin = $extension->plugin;
            $plugin->new_version = $extension->version;
            $plugin->url = $extension->url;
            if (class_exists('NewsletterExtensions')) {
                // NO filters here!
                $plugin->package = NewsletterExtensions::$instance->get_package($extension->id, $license_key);
            } else {
                $plugin->package = '';
            }
//            [banners] => Array
//                        (
//                            [2x] => https://ps.w.org/wp-rss-aggregator/assets/banner-1544x500.png?rev=2040548
//                            [1x] => https://ps.w.org/wp-rss-aggregator/assets/banner-772x250.png?rev=2040548
//                        )
//            [icons] => Array
//                        (
//                            [2x] => https://ps.w.org/advanced-custom-fields/assets/icon-256x256.png?rev=1082746
//                            [1x] => https://ps.w.org/advanced-custom-fields/assets/icon-128x128.png?rev=1082746
//                        )
            if (version_compare($extension->version, $plugin_data['Version']) > 0) {
                //$this->logger->debug('There is a new version');
                $extra_response[$extension->plugin] = $plugin;
            } else {
                // Maybe useless...
                //$this->logger->debug('There is NOT a new version');
                $value->no_update[$extension->plugin] = $plugin;
            }
            //$this->logger->debug('Added');
        }

        $value->response = array_merge($value->response, $extra_response);

        return $value;
    }

    /**
     * @deprecated since version 6.1.9
     */
    function get_extension_version($extension_id) {
        $extensions = $this->getTnpExtensions();

        if (!is_array($extensions)) {
            return null;
        }
        foreach ($extensions as $extension) {
            if ($extension->id == $extension_id) {
                return $extension->version;
            }
        }

        return null;
    }

    /**
     * MUST be kept for old addons.
     *
     * @deprecated since version 6.1.9
     */
    function set_extension_update_data($value, $extension) {
        return $value;
    }

    /**
     * Retrieve the extensions form the tnp site
     * @return array
     */
    function getTnpExtensions() {

        $extensions_json = get_transient('tnp_extensions_json');

        if (empty($extensions_json)) {
            $url = "http://www.thenewsletterplugin.com/wp-content/extensions.json?ver=" . NEWSLETTER_VERSION;
            $extensions_response = wp_remote_get($url);

            if (is_wp_error($extensions_response)) {
                // Cache anyway for blogs which cannot connect outside
                $extensions_json = '[]';
                set_transient('tnp_extensions_json', $extensions_json, 72 * 60 * 60);
                $this->logger->error($extensions_response);
            } else {

                $extensions_json = wp_remote_retrieve_body($extensions_response);

                // Not clear cases
                if (empty($extensions_json) || !json_decode($extensions_json)) {
                    $this->logger->error('Invalid json from thenewsletterplugin.com: retrying in 72 hours');
                    $this->logger->error('JSON: ' . $extensions_json);
                    $extensions_json = '[]';
                }
                set_transient('tnp_extensions_json', $extensions_json, 72 * 60 * 60);
            }
        }

        $extensions = json_decode($extensions_json);

        return $extensions;
    }

    function clear_extensions_cache() {
        delete_transient('tnp_extensions_json');
    }



    var $panels = array();

    function add_panel($key, $panel) {
        if (!isset($this->panels[$key]))
            $this->panels[$key] = array();
        if (!isset($panel['id']))
            $panel['id'] = sanitize_key($panel['label']);
        $this->panels[$key][] = $panel;
    }

    function has_license() {
        return !empty($this->options['contract_key']);
    }

    function get_sender_name() {
        return $this->options['sender_name'];
    }

    function get_sender_email() {
        return $this->options['sender_email'];
    }

    /**
     *
     * @return int
     */
    function get_newsletter_page_id() {
        return (int) $this->options['page'];
    }

    /**
     *
     * @return WP_Post
     */
    function get_newsletter_page() {
        return get_post($this->get_newsletter_page_id());
    }

    /**
     * Returns the Newsletter dedicated page URL or an alternative URL if that page if not
     * configured or not available.
     *
     * @staticvar string $url
     * @return string
     */
    function get_newsletter_page_url($language = '') {

        $page = $this->get_newsletter_page();

        if (!$page || $page->post_status !== 'publish') {
            return $this->build_action_url('m');
        }

        $newsletter_page_url = get_permalink($page->ID);
        if ($language && $newsletter_page_url) {
            if (class_exists('SitePress')) {
                $newsletter_page_url = apply_filters('wpml_permalink', $newsletter_page_url, $language, true);
            }
            if (function_exists('pll_get_post')) {
                $translated_page = get_permalink(pll_get_post($page->ID, $language));
                if ($translated_page) {
                    $newsletter_page_url = $translated_page;
                }
            }
        }

        return $newsletter_page_url;
    }

    function get_license_key() {
        if (defined('NEWSLETTER_LICENSE_KEY')) {
            return NEWSLETTER_LICENSE_KEY;
        } else {
            if (!empty($this->options['contract_key'])) {
                return trim($this->options['contract_key']);
            }
        }
        return false;
    }

    /**
     * Get the data connected to the specified license code on man settings.
     * 
     * - false if no license is present
     * - WP_Error if something went wrong if getting the license data
     * - object with expiration and addons list
     * 
     * @param boolean $refresh
     * @return \WP_Error|boolean|object
     */
    function get_license_data($refresh = false) {

        $this->logger->debug('Getting license data');

        $license_key = $this->get_license_key();
        if (empty($license_key)) {
            $this->logger->debug('License was empty');
            delete_transient('newsletter_license_data');
            return false;
        }

        if (!$refresh) {
            $license_data = get_transient('newsletter_license_data');
            if ($license_data !== false && is_object($license_data)) {
                $this->logger->debug('License data found on cache');
                return $license_data;
            }
        }

        $this->logger->debug('Refreshing the license data');

        $license_data_url = 'https://www.thenewsletterplugin.com/wp-content/plugins/file-commerce-pro/get-license-data.php';

        $response = wp_remote_post($license_data_url, array(
            'body' => array('k' => $license_key)
        ));

        // Fall back to http...
        if (is_wp_error($response)) {
            $this->logger->error($response);
            $this->logger->error('Falling back to http');
            $license_data_url = str_replace('https', 'http', $license_data_url);
            $response = wp_remote_post($license_data_url, array(
                'body' => array('k' => $license_key)
            ));
            if (is_wp_error($response)) {
                $this->logger->error($response);
                set_transient('newsletter_license_data', $response, DAY_IN_SECONDS);
                return $response;
            }
        }

        $download_message = 'You can download all addons from www.thenewsletterplugin.com if your license is valid.';

        if (wp_remote_retrieve_response_code($response) != '200') {
            $this->logger->error('license data error: ' . wp_remote_retrieve_response_code($response));
            $data = new WP_Error(wp_remote_retrieve_response_code($response), 'License validation service error. <br>' . $download_message);
            set_transient('newsletter_license_data', $data, DAY_IN_SECONDS);
            return $data;
        }

        $json = wp_remote_retrieve_body($response);
        $data = json_decode($json);

        if (!is_object($data)) {
            $this->logger->error($json);
            $data = new WP_Error(1, 'License validation service error. <br>' . $download_message);
            set_transient('newsletter_license_data', $data, DAY_IN_SECONDS);
            return $data;
        }

        if (isset($data->message)) {
            $data = new WP_Error(1, $data->message . ' (check the license on Newsletter main settings)');
            set_transient('newsletter_license_data', $data, DAY_IN_SECONDS);
            return $data;
        }

        $expiration = WEEK_IN_SECONDS;
        // If the license expires in few days, make the transient live only few days, so it will be refreshed
        if ($data->expire > time() && $data->expire - time() < WEEK_IN_SECONDS) {
            $expiration = $data->expire - time();
        }
        set_transient('newsletter_license_data', $data, $expiration);

        return $data;
    }

    /**
     * @deprecated
     * @param type $license_key
     * @return \WP_Error
     */
    public static function check_license($license_key) {
        $response = wp_remote_get('http://www.thenewsletterplugin.com/wp-content/plugins/file-commerce-pro/check.php?k=' . urlencode($license_key), array('sslverify' => false));
        if (is_wp_error($response)) {
            /* @var $response WP_Error */
            return new WP_Error(-1, 'It seems that your blog cannot contact the license validator. Ask your provider to unlock the HTTP/HTTPS connections to www.thenewsletterplugin.com<br>'
                    . esc_html($response->get_error_code()) . ' - ' . esc_html($response->get_error_message()));
        } else if ($response['response']['code'] != 200) {
            return new WP_Error(-1, '[' . $response['response']['code'] . '] The license seems expired or not valid, please check your <a href="https://www.thenewsletterplugin.com/account">license code and status</a>, thank you.'
                    . '<br>You can anyway download the professional extension from https://www.thenewsletterplugin.com.');
        } elseif ($expires = json_decode(wp_remote_retrieve_body($response))) {
            return array('expires' => $expires->expire, 'message' => 'Your license is valid and expires on ' . esc_html(date('Y-m-d', $expires->expire)));
        } else {
            return new WP_Error(-1, 'Unable to detect the license expiration. Debug data to report to the support: <code>' . esc_html(wp_remote_retrieve_body($response)) . '</code>');
        }
    }

    function add_notice_to_chosen_profile_page_hook($post_states, $post) {

        if ($post->ID == $this->options['page']) {
            $post_states[] = __('Newsletter plugin page, do not delete', 'newsletter');
        }

        return $post_states;
    }

}

$newsletter = Newsletter::instance();

require_once NEWSLETTER_DIR . '/subscription/subscription.php';
require_once NEWSLETTER_DIR . '/unsubscription/unsubscription.php';
require_once NEWSLETTER_DIR . '/profile/profile.php';
require_once NEWSLETTER_DIR . '/emails/emails.php';
require_once NEWSLETTER_DIR . '/users/users.php';
require_once NEWSLETTER_DIR . '/statistics/statistics.php';
require_once NEWSLETTER_DIR . '/widget/standard.php';
require_once NEWSLETTER_DIR . '/widget/minimal.php';
