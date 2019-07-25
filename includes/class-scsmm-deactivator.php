<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://github.com/dblosser0556
 * @since      1.0.0
 *
 * @package    Scsmm
 * @subpackage Scsmm/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Scsmm
 * @subpackage Scsmm/includes
 * @author     David Blosser <blosserdl@gmail.com>
 */
class Scsmm_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		global $wpdb;
        // dependents table
        $table_name = $wpdb->prefix . 'scsmm_dependentlist';
        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query($sql);

        // memberlist table
        $table_name = $wpdb->prefix . 'scsmm_memberlist';
        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query($sql);

        // membership types table
        $table_name = $wpdb->prefix . 'scsmm_membership_types';
        $sql = "DROP TABLE IF EXISTS $table_name";
		$wpdb->query($sql);
		
		// relationship table
        $table_name = $wpdb->prefix . 'scsmm_relationship_types';
        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query($sql);
	}

}
