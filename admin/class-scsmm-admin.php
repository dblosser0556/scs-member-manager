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

		// add the callback for the save screen options for the member list page
		add_filter('set-screen-option', array($this, 'list_table_screen_options'), 10, 3);
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
		wp_enqueue_style($this->plugin_name . '-bootstrap',  plugins_url('includes/css/bootstrap.min.css', __DIR__), array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/scsmm-admin.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name . 'membership', PLUGIN_URL . 'includes/js/scsmm-membership.js', array('jquery'), date('h:i:s'), false);
		wp_enqueue_script($this->plugin_name . 'common', PLUGIN_URL . 'includes/js/scsmm-common.js', array('jquery'), date('h:i:s'), false);

		// add the bootstrap scripts

		wp_enqueue_script($this->plugin_name . "bootstrap-admin", PLUGIN_URL . 'includes/js/bootstrap.bundle.min.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name . "validation", PLUGIN_URL .  'includes/js/jquery.validate.min.js',  array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name . "validation-add", PLUGIN_URL . 'includes/js/additional-methods.min.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name . "input-mask", PLUGIN_URL . 'includes/js/jquery.inputmask.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name . "input-mask-bindings", PLUGIN_URL . 'includes/js/inputmask.binding.js', array('jquery'), $this->version, false);
	}

	/**
	 * Loads the admin menu
	 *
	 * @return void
	 */
	public function add_scsmm_admin_menu()
	{

		//add the count of new application as a menu bubble
		$new_app_count = $this->get_new_application_count();
		$page_title = $new_app_count > 0 ? __('Members', 'scsmm') . '<span class="awaiting-mod">' . $new_app_count . '</span>' : __('Members', 'scsmm');

		//add the main membership menu
		$page_hook = add_menu_page(
			'Manage Memberships',
			$page_title,
			'manage_options',
			$this->plugin_name,
			array($this, 'load_member_list_table'),
			'dashicons-groups',
			6
		);
		// load the options list - required for wp_list_table to function
		add_action('load-' . $page_hook, array($this, 'load_member_list_table_screen_options'));



		//add the count of new application as a menu bubble
		// to do   $new_contact_count = $this->get_new_contact_count();
		$new_contact_count = 1;
		$page_title = $new_contact_count > 0 ? __('Contacts', 'scsmm') . '<span class="awaiting-mod">' . $new_contact_count . '</span>' : __('Contacts', 'scsmm');

		//add the main membership menu
		$page_hook = add_submenu_page(
			$this->plugin_name,
			'Contact List',
			$page_title,
			'manage_options',
			$this->plugin_name . '-contact-list',
			array($this, 'load_contact_list_table')
		);
		// load the options list - required for wp_list_table to function
		add_action('load-' . $page_hook, array($this, 'load_contact_list_table_screen_options'));

		// settings
		$page_hook = add_submenu_page(
			$this->plugin_name,
			'Member Manager Settings Setup',
			__('Settings', 'scsmm'),
			'manage_options',
			($this->plugin_name) . '-settings',
			array($this, 'load_admin_settings')
		);
		// load the options list - required for wp_list_table to function
		add_action('load-' . $page_hook, array($this, 'load_membership_types_list_table_screen_options'));
		add_action('load-' . $page_hook, array($this, 'load_relationship_types_list_table_screen_options'));
		add_action('load-' . $page_hook, array($this, 'load_status_list_table_screen_options'));
		add_action('load-' . $page_hook, array($this, 'load_email_templates_list_table_screen_options'));

		// load the help pages for the setting pages.
		add_action('load-' . $page_hook, array($this, 'load_email_templates_list_table_contextual_help'));

		// add a menu link for the email capability
		add_submenu_page(
			$this->plugin_name,
			'Email Member',
			__('Email', 'scsmm'),
			'manage_options',
			($this->plugin_name) . '-email',
			array($this, 'load_admin_email_page')
		);

		// add a call back to Edit Membership so this can be called from code thru a url  
		add_submenu_page(
			null,
			'Member Manager - Edit Membership',
			__('Membership', 'scsmm'),
			'manage_options',
			($this->plugin_name) . '-membership',
			array($this, 'load_admin_membership')
		);
	}
	/**
	 * Call back from menu for the settings page.
	 *
	 * @return void
	 */
	public function load_admin_settings()
	{
		// required call for a wp_table_list prior to display
		$this->membership_type_list_table->prepare_items();
		$this->relationship_type_list_table->prepare_items();
		$this->status_list_table->prepare_items();
		$this->email_template_list_table->prepare_items();

		// load the html 
		require_once PLUGIN_DIR . 'admin/partials/scsmm-admin-settings.php';
	}

	/**
	 * Call back from admin menu to load the email page
	 *
	 * @return void
	 */
	public function load_admin_email_page()
	{
		require_once PLUGIN_DIR . 'admin/partials/scsmm-admin-email.php';
	}
	/**
	 * Call back from the admin menu to load the list of members.
	 *
	 * @return void
	 */
	public function load_member_list_table()
	{
		$this->member_list_table->prepare_items();

		require_once PLUGIN_DIR . 'admin/partials/scsmm-admin-member-list.php';
	}

	/**
	 * Call back from the admin menu to load the list of contacts.
	 *
	 * @return void
	 */
	public function load_contact_list_table()
	{
		$this->contact_list_table->prepare_items();

		require_once PLUGIN_DIR . 'admin/partials/scsmm-admin-contact-list.php';
	}

	/**
	 * Called from the member_list_table page to load the detailed member application and details form
	 *
	 * @return void
	 */
	public function load_admin_membership()
	{


		require_once PLUGIN_DIR . 'admin/partials/scsmm-admin-membership.php';
	}

	/**
	 * Call back on load of member list table page to set up screen paging options
	 *
	 * @return void
	 */
	public function load_member_list_table_screen_options()
	{
		$arguments = array(
			'label' 	=> __('Members Per Page', $this->plugin_name),
			'default'	=> 5,
			'option' 	=> 'members_per_page'
		);

		add_screen_option('per_page', $arguments);
		require_once PLUGIN_DIR . 'admin/includes/libraries/class-wp-list-table.php';
		require_once PLUGIN_DIR . 'admin/includes/class-scsmm-membership-list.php';
		$this->member_list_table = new Member_List_Table($this->plugin_name);
	}

	/**
	 * Call back on load of member list table page to set up screen paging options
	 *
	 * @return void
	 */
	public function load_contact_list_table_screen_options()
	{
		$arguments = array(
			'label' 	=> __('Contacts Per Page', $this->plugin_name),
			'default'	=> 5,
			'option' 	=> 'contacts_per_page'
		);

		add_screen_option('per_page', $arguments);
		require_once PLUGIN_DIR . 'admin/includes/libraries/class-wp-list-table.php';
		require_once PLUGIN_DIR . 'admin/includes/class-scsmm-contact-list.php';
		$this->contact_list_table = new Contact_List_Table($this->plugin_name);
	}
	/*
	 *
	 * Set up the screen options for each of the settings wp-table-list instances
	 * 
	 */
	/**
	 * Call back on load of membership types list table page to set up screen paging options
	 *
	 * @return void
	 */
	public function load_membership_types_list_table_screen_options()
	{
		$arguments = array(
			'label' 	=> __('Types Per Page', $this->plugin_name),
			'default'	=> 5,
			'option' 	=> 'membership_types_per_page'
		);

		add_screen_option('per_page', $arguments);
		require_once PLUGIN_DIR . 'admin/includes/libraries/class-wp-list-table.php';
		require_once PLUGIN_DIR . 'admin/includes/class-scsmm-membership-type-list.php';

		$this->membership_type_list_table = new Membership_Type_List_Table($this->plugin_name);
	}
	/**
	 * Call back on load of status list table page to set up screen paging options
	 *
	 * @return void
	 */
	public function load_status_list_table_screen_options()
	{
		$arguments = array(
			'label' 	=> __('Types Per Page', $this->plugin_name),
			'default'	=> 5,
			'option' 	=> 'status_per_page'
		);

		add_screen_option('per_page', $arguments);
		require_once PLUGIN_DIR . 'admin/includes/libraries/class-wp-list-table.php';
		require_once PLUGIN_DIR . 'admin/includes/class-scsmm-status-list.php';

		$this->status_list_table = new Status_List_Table($this->plugin_name);
	}


	/**
	 * Call back on load of relationship types list table page to set up screen paging options
	 *
	 * @return void
	 */
	public function load_relationship_types_list_table_screen_options()
	{
		$arguments = array(
			'label' 	=> __('Types Per Page', $this->plugin_name),
			'default'	=> 5,
			'option' 	=> 'relationship_types_per_page'
		);

		add_screen_option('per_page', $arguments);
		require_once PLUGIN_DIR . 'admin/includes/libraries/class-wp-list-table.php';
		require_once PLUGIN_DIR . 'admin/includes/class-scsmm-relationship-type-list.php';

		$this->relationship_type_list_table = new Relationship_Type_List_Table($this->plugin_name);
	}
	/**
	 * Call back on load of email templates list table page to set up screen paging options
	 *
	 * @return void
	 */
	public function load_email_templates_list_table_screen_options()
	{
		$arguments = array(
			'label' 	=> __('Emails Per Page', $this->plugin_name),
			'default'	=> 5,
			'option' 	=> 'emails_per_page'
		);

		add_screen_option('per_page', $arguments);
		require_once PLUGIN_DIR . 'admin/includes/libraries/class-wp-list-table.php';
		require_once PLUGIN_DIR . 'admin/includes/class-scsmm-email-templates-list.php';

		$this->email_template_list_table = new Email_Template_List_Table($this->plugin_name);
	}

	/**
	 * Call back from loading the email templates list page to set up the help
	 *
	 * @return void
	 */
	public function load_email_templates_list_table_contextual_help()
	{
		// We are in the correct screen because we are taking advantage of the load-* action (below)

		$screen = get_current_screen();
		//$screen->remove_help_tabs();
		$screen->add_help_tab(array(
			'id'       => $this->plugin_name . '-options-help',
			'title'    => __('Options'),
			'content'  => '<strong>Options</strong></p>'
				. '<p>If your contact email is something other than the default admin email, please set it here.  This will be the email provided to the applicants.</p>'
				. '<p>The application redirect page is the page you want you applications to see upon completion of their application submission</p>'
				. '<p>The contact page and redirect is future use.</p>'
		));

		$screen->add_help_tab(array(
			'id'       => $this->plugin_name . '-types-list-help',
			'title'    => __('Types'),
			'content'  => '<strong>Membership Type</strong></p>'
				. '<p>Hovering over a row in the membership types will display action links that allow you to manage membership types. '
				. 'It is suggested to add new membership types if things like costs change.  The cost can contain text so it is '
				. 'suggested to include the term of the cost.  So $xx/mo or $nnn/year.</p>'
				. '<p>You can perform the following actions:</p>'
				. '<ul><li><p><strong>Edit</strong> opens the edit form that allows you to change the membership information</p></li>'
				. '<li><p><strong>Delete</strong> allow you to delete the type if it is no longer in use. '
				. 'You can also delete multiple types at once by using Bulk Actions.</p></li></ul>'

		));
		$screen->add_help_tab(array(
			'id'       => $this->plugin_name . 'relationship-types-list-help',
			'title'    => __('Relationships'),
			'content'  =>  '<strong>Membership Type</strong></p>'
				. '<p>Hovering over a row in the relationship types will display action links that allow you to manage them.</p> '
				. '<p>You can perform the following actions:</p>'
				. '<ul><li><p><b>Edit</b> opens the edit form that allows you to change the relationship information</p></li>'
				. '<li><b><p>Delete</b> allow you to delete the type if it is no longer in use. '
				. 'You can also delete multiple types at once by using Bulk Actions.</p></li></ul>'
		));
		$screen->add_help_tab(array(
			'id'       => $this->plugin_name . '-status-types-list-help',
			'title'    => __('Status'),
			'content'  => '<strong>Status</strong></p>'
				. '<p>Status provides a simple workflow capability. The workflow can update the final status, and '
				. 'send an email to the member.  The workflow column is used to define this workflow.  The workflow '
				. 'is expecting to be in the following format.  <strong>Title</strong>:<i>final_status=status number</i><b>,</b><i>email=email id</i> '
				. 'The only two action that are currently supported are final_status and email.</p>'
				. '<p><b>Example: </b>  Activate:final_status=8, email=4 where the status id for Active is 8, and the acceptance email is no 4.</p>'
				. '<p>Hovering over a row in the table will display action links that allow you to manage them.</p> '
				. '<p>You can perform the following actions:</p>'
				. '<ul><li><p><b>Edit</b> opens the edit form that allows you to change the status information</p></li>'
				. '<li><p><b>Delete</b> allows you to delete the status if it is no longer in use. '
				. 'You can also delete multiple status at once by using Bulk Actions.</p></li></ul>'
		));
		$screen->add_help_tab(array(
			'id'       => $this->plugin_name . '-email-template-list-help',
			'title'    => __('Email Templates'),
			'content'  => '<strong>Email Templates</strong></p>'
				. '<p>Email templates allow the use of predefined emails to be sent to your members.  These can be part of a '
				. 'status change workflow, or email created for special events.  The capability allows the use of embedding '
				. 'fields into the email that are then filled in with information from the members. For example, you can have a salutatory '
				. 'statement <b>Dear {first_name} {last_name}</b>.  The system will fill in first name and last name from the members current '
				. 'member data. </p>'
				. '<p>Accepted fields are: first_name, last_name, address1, address2, city, state, zipcode. The codes must be enclosed in braces {}</p>'
				. '<p>Hovering over a row in the table will display action links that allow you to manage them.</p> '
				. '<p>You can perform the following actions:</p>'
				. '<ul><li><p><b>Edit</b> opens the edit form that allows you to change the email information<p></li>'
				. '<li><p><b>Delete</b> allows you to delete the email.  <b>The is no check to see if it is part of a workflow.</b> '
				. 'You can also delete multiple status at once by using Bulk Actions.<p></li></ul>'
		));

		//add more help tabs as needed with unique id's

		// Help sidebars are optional
		$screen->set_help_sidebar(
			'<p><strong>' . __('For more information:') . '</strong></p>' .
				'<p><a href="http://wordpress.org/support/" target="_blank">' . _('Support Forums') . '</a></p>'
		);
	}
	/**
	 * Save the screen option setting.
	 *
	 * @param string $status The default value for the filter. Using anything other than false assumes you are handling saving the option.
	 * @param string $option The option name.
	 * @param array  $value  Whatever option you're setting.
	 */
	public function list_table_screen_options($status, $option, $value)
	{
		if ('members_per_page' == $option) return $value;
		if ('emails_per_page' == $option) return $value;
		if ('relationship_types_per_page' == $option) return $value;
		if ('status_per_page' == $option) return $value;
		if ('membership_types_per_page' == $option) return $value;
		if ('contact_per_page' == $option) return $value;

		return $status;
	}

	/**
	 * Internal function to add a consistent prefix to the plugins tables.
	 *
	 * @param string $table
	 * @return void
	 */
	private function getTable($table)
	{
		global $wpdb;
		return "{$wpdb->prefix}scsmm_{$table}";
	}

	/**
	 * Called from ajax from the membership application page to updates to the application/member details 
	 *
	 * @return void
	 */
	public function member_application()
	{
		//PC::debug(json_encode($_REQUEST));
		global $wpdb;

		// check to ensure we know the requester
		if (!check_ajax_referer('scs-member-check-string', 'security')) {
			return $this->handleError('Illegal Request', Requests_Exception_HTTP_401);
		}

		// make sure all of the required fields are sent.
		if (
			!isset($_REQUEST['id']) ||
			!isset($_REQUEST['dependent_count']) ||
			!isset($_REQUEST['first_name']) ||
			!isset($_REQUEST['last_name']) ||
			!isset($_REQUEST['email'])

		) return $this->handleError('Missing Data.');


		// find the id and dependent count
		$id = (int) sanitize_text_field($_REQUEST['id']);
		$dependent_count = (int) sanitize_text_field($_REQUEST['dependent_count']);

		// create a new_application variable for readability
		if ($id  === 0) $new_application = true;
		else $new_application = false;




		$member = array(
			'first_name' => sanitize_text_field($_REQUEST['first_name']),
			'last_name' => sanitize_text_field($_REQUEST['last_name']),
			'address1' => sanitize_text_field($_REQUEST['address1']),
			'address2' => sanitize_text_field($_REQUEST['address2']),
			'city' => sanitize_text_field($_REQUEST['city']),
			'state' => sanitize_text_field($_REQUEST['state']),
			'zipcode' => sanitize_text_field($_REQUEST['zipcode']),
			'phone' => sanitize_text_field($_REQUEST['phone']),
			'mobile' => sanitize_text_field($_REQUEST['mobile']),
			'employer' => sanitize_text_field($_REQUEST['employer']),
			'email' => sanitize_email($_REQUEST['email']),
			'notes' => sanitize_textarea_field($_REQUEST['notes']),
			'membership_type_id' => (int) $_REQUEST['membership_type_id'],
			'status_id' => (int) $_REQUEST['status_id'],
			'join_date' => sanitize_text_field($_REQUEST['join_date'])
		);




		$member_table = $this->getTable('member_list');

		if ($new_application) {

			// insert
			$count = $wpdb->insert(
				$member_table,
				$member
			);

			if (!$count) {
				$wpdb->print_error();
				return $this->handleError('Something went wrong.');
			}

			$membership_id = $wpdb->insert_id;
		} else {
			$count = $wpdb->update(
				$member_table,
				$member,
				array('id' => $id)
			);
			$membership_id = $id;
		}

		for ($i = 0; $i < $dependent_count; $i++) {
			$count = $this->insert_or_update_dependant($membership_id, $i);
			// check for errors	
			if (!$count) {
				return $this->handleError('Something went wrong.');
			}
		}

		$options = get_option($this->plugin_name);
		if (isset($options['member-application-notification-email']) && $options['member-application-notification-email'] != ''  && $new_application) {
			// email admin
			$email_id = $options['member-application-notification-email'];

			$to = $options['email'];
			if ($to == '') $to = get_option('admin_email');

			$sent = apply_filters('send_email_with_template', $email_id, $to, $member['email'], $member);
			//public function send_email_with_template($email_id, $to = '', $from = '', $member = '')
			// email applicant
			if (!$sent) $this->handleError('Something went wrong with sending notification email');
		}

		if (isset($options['member-application-confirmation-email']) && $options['member-application-confirmation-email'] != ''  && $new_application) {

			$email_id = $options['member-application-confirmation-email'];
			$sent = apply_filters('send_email_with_template', $email_id, $to = '', $from = '', $member);
			if (!$sent) $this->handleError('Something went wrong with sending application confirmation email');
		}

		if (isset($options['application-redirect-page'])  && $options['application-redirect-page'] != '' && $new_application) {
			$redirect = true;
		} else {
			$redirect = false;
		}

		// todo: use redirect option here.	
		$results['success'] = true;
		if ($new_application) {
			$results['msg'] = 'Application successfully submitted. We will be back shortly.';
		} else {
			$results['msg'] = 'Application successfully updated.';
		}

		if ($redirect)
			$results['redirect'] = $options['application-redirect-page'];
		print_r(json_encode($results));

		die();
	}
	/**
	 * Called from member application to insert a new dependant.
	 *
	 * @param string $membership_id
	 * @param string $i
	 * @return void
	 * @since 1.0.0
	 */
	private function insert_or_update_dependant($membership_id, $i)
	{
		global $wpdb;
		$dependant_table = $this->getTable('dependent_list');

		$dependant_id = (int) sanitize_text_field($_REQUEST['dep_id'][$i]);
		$dependant = array(
			'first_name' => sanitize_text_field($_REQUEST['dep_first_name'][$i]),
			'last_name' => sanitize_text_field($_REQUEST['dep_last_name'][$i]),
			'phone' => sanitize_text_field($_REQUEST['dep_phone'][$i]),
			'mobile' => sanitize_text_field($_REQUEST['dep_mobile'][$i]),
			'email' => sanitize_text_field($_REQUEST['dep_email'][$i]),
			'membership_id' => $membership_id,
			'relationship_id' => sanitize_text_field($_REQUEST['dep_relationship_id'][$i])
		);

		if ($dependant_id == 0) {

			$count =  $wpdb->insert(
				$dependant_table,
				$dependant
			);

			return $count;
		} else {
			$count = $wpdb->update(
				$dependant_table,
				$dependant,
				array('id' => $dependant_id)
			);
		}

		return $count;
	}

	/**
	 * Ajax call from the register member short code to add a new member.
	 * 
	 * 
	 * @return void
	 */
	public function register_member()
	{
		// check to ensure we know the requester
		if (!check_ajax_referer('scs-registration-check-string', 'security')) {
			return $this->handleError('Illegal Request', Requests_Exception_HTTP_401);
		}


		$registration_id = 		sanitize_text_field($_POST['registration_id']);
		$username   =   		sanitize_user($_POST['username']);
		$password   =   		$_POST['password'];
		$email      =   		sanitize_email($_POST['email']);
		$first_name =   		sanitize_text_field($_POST['first_name']);
		$last_name  =   		sanitize_text_field($_POST['last_name']);
		$membership_number = 	sanitize_text_field($_POST['membership_number']);

		$reg_errors = $this->registration_validation($username, $password, $email, $first_name, $last_name, $membership_number);

		if (count($reg_errors->errors) > 0) {
			$message = "";
			foreach ($reg_errors->get_error_messages() as $error_message) {
				$message .=  $error_message . '<br>';
			}
			$this->handleError($message, '400');
		}

		if ($this->complete_registration($registration_id, $username, $password, $email, $first_name, $last_name, $membership_number)) {
			$results['success'] = true;
			$results['msg'] = 'Registration details successfully added.';

			//check if there is a redirect page and add
			$options = get_option(PLUGIN_TEXT_DOMAIN);
			if (isset($options['registration-success-redirect-page'])) {
				$results['redirect'] = $options['registration-success-redirect-page'];
			}

			print_r(json_encode($results));
			die();
		} else {
			$results['success'] = false;
			$results['msg'] = 'Something went wrong. See your administrator.';
			print_r(json_encode($results));
			die();
		}
	}

	/**
	 * ensure the user has passed the proper data
	 *
	 * @param string $username
	 * @param string $password
	 * @param string $email
	 * @param string $first_name
	 * @param string $last_name
	 * @param string $membership_number
	 * @return array of WP_ERROR
	 */
	private function registration_validation($username, $password, $email, $first_name, $last_name, $membership_number)
	{
		$reg_errors = new WP_Error;

		if (empty($username) || empty($password) || empty($email)  || empty($first_name)  || empty($last_name)) {
			$reg_errors->add('field', 'Required form field is missing');
		}

		if (5 > strlen($username)) {
			$reg_errors->add('username_length', 'Username too short. At least 5 characters is required');
		}

		if (username_exists($username)) {
			$reg_errors->add('user_name', 'Sorry, that username already exists!');
		}

		if (!validate_username($username)) {
			$reg_errors->add('username_invalid', 'Sorry, the username you entered is not valid');
		}

		if (5 > strlen($password)) {
			$reg_errors->add('password', 'Password length must be greater than 5');
		}

		if (!is_email($email)) {
			$reg_errors->add('email_invalid', 'Email is not valid');
		}

		if (email_exists($email)) {
			$reg_errors->add('email', 'Email Already in use');
		}

		return $reg_errors;
	}
	/**
	 * Undocumented function
	 * @param int 		$registation_id
	 * @param string $username
	 * @param string $password
	 * @param string $email
	 * @param string $first_name
	 * @param string $last_name
	 * @param string $membership_number
	 * @return void
	 */
	function complete_registration($registration_id, $username, $password, $email, $first_name, $last_name, $membership_number)
	{
		global $wpdb;

		// get the current registration
		$registration = $wpdb->get_row("SELECT * FROM {$this->getTable('registration_list')} 
			WHERE id = $registration_id", ARRAY_A);

		if (count($registration) == 0) {
			//error
			$this->handleError('This user registration was not found.');
		}

		if (!empty($registration['username'])) {
			//error
			$this->handleError('This user is already registered.');
		}



		// add the new user
		$user_data = array(
			'user_login'    =>   $username,
			'user_email'    =>   $email,
			'user_pass'     =>   $password,
			'first_name'    =>   $first_name,
			'last_name'     =>   $last_name
		);
		$user = wp_insert_user($user_data);
		if (is_wp_error($user)) {
			$this->handleError('Something went wrong when adding user.');
		}

		// update the registration with username and user_id
		$registration['username'] = $username;
		$registration['user_id'] = $user;

		$count = $wpdb->update(
			$this->getTable('registration_list'),
			$registration,
			array('id' => $registration_id)
		);

		if ($count = 0) {
			$this->handleError('Something went wrong in saving registation data');
		}

		$table_name = $this->getTable('member_list');
		$id = $registration['membership_id'];

		// check to see if dependent record
		if ($registration['dependent_id'] > 0) {
			$table_name = $this->getTable('dependent_list');
			$id = $registration['dependent_id'];
		}

		$count = $wpdb->update(
			$table_name,
			array(
				'first_name' => $first_name,
				'last_name' => $last_name,
				'email'		=> $email
			),
			array('id' => $id)
		);

		if ($count === false) {
			$this->handleError('Something went wrong in saving user data');
		}

		return true;
	}

	/**
	 * ajax call from the contract form to save the contact in the database.
	 *
	 * @return void
	 */
	public function contact_form()
	{
		// check to ensure we know the requester
		if (!check_ajax_referer('scs-contact-form-check-string', 'security')) {
			return $this->handleError('Illegal Request', Requests_Exception_HTTP_401);
		}



		$phone 	= 				sanitize_text_field($_POST['phone']);
		$email      =   		sanitize_email($_POST['email']);
		$first_name =   		sanitize_text_field($_POST['first_name']);
		$last_name  =   		sanitize_text_field($_POST['last_name']);
		$comments = 			sanitize_textarea_field($_POST['comments']);
		$contact_date = 		sanitize_text_field($_POST['contact_date']);

		$reg_errors = $this->contact_validation($email, $first_name, $last_name);

		if (count($reg_errors->errors) > 0) {
			$message = "";
			foreach ($reg_errors->get_error_messages() as $error_message) {
				$message .=  $error_message . '<br>';
			}
			$this->handleError($message, '400');
		}


		if ($this->complete_contact_request($email, $first_name, $last_name, $phone, $comments, $contact_date)) {
			$results['success'] = true;
			$results['msg'] = 'Contact details successfully added.';

			//check if there is a redirect page and add
			$options = get_option(PLUGIN_TEXT_DOMAIN);
			if (isset($options['contact-success-redirect-page'])) {
				$results['redirect'] = $options['contact-success-redirect-page'];
			}

			print_r(json_encode($results));
			die();
		} else {
			$results['success'] = false;
			$results['msg'] = 'Something went wrong. See your administrator.';
			print_r(json_encode($results));
			die();
		}
	}

	private function contact_validation($email, $first_name, $last_name)
	{

		$reg_errors = new WP_Error;

		if (empty($email)  || empty($first_name)  || empty($last_name)) {
			$reg_errors->add('field', 'Required form field is missing');
		}


		return $reg_errors;
	}

	private function complete_contact_request($email, $first_name, $last_name, $phone, $comments, $contact_date)
	{
		global $wpdb;

		$contact = array(
			'first_name' => $first_name,
			'last_name' => $last_name,
			'email' => $email,
			'phone' => $phone,
			'comments' => $comments,
			'contact_date' => $contact_date
		);

		$count = $wpdb->insert(
			$this->getTable('contact_list'),
			$contact
		);

		return $count;
	}
	// use to save the settings from the settings page
	public function settings_update()
	{
		register_setting($this->plugin_name, $this->plugin_name, array($this, 'validate_settings'));
	}

	/**
	 * callback used to validate the input on the settings page.
	 *
	 * @param array $input
	 * @since 1.0.0
	 */
	public function validate_settings($input)
	{
		// todo: - this needs work
		// all inputs
		$valid = array();

		$valid['email'] = sanitize_email($input['email']);
		$valid['lifetime'] = sanitize_text_field($input['lifetime']);

		$valid['registration-check-page'] = (isset($input['registration-check-page']) && !empty($input['registration-check-page'])) ? esc_url($input['registration-check-page']) : false;
		$valid['registration-check-redirect-page'] = (isset($input['registration-check-redirect-page']) && !empty($input['registration-check-redirect-page'])) ? esc_url($input['registration-check-redirect-page']) : false;
		$valid['registration-expired-redirect-page'] = (isset($input['registration-expired-redirect-page']) && !empty($input['registration-expired-redirect-page'])) ? esc_url($input['registration-expired-redirect-page']) : false;
		$valid['registration-not-found-redirect-page'] = (isset($input['registration-not-found-redirect-page']) && !empty($input['registration-not-found-redirect-page'])) ? esc_url($input['registration-not-found-redirect-page']) : false;
		$valid['application-redirect-page'] = (isset($input['application-redirect-page']) && !empty($input['application-redirect-page'])) ? esc_url($input['application-redirect-page']) : false;

		$valid['contact-page'] = (isset($input['contact-page']) && !empty($input['contact-page'])) ? esc_url($input['contact-page']) : false;
		$valid['contact-success-redirect-page'] = (isset($input['contact-success-redirect-page']) && !empty($input['contact-success-redirect-page'])) ? esc_url($input['contact-success-redirect-page']) : false;
		$valid['registration-success-redirect-page'] = (isset($input['registration-success-redirect-page']) && !empty($input['registration-success-redirect-page'])) ? esc_url($input['registration-success-redirect-page']) : false;
		$valid['member-application-notification-email'] = (isset($input['member-application-notification-email']) && !empty($input['member-application-notification-email'])) ? $input['member-application-notification-email'] : false;
		$valid['member-application-confirmation-email'] = (isset($input['member-application-confirmation-email']) && !empty($input['member-application-confirmation-email'])) ? $input['member-application-confirmation-email'] : false;
		$valid['member-application-approved-email'] = (isset($input['member-application-approved-email']) && !empty($input['member-application-approved-email'])) ? $input['member-application-approved-email'] : false;


		return $valid;
	}

	/**
	 * Find the count of new membership applications
	 *
	 * @return integer
	 */
	private function get_new_application_count()
	{
		global $wpdb;

		$sql = "SELECT memberships.* FROM 
			{$this->getTable('member_list')} as memberships,
			{$this->getTable('status_types')} as status_types   
				WHERE status_types.id=memberships.status_id AND
					status_types.status_key	= 'new'";
		$results = $wpdb->get_results($sql, ARRAY_A);

		if (isset($results)) {
			return count($results);
		} else {
			return 0;
		}
	}

	/**
	 * handle errors in the ajax process.
	 *
	 * @param string $msg
	 * @param integer $code
	 * @return void
	 */
	private function handleError($msg = '', $code = 400)
	{
		status_header($code);
		//$results['success'] = false;
		//$results['msg'] = $msg;
		echo $msg;
		die();
	}
}
