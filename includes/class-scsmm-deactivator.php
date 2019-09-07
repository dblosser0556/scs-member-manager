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
class Scsmm_Deactivator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function deactivate()
    {
        global $wpdb;


        // delete the options
        delete_option(PLUGIN_TEXT_DOMAIN);

        // remove the pages that we added



        $titles = array(
            'Registration Check - SCS', 'Registration Check Redirect - SCS',
            'Registration Expired Redirect - SCS', 'Registration Not Found Redirect - SCS',
            'Thanks for Applying - SCS','Contact Us - SCS', 'Thanks for Contacting Us - SCS',
            'Thanks for Registering With Us - SCS'
        );


        foreach ($titles as $title) {
            $page = get_page_by_title($title);
            if (isset($page)) {
                wp_delete_post($page->ID, true);
            }
        }


        // registration table
        $table_name = $wpdb->prefix . 'scsmm_registration_list';
        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query($sql);

        // dependents table
        $table_name = $wpdb->prefix . 'scsmm_dependent_list';
        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query($sql);

        // member_list table
        $table_name = $wpdb->prefix . 'scsmm_member_list';
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

        // status table
        $table_name = $wpdb->prefix . 'scsmm_status_types';
        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query($sql);

        // status table
        $table_name = $wpdb->prefix . 'scsmm_email_templates';
        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query($sql);
    }
}
