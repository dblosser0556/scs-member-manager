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

$reg_errors = new WP_Error;

function getTable($table)
{
    global $wpdb;
    return "{$wpdb->prefix}scsmm_{$table}";
}


function get_member($guid)
{
    global $wpdb;
    
    $table_registration_list = getTable('registration_list');
    $table_dependent_list = getTable('dependent_list');
    $table_member_list = getTable('member_list');

    $registration =  $wpdb->get_row("SELECT * 
        FROM $table_registration_list WHERE registration_key = '$guid'", ARRAY_A);

    if ($registration['dependent_id'] > 0 ) {
       
        $member = $wpdb->get_row("SELECT 
            r.id as id,
            r.membership_id as membership_id,
            r.dependent_id as dependent_id,
            r.registration_key as registration_key, 
            r.registration_key_expiry_date as registration_key_expiry_date, 
            r.username as username,
            r.user_id as user_id,
            d.first_name as first_name, 
            d.last_name as last_name, 
            d.email as email,  
            m.membership_number as membership_number 
            FROM $table_registration_list r, $table_dependent_list d, $table_member_list m
    WHERE r.dependent_id = d.id AND r.membership_id = m.id AND r.id = {$registration['id']}", ARRAY_A);
    } else {
        $member = $wpdb->get_row("SELECT 
            r.id as id,
            r.membership_id as membership_id, 
            r.dependent_id as dependent_id,
            r.registration_key as registration_key, 
            r.registration_key_expiry_date as registration_key_expiry_date, 
            r.username as username,
            r.user_id as user_id,
            m.first_name as first_name, 
            m.last_name as last_name, 
            m.email as email,  
            m.membership_number as membership_number 
            FROM $table_registration_list r,  $table_member_list m
    WHERE r.membership_id = m.id AND r.id = {$registration['id']}", ARRAY_A);
    }

    return $member;
}


$options = get_option(PLUGIN_TEXT_DOMAIN);



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
$expire = new DateTime($member['registration_key_expiry_date']);

if ($now > $expire) {
    if (isset($options['registration-expired-redirect-page'])) {
        wp_safe_redirect($options['registration-expired-redirect-page']);
        return;
    } else {
        wp_safe_redirect(get_home_url());
        return;
    }
}

// check if already registered
if (!empty($member['username']) ) {
    if (isset($options['login-page'])) {
        wp_safe_redirect($options['login-page']);
        return;
    } else {
        wp_safe_redirect(wp_login_url());
        return;
    }
}

$ajax_nonce = wp_create_nonce("scs-registration-check-string");




?>

<div id="alert" class="alert alert-info alert-dismissible" role="alert" style="display:none">
    <button id="alertClose" type="button" class="close">&times;</button>
    <span id="alertText">Registration Unsuccessful.</span>
</div>

<div id="success" class="alert alert-success alert-dismissible" role="alert" style="display:none">
    <button id="successClose" type="button" class="close">&times;</button>
    <span id="successText">Registration Successful</span>
</div>

<div class="container">
    <div id="loader" class="smt-spinner-circle" style="display:none">
        <div class="smt-spinner"></div>
    </div>
    <h2><?= esc_html__('Register', PLUGIN_TEXT_DOMAIN) ?> </h2>
    <form id="registration_form" method="post" action="">
        <input type="text" hidden name="action" value="register_member" />
        <input type="text" hidden name="security" value="<?= $ajax_nonce ?>" />
        <input type="text" hidden name="registration_id" value="<?= $member['id'] ?>" />
        <div class="form-group">
            <label for="username">Username*</label>
            <input type="text" class="form-control" id="username" name="username">

        </div>
        <div class="form-group">
            <label for="password">Password*</label>
            <input type="password" class="form-control" id="password" name="password">

        </div>
        <div class="form-group">
            <label for="confirm_password">Password*</label>
            <input type="password" class="form-control" id="confirm_password">

        </div>
        <div class="form-group">
            <label for="email">Email*</label>
            <input type="email" class="form-control" id="email" name="email" value=' <?= $member['email'] ?> '>

        </div>
        <div class="form-group">
            <label for="first _name">First Name*</label>
            <input type="text" class="form-control" id="first_name" name="first_name" value=' <?= $member['first_name'] ?> '>

        </div>
        <div class="form-group">
            <label for="last_name">Last Name*</label>
            <input type="text" class="form-control" id="last_name" name="last_name" value='<?= $member['last_name'] ?> '>
        </div>
        <div class="form-group">
            <label for="member_number">Membership Number</label>
            <input type="text" class="form-control" id="membership_number" name="membership_number" readonly value='<?= $member['membership_number'] ?> '>
        </div>
        <div class="form-group">
            <div class="checkbox">
                <label>
                    <input type="checkbox" id="agree" name="agree" value="agree" />Please agree to our policy
                </label>
            </div>
        </div>
        <button type="submit" class='btn btn-primary' name="submit" id="reg_submit">Submit</button>
    </form>
</div>