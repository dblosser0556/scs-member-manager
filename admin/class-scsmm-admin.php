<?php


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

	/* 
	 * Instance of the member list table class to be 
	 * used to render the list of members
	 */
	private $member_list_table;
	
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
			'Member Manager - Memberships',
			__('Memberships', 'scsmm'),
			'manage_options',
			$this->plugin_name,
			array($this, 'load_admin_memberships')
		);

		// Edit Membership
		add_submenu_page(
			null,
			'Member Manager - Edit Membership',
			__('Membership', 'scsmm'),
			'manage_options',
			($this->plugin_name) . '-membership',
			array($this, 'load_admin_membership')
		);


		// Membership types list
		add_submenu_page(
			$this->plugin_name,
			'Member Manager - Membership Types',
			__('Membership Types', 'scsmm'),
			'manage_options',
			($this->plugin_name) . '-membership-types',
			array($this, 'load_admin_membership_types')
		);


		// aRelationship types list
		add_submenu_page(
			$this->plugin_name,
			'Member Manager Relationship Types',
			__('Relationship Types', 'scsmm'),
			'manage_options',
			($this->plugin_name) . '-relationship-types',
			array($this, 'load_admin_relationship_types')
		);

		// a status types list
		add_submenu_page(
			$this->plugin_name,
			'Member Manager Status Setup',
			__('Status', 'scsmm'),
			'manage_options',
			($this->plugin_name) . '-status-types',
			array($this, 'load_admin_status_types')
		);

		// a status types list
		add_submenu_page(
			$this->plugin_name,
			'Member Manager Settings Setup',
			__('Settings', 'scsmm'),
			'manage_options',
			($this->plugin_name) . '-settings',
			array($this, 'load_admin_settings')
		);

		$page_hook = add_submenu_page(
			$this->plugin_name,
			'Member List',
			__('Member List', $this->plugin_name),
			'manage_options',
			($this->plugin_name) . '-member-list',
			array($this, 'load_member_list_table')
		);

		add_action('load-' . $page_hook, array($this, 'load_member_list_table_screen_options'));
	}

	public function load_admin_settings()
	{
		require_once plugin_dir_path(__FILE__) . 'partials/scsmm-admin-settings.php';
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


	public function load_admin_relationship_types()
	{
		require_once plugin_dir_path(__FILE__) . 'partials/scsmm-admin-relationship-types.php';
	}

	public function load_admin_status_types()
	{
		require_once plugin_dir_path(__FILE__) . 'partials/scsmm-admin-status-types.php';
	}

	public function load_member_list_table()
	{ 
		$this->member_list_table->prepare_items();
		
		include_once plugin_dir_path(__FILE__) . 'partials/scsmm-admin-member-list.php';
	
	}

	public function load_member_list_table_screen_options()
	{
		$arguments = array(
			'label' 	=> __('Members Per Page', $this->plugin_name),
			'default'	=> 5,
			'options' 	=> 'members_per_page'
		);

		add_screen_option( 'per_page', $arguments); 

		
		include_once plugin_dir_path(__FILE__) . 'class-membership-list.php';
		$memberships = $this->get_memberships();
		$this->member_list_table = new Member_List_Table($this->plugin_name, $memberships);
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

	function scsmm_get_application_redirect()
	{
		return 'page_id=182';
	}

	public function update_type_ajax()
	{
		global $wpdb;
		PC::debug($_REQUEST);

		if (
			!isset($_REQUEST['requestType'])  ||
			!isset($_REQUEST['name']) ||
			!isset($_REQUEST['tableName'])
		) {
			return $this->handleError('Missing Data.');
		}

		$tableName = $_REQUEST['tableName'];
		$requestType = $_REQUEST['requestType'];
		$id = (int) $_REQUEST['id'];

		$table_types = $this->getTable($_REQUEST['tableName']);
		if ($requestType == 'delete') {
			$count = $wpdb->delete(
				$table_types,
				array('id' => $id)

			);

			if (!$count) {
				return $this->handleError('Something went wrong.');
			}

			$results['success'] = true;
			$results['msg'] = 'Type was successfully deleted.';
			print_r(json_encode($results));
			die();
		}


		if ($requestType == 'get') {


			$type = $wpdb->get_row("SELECT *
				FROM $table_types as types
					WHERE types.id= $id ");
			$out = json_encode(array('success' => 1, 'result' => $type));
			echo $out;
			die;
		}

		if ($requestType == 'save') {
			if ($tableName == 'membership_types') {
				$inputArray = array(
					'name' => $_REQUEST['name'],
					'description' => $_REQUEST['description'],
					'cost' => $_REQUEST['cost']
				);
				$typeArray = array(
					'%s',
					'%s',
					'%d'
				);
			} elseif ($tableName == 'relationship_types') {
				$inputArray = array(
					'name' => $_REQUEST['name']
				);
				$typeArray = array(
					'%s',
				);
			} elseif ($tableName == 'status_types') {
				$inputArray = array(
					'name' => $_REQUEST['name'],
					'work_flow_action' => $_REQUEST['work_flow_action'],
					'work_flow_order' => $_REQUEST['work_flow_order']
				);
				$typeArray = array(
					'%s',
					'%s',
					'%d'
				);
			}
		}
		// handle update request
		if ($requestType == 'save' && $id > 0) {


			$count = $wpdb->update(
				$table_types,
				$inputArray,
				array('id' => $_REQUEST['id']),
				$typeArray
			);

			if (!$count) {
				return $this->handleError('Something went wrong.');
			}

			$results['success'] = true;
			$results['msg'] = 'Type successfully updated.';
			print_r(json_encode($results));
			die();
		}

		// handle new request
		if ($requestType = 'save' && $id == 0) {
			$count = $wpdb->insert(
				$table_types,
				$inputArray,
				$typeArray
			);

			if (!$count) {
				return $this->handleError('Something went wrong.');
			}

			$results['success'] = true;
			$results['msg'] = 'Type successfully created.';
			print_r(json_encode($results));
			die();
		}
	}

	// handle member registration insert and update
	// this is an ajax call.
	public function member_application()
	{
		PC::debug(json_encode($_REQUEST));
		global $wpdb;

		// check to ensure we know the requester
		if (!check_ajax_referer('scs-member-check-string', 'security')) {
			return $this->handleError('Illegal Request');
		}

		// make sure all of the required fields are sent.
		if (
			!isset($_REQUEST['id']) ||
			!isset($_REQUEST['dependantCount']) ||
			!isset($_REQUEST['firstname']) ||
			!isset($_REQUEST['lastname']) ||
			!isset($_REQUEST['email']) ||
			!isset($_REQUEST['username'])
		) return $this->handleError('Missing Data.');

		// sanitize all the fields.
		$id = (int) sanitize_text_field($_REQUEST['id']);
		$dependantCount = sanitize_text_field($_REQUEST['dependantCount']);
		$firstname = sanitize_text_field($_REQUEST['firstname']);
		$lastname = sanitize_text_field($_REQUEST['lastname']);
		$email = sanitize_email($_REQUEST['email']);
		$notes = sanitize_textarea_field($_REQUEST['notes']);

		$member_table = $this->getTable('member_list');

		if ($id == 0) {
			// insert
			$count = $wpdb->insert(
				$member_table,
				array(
					'username' => sanitize_text_field($_REQUEST['username']),
					'firstname' => $firstname,
					'lastname' => $lastname,
					'address1' => sanitize_text_field($_REQUEST['address1']),
					'address2' => sanitize_text_field($_REQUEST['address2']),
					'city' => sanitize_text_field($_REQUEST['city']),
					'state' => sanitize_text_field($_REQUEST['state']),
					'zipcode' => sanitize_text_field($_REQUEST['zipcode']),
					'phone' => sanitize_text_field($_REQUEST['phone']),
					'mobile' => sanitize_text_field($_REQUEST['mobile']),
					'employer' => sanitize_text_field($_REQUEST['employer']),
					'email' => $email,
					'notes' => $notes,
					'membershiptypeid' => (int) $_REQUEST['membershiptypeid'],
					'statusid' => (int) $_REQUEST['statusid'] // default in 1 (new) for insterted members
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
				$wpdb->print_error();
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
			// email admin
			$emailTo = get_option($this->plugin_name . '_email');
			if (!isset($emailTo) || ($emailTo == '')) {
				$emailTo = get_option('admin_email');
			}
			$subject = 'Application Request From ' . $firstname . ' ' . $lastname;
			$body = "Application Request for Name: $firstname . ' ' . $lastname \n\nEmail: $email \n\nComments: $notes";
			$headers = 'From: ' . $firstname . ' ' . $lastname . ' <' . $email . '>' . "\r\n" . 'Reply-To: ' . $emailTo;

			wp_mail($emailTo, $subject, $body, $headers);
			$emailSent = true;

			// email applicant

			$subject = 'Terrytown Country Club Application';
			$body = "Thanks for applying to Terrytown Country Club.  We have your application and will be reviewing it shortly.";
			$headers = 'From: ' . $firstname . ' ' . $lastname . ' <' . $emailTo . '>' . "\r\n" . 'Reply-To: ' . $email;

			wp_mail($emailTo, $subject, $body, $headers);
			$emailSent = true;

			$results['success'] = true;
			$results['msg'] = 'Application successfully submitted. We will be back shortly.';
			print_r(json_encode($results));


			if (!empty($this->scsmm_get_application_redirect())) {
				$redirect = $this->scsmm_get_application_redirect();
			}


			wp_safe_redirect($redirect);



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

	// use to save the settings from the settings page
	public function settings_update()
	{
		register_setting($this->plugin_name, $this->plugin_name, array($this, 'validate_settings'));
	}

	// used to validate the input on the settings page.
	public function validate_settings($input)
	{
		// all inputs
		$valid = array();

		$valid['email'] = sanitize_email($input['email']);
		$valid['contact-page'] = (isset($input['contact-page']) && !empty($input['contact-page'])) ? esc_url($input['contact-page']) : false;
		$valid['contact-redirect-page'] = (isset($input['contact-redirect-page']) && !empty($input['contact-redirect-page'])) ? esc_url($input['contact-redirect-page']) : false;
		$valid['application-page'] = (isset($input['application-page']) && !empty($input['application-page'])) ? esc_url($input['application-page']) : false;
		$valid['application-redirect-page'] = (isset($input['application-redirect-page']) && !empty($input['application-redirect-page'])) ? esc_url($input['application-redirect-page']) : false;

		return $valid;
	}

	// handle errors in the ajax process.
	private function handleError($msg, $code = 400)
	{
		status_header($code);
		$results['success'] = false;
		$results['msg'] = $msg;
		echo json_encode($results);
		die();
	}
}
