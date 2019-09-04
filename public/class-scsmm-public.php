<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/dblosser0556
 * @since      1.0.0
 *
 * @package    Scsmm
 * @subpackage Scsmm/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Scsmm
 * @subpackage Scsmm/public
 * @author     David Blosser <blosserdl@gmail.com>
 */
class Scsmm_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Scsmm_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Scsmm_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, PLUGIN_URL . 'public/css/scsmm-public.css', array(), date('h:i:s'), 'all' );
		// include bootstrap from common directory
		wp_enqueue_style( $this->plugin_name . '-load-bs',  PLUGIN_URL . 'includes/css/bootstrap.min.css' , array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Scsmm_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Scsmm_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// add the custom script
		wp_enqueue_script( $this->plugin_name, PLUGIN_URL . 'public/js/scsmm-public.js', array( 'jquery' ), date('h:i:s'), false );
		
		// add variables 
		$options = get_option( PLUGIN_TEXT_DOMAIN);

		if(isset($options['register-success-redirect-page']) ) {
			$register_success_redirect = $options['registration-success-redirect-page'];
		} else {
			$register_success_redirect = wp_login_url();
		}

		if(isset($options['contact-success-redirect-page']) ) {
			$contact_success_redirect = $options['contact-success-redirect-page'];
		} else {
			$contact_success_redirect = home_url();
		}

		$script = 'register_success_redirect = ' . json_encode($register_success_redirect) . '; ';
		$script = 'contact_success_redirect = ' . json_encode($contact_success_redirect) . '; ';
		wp_add_inline_script($this->plugin_name, $script, 'before'); 
		
		
		// enqueue the the jQuery plugins  
		wp_enqueue_script( $this->plugin_name . "bootstrap", PLUGIN_URL . 'includes/js/bootstrap.bundle.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . "validation", PLUGIN_URL .  'includes/js/jquery.validate.min.js',  array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . "validation-add", PLUGIN_URL . 'includes/js/additional-methods.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . "masked-input", PLUGIN_URL .  'includes/js/jquery.maskedinput.min.js',  array( 'jquery' ), $this->version, false );
	}

	
	public function apply_shortcode( $atts, $content = null ) {
		ob_start();
		include_once( 'partials/'.$this->plugin_name.'-public-membership.php' );
		return ob_get_clean();
	}

	public function registration_check_shortcode ( $atts, $content = null ) {
		ob_start();
		include_once( 'partials/'.$this->plugin_name.'-public-registration-check.php' );
		return ob_get_clean();
	}

	public function contact_form_shortcode(  $atts, $content = null ) {
		ob_start();
		include_once( 'partials/'.$this->plugin_name.'-public-contact-form.php' );
		return ob_get_clean();

	}
}
