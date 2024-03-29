<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/dblosser0556
 * @since      1.0.0
 *
 * @package    Scsmm
 * @subpackage Scsmm/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Scsmm
 * @subpackage Scsmm/includes
 * @author     David Blosser <blosserdl@gmail.com>
 */
class Scsmm_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{

		self::create_status_table();

		self::create_relationship_type_table();

		self::create_membership_type_table();

		self::create_membership_list_table();

		self::create_dependant_list_table();

		self::create_mail_template_table();

		self::create_member_registration_table();

		self::create_contact_list_table();

		// add default data 
		self::add_default_relationship_types();
		self::add_default_membership_types();
		
		// adding emails returns the list of email ids
		$emails = self::add_default_mail_items();
		
		// add the status types with one email workflow
		self::add_default_status_types($emails['member-application-approved-email']);


		// store the current database version for future upgrades
		add_option(PLUGIN_TEXT_DOMAIN . '_db_version', PLUGIN_DB_VERSION);

		// add some default page redirects and save to the options
		$pages = self::add_default_pages();

		// save the appropriate options
		foreach ($pages as $page) {
			$new_page_id = self::add_page($page);
			$options[$page['option']]  = get_permalink($new_page_id);
		}

		// add the email options
		foreach ($emails as $key => $email) {
			$options[$key] = $email;
		}
		// add the none page option defaults
		$options['email'] = get_option('admin_email');
		$options['lifetime'] = '14';

