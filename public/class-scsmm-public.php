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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/scsmm-public.css', array(), date('h:i:s'), 'all' );
		wp_enqueue_style( $this->plugin_name . '-load-bs',  plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), $this->version, 'all' );
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/scsmm-public.js', array( 'jquery' ), date('h:i:s'), false );
		wp_enqueue_script( $this->plugin_name . "bootstrap", plugin_dir_url( __FILE__ ) . 'js/bootstrap.bundle.min.js', array( 'jquery' ), $this->version, false );
	}

	public function public_shortcode( $atts, $content = null ) {
		ob_start();
		include_once( 'partials/'.$this->plugin_name.'-public-membership.php' );
		return ob_get_clean();
	}


	public function get_member($memberId)
	{
		global $wpdb;
		return $wpdb->get_results("SELECT memberships.*, membership_types.name as type
		FROM {$this->getTable('memberlist')} as memberships,
	   		{$this->getTable('membership_types')} as membership_types
	    	WHERE membership_types.id=memberships.membershiptypeid  
				AND memberships.id = " . $memberId);
	}

	public function get_dependants($memberId)
	{
		global $wpdb;
		return $wpdb->get_results("SELECT dependents.*, relationship_types.name as type FROM 
			{$this->getTable('dependentlist')} as dependents,
	   		{$this->getTable('relationship_types')} as relationship_types
	    	WHERE relationship_types.id=dependents.relationshipid
				AND dependents.membershipid = " . $memberId);
	}
}
