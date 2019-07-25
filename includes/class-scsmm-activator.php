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

        // relationship types table
        $table_relationship_types = $wpdb->prefix . 'scsmm_relationship_types';
        $sql = "CREATE TABLE $table_relationship_types (
						  id mediumint(9) NOT NULL AUTO_INCREMENT,
						  name varchar(255) NULL,
						  UNIQUE KEY id (id)
					  ) $charset_collate;";
        dbDelta($sql);

        // membership types table
        $table_membership_types = $wpdb->prefix . 'scsmm_membership_types';
        $sql = "CREATE TABLE $table_membership_types (
						  id mediumint(9) NOT NULL AUTO_INCREMENT,
						  name varchar(255) NULL,
						  UNIQUE KEY id (id)
					  ) $charset_collate;";
        dbDelta($sql);

        // memberlist table
        $table_memberlist = $wpdb->prefix . 'scsmm_memberlist';
        $sql = "CREATE TABLE $table_memberlist (
			        id mediumint(9) NOT NULL AUTO_INCREMENT,
					firstname varchar(255) NOT NULL,
					lastname varchar(255) NOT NULL,
					address1 varchar(255) NOT NULL,
					address2 varchar(255),
					city varchar(255) NOT NULL,
					state varchar(255) NOT NULL,
					zipcode varchar(11) NOT NULL,
		     		homephone varchar(10),
					mobile varchar(10),
					employer varchar(255),
					email varchar(255),
					membershiptypeid mediumint(9),
					status tinyint(1) DEFAULT 0 NOT NULL,
					joindate datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
					FOREIGN KEY (membershiptypeid) REFERENCES {$table_membership_types}(id) ON DELETE CASCADE,
					UNIQUE KEY id (id)
				) $charset_collate;";
        dbDelta($sql);

        // memberlist table
        $table_dependant_list = $wpdb->prefix . 'scsmm_dependentlist';
        $sql = "CREATE TABLE $table_dependant_list (
					  id mediumint(9) NOT NULL AUTO_INCREMENT,
					  membershipid mediumint(9) NOT NULL,
					  firstname varchar(255) NOT NULL,
					  lastname varchar(255) NOT NULL,
					  phone varchar(10),
					  mobile varchar(10),
					  email varchar(255),
					  relationshipid mediumint(9) NOT NULL,
					  FOREIGN KEY (relationshipid) REFERENCES {$table_relationship_types}(id) ON DELETE CASCADE,
				  	  FOREIGN KEY (membershipid) REFERENCES {$table_memberlist}(id) ON DELETE CASCADE,


					  UNIQUE KEY id (id)
				  ) $charset_collate;";
        dbDelta($sql);

    }

}
