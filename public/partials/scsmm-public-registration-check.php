<?php

/**
 * Provide a public-facing view for the registration request
 *
 * This short code feature is used to determine if a request has the proper stored registration request 
 * number to be redirected to the registration page.  
 *
 * @link       https://github.com/dblosser0556
 * @since      1.0.0
 *
 * @package    Scsmm
 * @subpackage Scsmm/public/partials
 */
?>

<?php
function getTable($table)
{
    global $wpdb;
    return "{$wpdb->prefix}scsmm_{$table}";
}


function get_member($guid)
{
    global $wpdb;
    $table_member_list = getTable('member_list');
    $member =  $wpdb->get_row("SELECT memberships.* 
        FROM $table_member_list as memberships WHERE memberships.registration_key = '$guid'");
    return $member;
}



$options = get_option(PLUGIN_TEXT_DOMAIN);

add_action('wpum_before_registration_end', 'wpum_before_registration_end', 10, 2);

if (!isset($_REQUEST['key'])) {
    if (isset($options['registration-not-found-page'])) {
        wp_safe_redirect($options['registration-not-found-page']);
    }
}

// test to see if passed key is valid
$member = get_member($_REQUEST['key']);

// is not found
if (!isset($member)) {
    if (isset($options['registration-not-found-redirect-page'])) {
        wp_safe_redirect($options['registration-not-found-redirect-page']);
        return;
    } else {
        wp_safe_redirect(get_home_url());
        return;
    }
}

$now = date_create();

if ($now > strtotime($member->registration_key_expiry_date)) {
    if (isset($options['registration-expired-redirect-page'])) {
        wp_safe_redirect($options['registration-expired-redirect-page']);
        return;
    } else {
        wp_safe_redirect(get_home_url());
        return;
    }
}

// if successful
if (isset($options['registration-check-redirect-page'])) {
    // this should be the registation page
    wp_safe_redirect($options['registration-check-redirect-page']);
    return;
} else {
    wp_safe_redirect(PLUGIN_URL);
    return;
}


function wpum_before_registration_end($new_user_id, $values)
{
    //hmmm
    $see_me = 1;
}



?>