<?php if (! defined('ABSPATH')) {
    exit('No direct script access allowed');
}

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://agegate.io
 * @since      1.0.0
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Age_Gate
 * @subpackage Age_Gate/includes
 * @author     Phil Baker
 */
class Age_Gate
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Age_Gate_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        if (defined('AGE_GATE_VERSION')) {
            $this->version = AGE_GATE_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'age-gate';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Age_Gate_Loader. Orchestrates the hooks of the plugin.
     * - Age_Gate_i18n. Defines internationalization functionality.
     * - Age_Gate_Admin. Defines all hooks for the admin area.
     * - Age_Gate_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * Composer autoload for third party classes
         */
        require_once AGE_GATE_PATH . 'vendor/autoload.php';


        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once AGE_GATE_PATH . 'includes/class-age-gate-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once AGE_GATE_PATH . 'includes/class-age-gate-i18n.php';

        /**
         * A common class for shared functionality
         */
        require_once AGE_GATE_PATH . 'includes/class-age-gate-common.php';


        /**
         * The classes responsible for defining all actions that occur in the admin area.
         */
        require_once AGE_GATE_PATH . 'admin/third-party/form-helper.php';
        require_once AGE_GATE_PATH . 'admin/class-age-gate-admin.php';
        require_once AGE_GATE_PATH . 'admin/class-age-gate-advanced.php';
        require_once AGE_GATE_PATH . 'admin/class-age-gate-appearance.php';
        require_once AGE_GATE_PATH . 'admin/class-age-gate-messaging.php';
        require_once AGE_GATE_PATH . 'admin/class-age-gate-restriction.php';
        require_once AGE_GATE_PATH . 'admin/class-age-gate-access.php';
        require_once AGE_GATE_PATH . 'admin/class-age-gate-post-types.php';
        require_once AGE_GATE_PATH . 'admin/class-age-gate-taxonomies.php';
        require_once AGE_GATE_PATH . 'admin/class-age-gate-ajax.php';
        require_once AGE_GATE_PATH . 'admin/class-age-gate-update.php';
        require_once AGE_GATE_PATH . 'admin/class-age-gate-addons.php';
        require_once AGE_GATE_PATH . 'admin/class-age-gate-import-export.php';
        require_once AGE_GATE_PATH . 'admin/class-age-gate-lang.php';
        require_once AGE_GATE_PATH . 'admin/class-age-gate-v3.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once AGE_GATE_PATH . 'public/class-age-gate-public.php';
        require_once AGE_GATE_PATH . 'public/class-age-gate-public-submission.php';
        require_once AGE_GATE_PATH . 'public/class-age-gate-public-presentation.php';
        require_once AGE_GATE_PATH . 'public/class-age-gate-public-js.php';
        require_once AGE_GATE_PATH . 'public/class-age-gate-public-validation.php';
        require_once AGE_GATE_PATH . 'public/class-age-gate-public-registration.php';
        require_once AGE_GATE_PATH . 'public/class-age-gate-public-shortcode.php';
        require_once AGE_GATE_PATH . 'public/class-age-gate-public-rest.php';


        $this->loader = new Age_Gate_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Age_Gate_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new Age_Gate_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        if (!is_admin()) {
            return;
        }

        $plugin_admin = new Age_Gate_Admin;
        $plugin_advanced = new Age_Gate_Advanced;
        $plugin_appearance = new Age_Gate_Appearance;
        $plugin_messaging = new Age_Gate_Messaging;
        $plugin_restriction = new Age_Gate_Restriction;
        $plugin_access = new Age_Gate_Access;
        $plugin_posts = new Age_Gate_Post_Types;
        $plugin_ajax = new Age_Gate_Ajax;
        $plugin_taxonomies = new Age_Gate_Taxonomies;
        $plugin_update = new Age_Gate_Update;
        $plugin_addons = new Age_Gate_Addons;
        $plugin_lang = new Age_Gate_Multi_Lingual;
        $plugin_import_export = new Age_Gate_Import_Export;
        $plugin_v3 = new Age_Gate_V3;

        /**
         * Admin Class Actions
         */

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_menu_section');
        $this->loader->add_action('admin_notices', $plugin_admin, 'age_gate_admin_notice');
        $this->loader->add_action('admin_print_footer_scripts', $plugin_admin, 'editor_scripts');
        $this->loader->add_action('admin_init', $plugin_admin, 'purge_transients');
        $this->loader->add_action('admin_init', $plugin_admin, 'ag_toggle');
        $this->loader->add_action('in_admin_header', $plugin_admin, 'in_admin_header');


        /**
         * Admin Class Filters
         */
        $basename = plugin_basename(AGE_GATE_PATH . $this->plugin_name . '.php');
        $this->loader->add_filter("plugin_action_links_" . $basename, $plugin_admin, 'plugin_action_links');
        $this->loader->add_filter('plugin_row_meta', $plugin_admin, 'website_link', 10, 2);

        /**
         * Restriction Class Actions
         */
        $this->loader->add_action('admin_menu', $plugin_restriction, 'add_settings_page');
        $this->loader->add_action('admin_post_age_gate_restriction', $plugin_restriction, 'handle_form_submission');

        /**
         * Restriction Class Filters
         */


        /**
         * Messaging Class Actions
         */
        $this->loader->add_action('admin_menu', $plugin_messaging, 'add_settings_page');
        $this->loader->add_action('admin_post_age_gate_messages', $plugin_messaging, 'handle_form_submission');

        /**
         * Messaging Class Filters
         */
        $this->loader->add_filter('mce_buttons', $plugin_messaging, 'customise_tinymce', 2, 10);

        /**
         * Appearance Class Actions
         */
        $this->loader->add_action('admin_menu', $plugin_appearance, 'add_settings_page');
        $this->loader->add_action('admin_post_age_gate_appearance', $plugin_appearance, 'handle_form_submission');

        /**
         * Appearance Class Filters
         */

        /**
         * Advanced Class Actions
         */
        $this->loader->add_action('admin_menu', $plugin_advanced, 'add_settings_page');
        $this->loader->add_action('admin_post_age_gate_advanced', $plugin_advanced, 'handle_form_submission');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_advanced, 'enqueue_scripts');


        /**
         * Advanced Class Filters
         */



        /**
         * Access Class Actions
         */
        $this->loader->add_action('admin_menu', $plugin_access, 'add_settings_page');
        $this->loader->add_action('admin_post_age_gate_access', $plugin_access, 'handle_form_submission');

        /**
         * Access Class Filters
         */

        /**
         * V3 info
         */
        // $this->loader->add_action('admin_menu', $plugin_v3, 'add_settings_page');

        /**
         * Post Types Class Actions
         */
        // Save the "restrict" or "bypass" checkbox value.

        // Pre Gutenberg we can use misc_actions
        // $this->loader->add_action('post_submitbox_misc_actions', $plugin_posts, 'add_restriction_options');

        // Post Gutenberg this will be moved to a metabox
        $this->loader->add_action('add_meta_boxes', $plugin_posts, 'add_restriction_options');


        $this->loader->add_action('admin_enqueue_scripts', $plugin_posts, 'enqueue_scripts');
        $this->loader->add_action('save_post', $plugin_posts, 'save_post');
        $this->loader->add_action('edit_attachment', $plugin_posts, 'save_post');

        $this->loader->add_action('quick_edit_custom_box', $plugin_posts, 'age_gate_quick_edit', 10, 2);

        // will need to loop over all types, but for now, just posts
        $this->loader->add_filter('manage_posts_columns', $plugin_posts, 'age_gate_columns', 10000);
        $this->loader->add_filter('manage_pages_columns', $plugin_posts, 'age_gate_columns', 10000);
        $this->loader->add_action('manage_posts_custom_column', $plugin_posts, 'age_gate_columns_data', 10, 2);
        $this->loader->add_action('manage_pages_custom_column', $plugin_posts, 'age_gate_columns_data', 10, 2);

        // bulk actions - High priority in case of plugins like CPT generator
        $this->loader->add_filter('init', $plugin_posts, 'register_age_gate_bulk_actions', 10000);
        $this->loader->add_action('admin_notices', $plugin_posts, 'age_gate_bulk_action_admin_notice');

        /**
         * Post Types Class Filters
         */


        /**
         * AJAX Class Actions
         */
        $this->loader->add_action('wp_ajax_editor_css', $plugin_ajax, 'editor_get_css');
        $this->loader->add_action('wp_ajax_get_textarea', $plugin_ajax, 'get_text_area');
        $this->loader->add_action('wp_ajax_recover_css', $plugin_ajax, 'get_stored_css');
        $this->loader->add_action('wp_ajax_regenerate_serial', $plugin_ajax, 'regenerate_serial');
        $this->loader->add_action('wp_ajax_set_ag3_hidden', $plugin_ajax, 'set_ag3_hidden');

        /**
         * AJAX Class Filters
         */


        /**
         * Taxonomies class actions
         */
        $this->loader->add_action('admin_init', $plugin_taxonomies, 'register_taxonomies_fields', 1000, 3);

        /**
         * Update message actions
         */
        // $this->loader->add_filter('plugin_row_meta', array(__CLASS__, 'meta_links'), 10, 2);

        /**
         * Update message filters
         */
        $this->loader->add_action('in_plugin_update_message-age-gate/age-gate.php', $plugin_update, 'in_plugin_update_message', 10, 2);


        /**
        * Addon Class Actions
        */
        $this->loader->add_action('plugins_loaded', $plugin_addons, 'register_addons');
        $this->loader->add_action('init', $plugin_addons, '_check_cap');
        $this->loader->add_action('admin_post_age_gate_addon', $plugin_addons, 'handle_form_submission');

        /**
         * Multi lingual
         */
        $this->loader->add_action("init", $plugin_lang, 'is_multi_lingual');

        /**
         * Import/Export
         */
        $this->loader->add_action('admin_menu', $plugin_import_export, 'add_settings_page');
        $this->loader->add_filter('age_gate_admin_tabs', $plugin_import_export, 'import_export_tab');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {
        global $pagenow;
        if (is_admin() && $pagenow !== 'admin-post.php' && $pagenow !== 'admin-ajax.php') {
            return;
        }
        $plugin_public = new Age_Gate_Public;
        $plugin_submission = new Age_Gate_Submission;
        $plugin_presentation = new Age_Gate_Presentation;
        $plugin_js = new Age_Gate_Public_JS;
        $plugin_registration = new Age_Gate_Registration;
        $plugin_lang = new Age_Gate_Multi_Lingual;
        new Age_Gate_Shortcode;
        new Age_Gate_Rest;

        /**
         * Public Class Actions
         */
        // Calling the test as early as we can
        $this->loader->add_action("wp", $plugin_public, 'is_restricted');
        // $this->loader->add_action('age_gate_form_failed', $plugin_public, 'age_gate_failed');
        $this->loader->add_action('admin_bar_menu', $plugin_public, 'toolbar_link_to_mypage', 1000);
        $this->loader->add_action('wp_head', $plugin_public, 'customiser_js_disable');

        /**
         * Public Class Filters
         */
        $this->loader->add_filter('template_include', $plugin_public, 'load_template', 9999); // need a higher than 10 value for WooCommerce

        /**
         * Presentation Class Actions
         */
        $this->loader->add_action('wp_enqueue_scripts', $plugin_presentation, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_presentation, 'enqueue_scripts', 1);

        /**
         * Presentation Class Filters
         */
        // $this->loader->add_filter( 'pre_get_document_title', $plugin_presentation, 'return_page_title', 10, 1 );
        $this->loader->add_filter('wpseo_title', $plugin_presentation, 'return_page_title', 1000, 1);
        $this->loader->add_filter('document_title_parts', $plugin_presentation, 'change_page_title', 1000, 1);
        $this->loader->add_filter('wp_title', $plugin_presentation, 'change_default_title', 10, 3);

        /**
         * Submission Class Actions
         */
        // Form submission actions, need to technically be registered as Admin Hooks!
        // $this->loader->add_action('admin_post_age_gate_submit', $plugin_submission, 'handle_form_submission');
        // $this->loader->add_action('admin_post_nopriv_age_gate_submit', $plugin_submission, 'handle_form_submission');
        $this->loader->add_action('init', $plugin_submission, 'self_post', 999);
        
        /**
         * Submission Class Filters
         */


        /**
         * JS Class Actions
         */
        $this->loader->add_action('wp_footer', $plugin_js, 'render_age_gate');

        $this->loader->add_action('rest_api_init', $plugin_js, 'register_rest_endpoints');

        /**
         * Ajax Class Actions
         */
        // Form submission actions, need to technically be registered as Admin Hooks!
        $this->loader->add_action('wp_ajax_age_gate_submit', $plugin_js, 'handle_ajax_form_submission');
        $this->loader->add_action('wp_ajax_nopriv_age_gate_submit', $plugin_js, 'handle_ajax_form_submission');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_js, 'ajax_setup');
        $this->loader->add_action('wp_ajax_age_gate_filters', $plugin_js, 'age_gate_filters');
        $this->loader->add_action('wp_ajax_nopriv_age_gate_filters', $plugin_js, 'age_gate_filters');
        /**
         * Ajax Class Filters
         */

        /**
         * Registration Class Actions
         */
        // $this->loader->add_action( 'register_form',  $plugin_registration, 'extend_registration_form' );
        // $this->loader->add_action( 'login_enqueue_scripts', $plugin_registration, 'register_style' );
        // $this->loader->add_filter( 'registration_errors', $plugin_registration, 'extend_registration_form_show_errors', 10, 3 );
        // $this->loader->add_action( 'user_register', $plugin_registration, 'extend_registration_user_data', 10, 1 );

        /**
         * Registration Class Filters
         */

        /**
         * Multi lingual
         */
        $this->loader->add_action("init", $plugin_lang, 'is_multi_lingual');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     * @deprecated 2.0.0
     */
    public function get_plugin_name()
    {
        log_message('Warning: the "get_plugin_name" method has been deprecated in version 2.0.0');
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Age_Gate_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        log_message('Warning: the "get_version" method has been deprecated in version 2.0.0');
        return $this->version;
    }
}