		add_option(PLUGIN_TEXT_DOMAIN, $options);

	}

	
	private static function create_status_table()
	{
		// create tables
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		if (!function_exists('dbDelta')) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		// status types table
		$table_status_types = $wpdb->prefix . 'scsmm_status_types';
		$sql = "CREATE TABLE $table_status_types (
				  id 				mediumint(9) NOT NULL AUTO_INCREMENT,
				  name 				varchar(15) NOT NULL,
				  work_flow_action 	varchar(255),
				  work_flow_order 	smallint(2) NOT NULL,
				  status_key 		varchar(15),
				  PRIMARY KEY  (id)
			  ) $charset_collate;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		dbDelta($sql);
	}

	private static function create_relationship_type_table()
	{

		// create tables
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		if (!function_exists('dbDelta')) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}
		// relationship types table
		$table_relationship_types = $wpdb->prefix . 'scsmm_relationship_types';
		$sql = "CREATE TABLE $table_relationship_types (
				  id mediumint(9) NOT NULL AUTO_INCREMENT,
				  name varchar(15) NOT NULL,
				  PRIMARY KEY  (id)
			  ) $charset_collate;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');


		dbDelta($sql);
	}

	private static function create_membership_type_table()
	{
		// create tables
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		if (!function_exists('dbDelta')) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}
		// membership types table
		$table_membership_types = $wpdb->prefix . 'scsmm_membership_types';
		$sql = "CREATE TABLE $table_membership_types (
					  id mediumint(9) NOT NULL AUTO_INCREMENT,
					  name varchar(20) NOT NULL,
					  description varchar(1024) NULL,
					  cost varchar(1024) NULL,
					  short_name varchar(5) NOT NULL,
					  next_number mediumint(9) NOT NULL,
					  increment tinyint(2) NOT NULL,
 					  PRIMARY KEY  (id)
				  ) $charset_collate;";



		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');


		dbDelta($sql);
	}




	private static function create_membership_list_table()
	{
		// create membership list table
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		if (!function_exists('dbDelta')) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		$table_membership_types = $wpdb->prefix . 'scsmm_membership_types';
		$table_status_types = $wpdb->prefix . 'scsmm_status_types';

		// member_list table
		$table_member_list = $wpdb->prefix . 'scsmm_member_list';
		$sql = "CREATE TABLE $table_member_list (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				membership_number varchar(50),		
				first_name varchar(75) NOT NULL,
				last_name varchar(75) NOT NULL,
				address1 varchar(255) NOT NULL,
				address2 varchar(255),
				city varchar(75) NOT NULL,
				state varchar(2) NOT NULL,
				zipcode varchar(11) NOT NULL,
				phone varchar(20),
				mobile varchar(20),
				employer varchar(255),
				email varchar(75),
				notes varchar(1024),
				membership_type_id mediumint(9),
				status_id mediumint(9),
				join_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				FOREIGN KEY (membership_type_id) REFERENCES {$table_membership_types}(id) ON DELETE CASCADE,
				FOREIGN KEY (status_id) REFERENCES {$table_status_types}(id) ON DELETE CASCADE,
				PRIMARY KEY  (id)
			) $charset_collate;";


		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');


		dbDelta($sql);
	}

	private static function create_member_registration_table()
	{
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		if (!function_exists('dbDelta')) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		$table_member_list = $wpdb->prefix . 'scsmm_member_list';
		$table_registration_list = $wpdb->prefix . 'scsmm_registration_list';

		$sql = "CREATE TABLE $table_registration_list (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			membership_id  mediumint(9) NOT NULL,
			dependent_id mediumint(9) NULL,
			first_name varchar(75) NOT NULL,
			last_name varchar(75) NOT NULL,
			email varchar(255) NOT NULL,
			registration_key varchar(255),
			registration_key_expiry_date datetime,
			username varchar(50) NULL,
			user_id mediumint(9)  NULL,
			FOREIGN KEY (membership_id) REFERENCES {$table_member_list}(id) ON DELETE CASCADE,
			PRIMARY KEY  (id)
			) $charset_collate;";


		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');


		dbDelta($sql);
	}

	private static function create_contact_list_table()
	{
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		if (!function_exists('dbDelta')) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		$table_contact_list = $wpdb->prefix . 'scsmm_contact_list';

		$sql = "CREATE TABLE $table_contact_list (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			first_name varchar(75) NOT NULL,
			last_name varchar(75) NOT NULL,
			phone varchar(75) NOT NULL,
			email varchar(255) NOT NULL,
			contact_date date(25) NOT NULL,
			comments text NULL,
			PRIMARY KEY  (id)
			) $charset_collate;";


		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');


		dbDelta($sql);
	}

	private static function create_dependant_list_table()
	{
		// create tables
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		if (!function_exists('dbDelta')) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		$table_member_list = $wpdb->prefix . 'scsmm_member_list';
		$table_relationship_types = $wpdb->prefix . 'scsmm_relationship_types';

		// dependant_list table
		$table_dependant_list = $wpdb->prefix . 'scsmm_dependent_list';
		$sql = "CREATE TABLE $table_dependant_list (
						id mediumint(9) NOT NULL AUTO_INCREMENT,
						membership_id mediumint(9) NOT NULL,
						first_name varchar(255) NOT NULL,
						last_name varchar(255) NOT NULL,
						phone varchar(20),
						mobile varchar(20),
						email varchar(255),
						relationship_id mediumint(9) NOT NULL,
						FOREIGN KEY (relationship_id) REFERENCES {$table_relationship_types}(id) ON DELETE CASCADE,
						FOREIGN KEY (membership_id) REFERENCES {$table_member_list}(id) ON DELETE CASCADE,
						PRIMARY KEY  (id)
					  ) $charset_collate;";


		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');


		dbDelta($sql);
	}


	private static function create_mail_template_table()
	{
		// create tables
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		if (!function_exists('dbDelta')) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}



		// email template table
		$table_list = $wpdb->prefix . 'scsmm_email_templates';
		$sql = "CREATE TABLE $table_list (
						id mediumint(9) NOT NULL AUTO_INCREMENT,
						name varchar(25) NOT NULL,
						recipient_desc varchar(255) NOT NULL,
						sender_desc varchar(255) NOT NULL,
						sender_email varchar(255) NOT NULL,
						subject varchar(255) NOT NULL,
						email_text text,
						PRIMARY KEY  (id)
					  ) $charset_collate;";


		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');


		dbDelta($sql);
	}

	private static function add_default_relationship_types()
	{
		global $wpdb;

		$table_name = $wpdb->prefix . 'scsmm_relationship_types';
		$wpdb->insert(
			$table_name,
			array(
				'name' => 'Child'
			)
		);

		$wpdb->insert(
			$table_name,
			array(
				'name' => 'Spouse'
			)
		);

		$wpdb->insert(
			$table_name,
			array(
				'name' => 'Grandchild'
			)
		);
	}

	private static function add_default_status_types($registration_email)
	{
		global $wpdb;



		$table_name = $wpdb->prefix . 'scsmm_status_types';
		$wpdb->insert(
			$table_name,
			array(
				'name' 				=> 'Applied',
				'work_flow_action' 	=> 'Activate:status=2,reg-email=' . $registration_email . ';Disable:status=3',
				'work_flow_order'	=> '1',
				'status_key'		=> 'new'
			)
		);

		$wpdb->insert(
			$table_name,
			array(
				'name' 				=> 'Active',
				'work_flow_action' 	=> 'Disable:status=3',
				'work_flow_order'	=> '2',
				'status_key'		=> 'active'
			)
		);

		$wpdb->insert(
			$table_name,
			array(
				'name' 				=> 'In Active',
				'work_flow_action' 	=> '',
				'work_flow_order'	=> '3',
				'status_key'		=> 'inactive'
			)
		);
	}

	private static function add_default_membership_types()
	{
		global $wpdb;

		$table_name = $wpdb->prefix . 'scsmm_membership_types';
		$wpdb->insert(
			$table_name,
			array(
				'name' 				=> 'Default',
				'description'	 	=> 'Default type should be deleted on configuration',
				'cost'				=> '',
				'short_name'		=> 'D',
				'next_number'		=> 1000, 
				'increment'			=> 1
			)
		);
	}
	private static function add_default_pages()
	{



		$pages[] = array(
			'title' => 'Registration Check - SCS',
			'content' => '[scsmm-registration-check]',
			'option'	=> 'registration-check-page'
		);

		

		$pages[] = array(
			'title' => 'Registration Check Redirect - SCS',
			'content' => '',
			'option'	=> 'registration-check-redirect-page'
		);


		$pages[] = array(
			'title' => 'Registration Expired Redirect - SCS',
			'content' => 'Sorry your registration has expired',
			'option'	=> 'registration-expired-redirect-page'
		);

		$pages[] = array(
			'title' => 'Registration Not Found Redirect - SCS',
			'content' => 'Sorry, your registration is incorrect.',
			'option'	=> 'registration-not-found-redirect-page'
		);

		$pages[] = array(
			'title' => 'Thanks for Applying - SCS',
			'content'	=> 'Thanks for applying',
			'option'	=> 'application-redirect-page'
		);

		$pages[] = array(
			'title' => 'Contact Us - SCS',
			'content' => '[scsmm-contact-form]',
			'option'	=> 'contact-page'
		);

		$pages[] = array(
			'title' => 'Thanks for Contacting Us - SCS',
			'content'	=> 'Thanks for contacting us.  We will be back with you shortly',
			'option'	=> 'contact-success-redirect-page'
		);

		$pages[] = array(
			'title' => 'Thanks for Registering With Us - SCS',
			'content'	=> 'Thanks for registering with us.  Please login for more content.',
			'option'	=> 'registration-success-redirect-page'
		);

		return $pages;
	}

	private static function add_page($page = array())
	{
		//Add Events page on activation:

		$new_page_title = $page['title'];
		$new_page_content = $page['content'];

		


		$new_page = array(
			'post_type' => 'page',
			'post_title' => $new_page_title,
			'post_content' => $new_page_content,
			'post_status' => 'publish',
			'post_author' => 1,
		);

		$page_check = get_page_by_title($new_page_title);
		if (!isset($page_check->ID)) {
			$new_page_id = wp_insert_post($new_page);
			return $new_page_id;
		} 
		return $page_check->ID;
	}

	/**
	 * Add the default mail items at activation
	 *
	 * @return void
	 */
	private static function add_default_mail_items()
	{
		//need two default emails
		//default email upon successful registation
		global $wpdb;

		$table_name = $wpdb->prefix . 'scsmm_email_templates';
		$wpdb->insert(
			$table_name,
			array(
				'name' 					=> 'Application Notification',
				'recipient_desc' 		=> 'New Applicant',
				'sender_desc'			=> 'Application Committee',
				'sender_email' 			=> '',
				'subject'				=> 'Thanks for applying',
				'email_text'			=> '<p>Dear {first_name} {last_name}:</p>'
					. '<p>Thanks for applying.  We will be back to you shortly.</p>'
					. '<p>Registration Committee</p>'
			)
		);
		$emails['member-application-notification-email'] = $wpdb->insert_id;

		$wpdb->insert(
			$table_name,
			array(
				'name' 					=> 'Application Confirmation',
				'recipient_desc' 		=> 'Application Committee',
				'sender_desc'			=> 'Application Committee',
				'sender_email' 			=> '',
				'subject'				=> 'New Application',
				'email_text'			=> '<p>Dear Application Committee:</p>'
					. '<p>You have received a new application from  {first_name} {last_name}.  We will be back to you shortly.</p>'
					. '<p>Registration Committee</p>'
			)
		);
		$emails['member-application-confirmation-email'] = $wpdb->insert_id;

		$wpdb->insert(
			$table_name,
			array(
				'name' 					=> 'Application Approved',
				'recipient_desc' 		=> 'New Applicant',
				'sender_desc'			=> 'Application Committee',
				'sender_email' 			=> '',
				'subject'				=> 'Application Accepted',
				'email_text'			=> '<p>Dear {first_name} {last_name}:</p>'
					. '<p><strong>Congratulations your application has been approved.</strong></p>'
					. '<p>To register on our web site please use the link below.</p>'
					. '{registration_url}'
					. '<p>Registration Committee</p>'
			)
		);
		$emails['member-application-approved-email'] = $wpdb->insert_id;
		return $emails;
	}
}
