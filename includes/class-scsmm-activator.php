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
        // create tables
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        if (!function_exists('dbDelta')) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        // status types table
        $table_status_types = $wpdb->prefix . 'scsmm_status_types';
        $sql = "CREATE TABLE $table_status_types (
						  id mediumint NOT NULL AUTO_INCREMENT,
						  name varchar(15) NOT NULL,
						  work_flow_action varchar(255),
						  work_flow_order smallint NOT NULL,
						  UNIQUE KEY id (id)
					  ) $charset_collate;";
        dbDelta($sql);

        // membership types table
        $table_membership_types = $wpdb->prefix . 'scsmm_membership_types';
        $sql = "CREATE TABLE $table_membership_types (
						  id mediumint NOT NULL AUTO_INCREMENT,
						  name varchar(20) NULL,
						  description varchar(1024) NULL,
						  cost varchar(1024) NULL,
						  UNIQUE KEY id (id)
					  ) $charset_collate;";
        dbDelta($sql);

        // relationship types table
        $table_relationship_types = $wpdb->prefix . 'scsmm_relationship_types';
        $sql = "CREATE TABLE $table_relationship_types (
						  id mediumint NOT NULL AUTO_INCREMENT,
						  name varchar(15) NOT NULL,
						  UNIQUE KEY id (id)
					  ) $charset_collate;";

		dbDelta($sql);
	
        // member_list table
        $table_member_list = $wpdb->prefix . 'scsmm_member_list';
        $sql = "CREATE TABLE $table_member_list (
			        id mediumint NOT NULL AUTO_INCREMENT,
					username varchar(50) NOT NULL,
					firstname varchar(75) NOT NULL,
					lastname varchar(75) NOT NULL,
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
					membershiptypeid mediumint,
					statusid mediumint,
					joindate datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
					FOREIGN KEY (membershiptypeid) REFERENCES {$table_membership_types}(id) ON DELETE CASCADE,
					FOREIGN KEY (statusid) REFERENCES {$table_status_types}(id) ON DELETE CASCADE,
					UNIQUE KEY id (id)
				) $charset_collate;";

        dbDelta($sql);

        // member_list table
        $table_dependant_list = $wpdb->prefix . 'scsmm_dependent_list';
        $sql = "CREATE TABLE $table_dependant_list (
					  id mediumint NOT NULL AUTO_INCREMENT,
					  membershipid mediumint(9) NOT NULL,
					  firstname varchar(255) NOT NULL,
					  lastname varchar(255) NOT NULL,
					  phone varchar(20),
					  mobile varchar(20),
					  email varchar(255),
					  relationshipid mediumint NOT NULL,
					  FOREIGN KEY (relationshipid) REFERENCES {$table_relationship_types}(id) ON DELETE CASCADE,
				  	  FOREIGN KEY (membershipid) REFERENCES {$table_member_list}(id) ON DELETE CASCADE,


					  UNIQUE KEY id (id)
				  ) $charset_collate;";
        dbDelta($sql);

    }

}
