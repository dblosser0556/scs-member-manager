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

$ajax_nonce = wp_create_nonce("scs-contact-form-check-string");

?>

<div id="alert" class="alert alert-info alert-dismissible" role="alert" style="display:none">
    <button id="alertClose" type="button" class="close">&times;</button>
    <span id="alertText">Something went wrong.</span>
</div>

<div id="success" class="alert alert-success alert-dismissible" role="alert" style="display:none">
    <button id="successClose" type="button" class="close">&times;</button>
    <span id="successText">Contact Entry Successful</span>
</div>

<div class="container">
    <div id="loader" class="smt-spinner-circle" style="display:none">
        <div class="smt-spinner"></div>
    </div>
    <h2><?= esc_html__('Contact Us', PLUGIN_TEXT_DOMAIN) ?> </h2>
    <form id="contact_form" method="post" action="">
        <input type="text" hidden name="action" value="contact_form" />
        <input type="text" hidden name="security" value="<?= $ajax_nonce ?>" />
        <div class="form-group">
            <label for="first _name">First Name*</label>
            <input type="text" class="form-control" id="first_name" name="first_name" >

        </div>
        <div class="form-group">
            <label for="last_name">Last Name*</label>
            <input type="text" class="form-control" id="last_name" name="last_name">
        </div>
        <div class="form-group">
            <label for="email">Email*</label>
            <input type="email" class="form-control" id="email" name="email">

        </div>
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" class="form-control phone" data-inputmask="'mask': '(999) 999-9999'" id="phone" name="phone">
        </div>
        
        <div class="form-group">
            <label for="member_number">Questions or Comments</label>
            <textarea rows="4" class="form-control" id="comments" name="comments"></textarea> 
        </div>
        
        <button type="submit" class='btn btn-primary' name="submit" id="contact_submit">Submit</button>
    </form>
</div>