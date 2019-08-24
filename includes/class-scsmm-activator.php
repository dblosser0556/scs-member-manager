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

		// add default data 
		self::add_default_relationship_types();
		self::add_default_status_types();

		// store the current database version for future upgrades
		add_option(PLUGIN_TEXT_DOMAIN . '_db_version', PLUGIN_DB_VERSION);
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
				  status_key 		varchar(15) NOT NULL,
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
					  name varchar(20) NULL,
					  description varchar(1024) NULL,
					  cost varchar(1024) NULL,
					  PRIMARY KEY  (id)
				  ) $charset_collate;";
		dbDelta($sql);



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
				id mediumint NOT NULL AUTO_INCREMENT,
				membership_number varchar(50),
				registration_key varchar(255),
				registration_key_expiry_date datetime,
				username varchar(50) NOT NULL,
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
				membership_type_id mediumint,
				status_id mediumint,
				join_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				FOREIGN KEY (membership_type_id) REFERENCES {$table_membership_types}(id) ON DELETE CASCADE,
				FOREIGN KEY (status_id) REFERENCES {$table_status_types}(id) ON DELETE CASCADE,
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
		$table_dependant_list = $wpdb->prefix . 'scsmm_email_templates';
		$sql = "CREATE TABLE $table_dependant_list (
						id mediumint(9) NOT NULL AUTO_INCREMENT,
						name varchar(25) NOT NULL,
						recipient_desc varchar(255) NOT NULL,
						sender_desc varchar(255) NOT NULL,
						sender_email varchar(255) NOT NULL,
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

	private static function add_default_status_types()
	{
		global $wpdb;

		$table_name = $wpdb->prefix . 'scsmm_status_types';
		$wpdb->insert(
			$table_name,
			array(
				'name' 				=> 'Applied',
				'work_flow_action' 	=> 'Activate:final_status=2;Disable:final_status=3',
				'work_flow_order'	=> '1',
				'status_key'		=> 'new'
			)
		);

		$wpdb->insert(
			$table_name,
			array(
				'name' 				=> 'Active',
				'work_flow_action' 	=> 'Disable:final_status=3',
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
}
