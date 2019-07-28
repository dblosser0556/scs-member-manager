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
class Scsmm_Admin
{

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
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/scsmm-admin.css', array(), $this->version, 'all');

		// include bootstrap from common directory
		wp_enqueue_style($this->plugin_name . '-load-bs-admin',  plugins_url('includes/css/bootstrap.min.css', __DIR__), array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

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

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/scsmm-admin.js', array('jquery'), $this->version, false);

		// add the bootstrap scripts

		wp_enqueue_script($this->plugin_name . "bootstrap-admin", plugins_url('includes/js/bootstrap.bundle.min.js', __DIR__), array('jquery'), $this->version, false);
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
		require_once plugin_dir_path(__FILE__) . 'partials/scsmm-admin-membership.php';
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
		return $wpdb->get_results("SELECT memberships.*, membership_types.name as type,
			status_types.name as status FROM 
			{$this->getTable('member_list')} as memberships,
	   		{$this->getTable('membership_types')} as membership_types,
			{$this->getTable('status_types')} as status_types   
				WHERE membership_types.id=memberships.membershiptypeid AND
					status_types.id=memberships.statusid");
	}


	public function delete_membership($id)
	{
		global $wpdb;
		$table = $this->getTable('member_list');
		return $wpdb->get_delete($table, array('id' => $id));
	}


	public function get_relationships()
	{
		global $wpdb;
		$table_name = $this->getTable('relationship_types');
		return $wpdb->get_results("SELECT * FROM $table_name ORDER BY name");
	}


	public function get_membership_types()
	{
		global $wpdb;
		$table_name = $this->getTable('membership_types');
		return $wpdb->get_results("SELECT * FROM $table_name ORDER BY name");
	}

	public function update_membership_type_ajax()
	{
		global $wpdb;
		PC::debug($_REQUEST);
		
		if (
			!isset($_REQUEST['requestType'])  ||
			!isset($_REQUEST['name']) 
		) {
			return $this->handleError('Missing Data.');
		}

		PC::debug('step2');
		$table_types = $this->getTable('membership_types');
		$requestType = $_REQUEST['requestType'];
		if ($requestType == 'get') {
			$id = $_REQUEST['id'];



			$type = $wpdb->get_row("SELECT *
				FROM $table_types as membership_types
					WHERE membership_types.id= $id ");
			$out = json_encode(array('success' => 1, 'result' => $type));
			echo $out;
			die;
		}
		// handle update request
		if ($requestType = 'update') {
			$count = $wpdb->update(
				$table_types,
				array(
					'name' => $_REQUEST['name'],
					'description' => $_REQUEST['description'],
					'cost' => $_REQUEST['cost']
				),
				array('id' => $_REQUEST['id']),
				array(
					'%s',
					'%s',
					'%d'
				)
			);

			if (!$count) {
				return $this->handleError('Something went wrong.');
			}

			$results['success'] = true;
			$results['msg'] = 'Member type successfully updated.';
			print_r(json_encode($results));
			die();
		}

		// handle new request
		if ($requestType = 'create') {
			$count = $wpdb->insert(
				$table_types,
				array(
					'name' => $_REQUEST['name'],
					'description' => $_REQUEST['description'],
					'cost' => $_REQUEST['cost']
				),
				array(
					'%s',
					'%s',
					'%d'
				)
			);

			if (!$count) {
				return $this->handleError('Something went wrong.');
			}

			$results['success'] = true;
			$results['msg'] = 'Member type successfully created.';
			print_r(json_encode($results));
			die();
		}
	}

	public function member_registration()
	{
		PC::debug(json_encode($_REQUEST));
		global $wpdb;

		if (
			!isset($_REQUEST['id']) ||
			!isset($_REQUEST['dependantCount']) ||
			!isset($_REQUEST['firstname']) ||
			!isset($_REQUEST['lastname']) ||
			!isset($_REQUEST['email']) ||
			!isset($_REQUEST['username'])
		) return $this->handleError('Missing Data.');

		$id = (int) $_REQUEST['id'];
		$dependantCount = $_REQUEST['dependantCount'];

		$member_table = $this->getTable('member_list');

		if ($id == 0) {
			// insert
			$count = $wpdb->insert(
				$member_table,
				array(
					'username' => $_REQUEST['username'],
					'firstname' => $_REQUEST['firstname'],
					'lastname' => $_REQUEST['lastname'],
					'address1' => $_REQUEST['address1'],
					'address2' => $_REQUEST['address2'],
					'city' => $_REQUEST['city'],
					'state' => $_REQUEST['state'],
					'zipcode' => $_REQUEST['zipcode'],
					'phone' => $_REQUEST['phone'],
					'mobile' => $_REQUEST['mobile'],
					'employer' => $_REQUEST['employer'],
					'email' => $_REQUEST['email'],
					'notes' => $_REQUEST['notes'],
					'membershiptypeid' => (int) $_REQUEST['membershiptypeid'],
					'statusid' => 1 // default in 1 (new) for insterted members
				),
				array(
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%d',
					'%d'
				)
			);

			if (!$count) {
				return $this->handleError('Something went wrong.');
			}

			$membershipid = $wpdb->insert_id;

			for ($i = 1; $i <= $dependantCount; $i++) {
				$this->insertDependant($membershipid, $i);
				// check for errors	
				if (!$count) {
					return $this->handleError('Something went wrong.');
				}
			}
			$results['success'] = true;
			$results['msg'] = 'Application successfully submitted. We will be back shortly.';
			print_r(json_encode($results));
			die();
		} else {
			// update
			$count = $wpdb->update(
				$member_table,
				array(
					'username' => $_REQUEST['username'],
					'firstname' => $_REQUEST['firstname'],
					'lastname' => $_REQUEST['lastname'],
					'address1' => $_REQUEST['address1'],
					'address2' => $_REQUEST['address2'],
					'city' => $_REQUEST['city'],
					'state' => $_REQUEST['state'],
					'zipcode' => $_REQUEST['zipcode'],
					'phone' => $_REQUEST['phone'],
					'mobile' => $_REQUEST['mobile'],
					'employer' => $_REQUEST['employer'],
					'email' => $_REQUEST['email'],
					'notes' => $_REQUEST['notes'],
					'membershiptypeid' => (int) $_REQUEST['membershiptypeid'],
					'statusid' => (int) $_REQUEST['statusid']
				),
				array('id' => $_REQUEST['id']),
				array(
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%d',
					'%d'
				)
			);

			for ($i = 1; $i <= $dependantCount; $i++) {
				if ((int) $_REQUEST['id' . $i] > 0) {
					$count = $this->updateDependant($membershipid, $i);
				} else {
					$count = $this->insertDependant($membershipid, $i);
				}

				// check for errors	
				if (!$count) {
					return $this->handleError('Something went wrong.');
				}
			}
			$results['success'] = true;
			$results['msg'] = 'Member details successfully updated.';
			print_r(json_encode($results));
			die();
		}
	}

	function insertDependant($membershipid, $i)
	{
		global $wpdb;
		$dependant_table = $this->getTable('dependantlist');

		return $wpdb->insert(
			$dependant_table,
			array(
				'firstname' => $_REQUEST['firstname' . $i],
				'lastname' => $_REQUEST['lastname' . $i],
				'phone' => $_REQUEST['phone' . $i],
				'mobile' => $_REQUEST['mobile' . $i],
				'email' => $_REQUEST['email' . $i],
				'membershipid' => $membershipid,
				'relationshipid' => $_REQUEST['relationshipid' . $i]
			),
			array(
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%d'
			)
		);
	}

	function updateDependant($membershipid, $i)
	{
		global $wpdb;
		$dependant_table = $this->getTable('dependantlist');

		return $wpdb->insert(
			$dependant_table,
			array(
				'firstname' => $_REQUEST['firstname' . $i],
				'lastname' => $_REQUEST['lastname' . $i],
				'phone' => $_REQUEST['phone' . $i],
				'mobile' => $_REQUEST['mobile' . $i],
				'email' => $_REQUEST['email' . $i],
				'membershipid' => $membershipid,
				'relationshipid' => $_REQUEST['relationshipid' . $i]
			),
			array('id' => $_REQUEST['id' . $i]),
			array(
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%d'
			)
		);
	}
	private function handleError($msg, $code = 400)
	{
		status_header($code);
		$results['success'] = false;
		$results['msg'] = $msg;
		echo json_encode($results);
		die();
	}
}
