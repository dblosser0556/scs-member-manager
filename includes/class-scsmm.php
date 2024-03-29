<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/dblosser0556
 * @since      1.0.0
 *
 * @package    Scsmm
 * @subpackage Scsmm/includes
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
 * @package    Scsmm
 * @subpackage Scsmm/includes
 * @author     David Blosser <blosserdl@gmail.com>
 */
class Scsmm
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Scsmm_Loader    $loader    Maintains and registers all hooks for the plugin.
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
        if (defined('PLUGIN_VERSION')) {
            $this->version = PLUGIN_VERSION;
        } else {
            $this->version = '1.0.0';
        }

        if (defined('PLUGIN_TEXT_DOMAIN')) {
            $this->plugin_name = PLUGIN_TEXT_DOMAIN;
        } else {
            $this->plugin_name = 'scsmm';
        }
    

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_common_hooks();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Scsmm_Loader. Orchestrates the hooks of the plugin.
     * - Scsmm_i18n. Defines internationalization functionality.
     * - Scsmm_Admin. Defines all hooks for the admin area.
     * - Scsmm_Public. Defines all hooks for the public side of the site.
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
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-scsmm-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-scsmm-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-scsmm-admin.php';
        
        /**
         * The class responsible for defining common filters .
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-scsmm-common.php';


        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-scsmm-public.php';

        $this->loader = new Scsmm_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Scsmm_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new Scsmm_i18n();

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

        $plugin_admin = new Scsmm_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        // Add the add reservation post back
        $this->loader->add_action('wp_ajax_member_application', $plugin_admin, 'member_application');
        $this->loader->add_action('wp_ajax_nopriv_member_application', $plugin_admin, 'member_application');

        // add the handler for registration post back
        $this->loader->add_action('wp_ajax_register_member', $plugin_admin, 'register_member');
        $this->loader->add_action('wp_ajax_nopriv_register_member', $plugin_admin, 'register_member');

         // add the handler for registration post back
         $this->loader->add_action('wp_ajax_contact_form', $plugin_admin, 'contact_form');
         $this->loader->add_action('wp_ajax_nopriv_contact_form', $plugin_admin, 'contact_form');

        // Add menu item
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_scsmm_admin_menu');

        // Save/update our plugin settings
        $this->loader->add_action('admin_init', $plugin_admin, 'settings_update');
    }



    private function define_common_hooks() {
        $plugin_common = new Scsmm_Common($this->get_plugin_name(), $this->get_version());


        $this->loader->add_filter('send_email_with_template', $plugin_common, 'send_email_with_template', 10, 4);
        $this->loader->add_filter('get_plugin_table', $plugin_common, 'get_plugin_table', 10, 1);
        $this->loader->add_filter('get_email_templates', $plugin_common, 'get_email_templates', 10, 1);
        $this->loader->add_filter('save_contact', $plugin_common, 'save_contact', 10, 1);
        

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

        $plugin_public = new Scsmm_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        $this->loader->add_shortcode('scsmm-apply', $plugin_public, 'apply_shortcode');
        $this->loader->add_shortcode('scsmm-registration-check', $plugin_public, 'registration_check_shortcode');
        $this->loader->add_shortcode('scsmm-contact-form', $plugin_public, 'contact_form_shortcode');
        
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
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Scsmm_Loader    Orchestrates the hooks of the plugin.
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
        return $this->version;
    }

}
