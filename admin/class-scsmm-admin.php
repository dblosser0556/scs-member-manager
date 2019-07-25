<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/dblosser0556
 * @since      1.0.0
 *
 * @package    Scsmm
 * @subpackage Scsmm/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Scsmm
 * @subpackage Scsmm/admin
 * @author     David Blosser <blosserdl@gmail.com>
 */
class Scsmm_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/scsmm-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-load-bs',  plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/scsmm-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . "bootstrap", plugin_dir_url( __FILE__ ) . 'js/bootstrap.bundle.min.js', array( 'jquery' ), $this->version, false );
	}


	public function add_scsmm_admin_menu()
	{

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 */
		//add_options_page( 'Reserve Me resource Setup', 'Add resources', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page')
		//);
		add_menu_page(
			'Manage Memberships',
			__('Manage Members', 'scsmm'),
			'manage_options',
			$this->plugin_name,
			array($this, 'load_admin_memberships'),
			'dashicons-groups',
			6
		);

		// Membership list
		add_submenu_page(
			$this->plugin_name,
			'Memberships',
			__('Memberships', 'scsmm'),
			'manage_options',
			$this->plugin_name,
			array($this, 'load_admin_memberships')
		);

		// Edit Membership
		add_submenu_page(
			null,
			'Edit Membership',
			__('Membership', 'scsmm'),
			'manage_options',
			($this->plugin_name) . '-membership',
			array($this, 'load_admin_membership')
		);
	

		// Membership types list
		add_submenu_page(
			$this->plugin_name,
			'Membership Types',
			__('Membership Types', 'scsmm'),
			'manage_options',
			($this->plugin_name) . '-membership-types',
			array($this, 'load_admin_membership_types')
		);
		// Membership type edit page.
		add_submenu_page(
			null,
			'Edit Membership Type',
			__('Edit Membership Type', 'scsmm'),
			'manage_options',
			($this->plugin_name) . '-membership-type',
			array($this, 'load_admin_membership_type')
		);

		// aRelationship types list
		add_submenu_page(
			$this->plugin_name,
			'Relationship Types',
			__('Relationship Types', 'scsmm'),
			'manage_options',
			($this->plugin_name) . '-relationship-types',
			array($this, 'load_admin_relationship_types')
		);

		// Relatioinship type edit page.
		add_submenu_page(
			null,
			'Edit Relationship Type',
			'Edit Relationship Type',
			'manage_options',
			($this->plugin_name) . '-relationship-type',
			array($this, 'load_admin_relatiohship_type')
		);
	}

	public function load_admin_memberships()
	{
		require_once plugin_dir_path(__FILE__) . 'partials/scsmm-admin-memberships.php';
	}

	public function load_admin_membership()
	{
		$path = realpath(__DIR__.'/../public/partials/scsmm-public-membership.php');
		require_once $path;
	}

	public function load_admin_membership_types()
	{
		require_once plugin_dir_path(__FILE__) . 'partials/scsmm-admin-membership-types.php';
	}

	public function load_admin_membership_type()
	{
		require_once plugin_dir_path(__FILE__) . 'partials/scsmm-admin-membership-type.php';
	}

	public function load_admin_relationship_types()
	{
		require_once plugin_dir_path(__FILE__) . 'partials/scsmm-admin-relationship-types.php';
	}

	public function load_admin_relatiohship_type()
	{
		require_once plugin_dir_path(__FILE__) . 'partials/scsmm-admin-relationship-type.php';
	}

	private function getTable($table)
	{
		global $wpdb;
		return "{$wpdb->prefix}scsmm_{$table}";
	}

	public function get_memberships()
	{
		global $wpdb;
		return $wpdb->get_results("SELECT memberships.*, membership_types.name as type FROM 
			{$this->getTable('memberlist')} as memberships,
	   		{$this->getTable('membership_types')} as membership_types
	    	WHERE membership_types.id=memberships.membershiptypeid");
	}

	public function get_member($memberId)
	{
		global $wpdb;
		return $wpdb->get_row("SELECT memberships.*, membership_types.name as type
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


	public function delete_membership($id)
	{
		global $wpdb;
		$table = $this->getTable('memberlist');
		return $wpdb->get_delete($table, array('id' => $id));
	}


public function get_relationships() {
		global $wpdb;
		$table_name = $this->getTable('relationship_types');
		return $wpdb->get_results("SELECT * FROM $table_name ORDER BY name");
	}

	public function get_relationship_types() {
		global $wpdb;
		$table_name = $this->getTable('relationship_types');
		$relationshiptypes = $wpdb->get_results("SELECT * FROM $table_name ORDER BY name");

		// create an options list to be passed back
		$html="";
		foreach($relationshiptypes as $type){
			
			$html .= '<option value='. $type->id .'>'.$type->name . '</option>';
		
		}
		print_r($html);
		die();
	}


	public function get_membership_types() {
		global $wpdb;
		$table_name = $this->getTable('membership_types');
		$membershiptypes = $wpdb->get_results("SELECT * FROM $table_name ORDER BY name");


		print_r(json_encode($membershiptypes));
		die();
	}


	public function member_registration() {
		PC::debug(json_encode($_REQUEST));

		$id = $_REQUEST['id'];

		if ($id == 0 ) {
			
		}
		$results['success'] = true;
		$results['msg'] = 'Successfully applied';
		print_r(json_encode($results));
		die();
	}

}
