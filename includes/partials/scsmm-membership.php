<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://github.com/dblosser0556
 * @since      1.0.0
 *
 * @package    Scsmm
 * @subpackage Scsmm/public/partials
 */
?>

<?php


function get_member($memberId)
{
    global $wpdb;
    $table_member_list = apply_filters('get_plugin_table', 'member_list');
    $table_types = apply_filters('get_plugin_table', 'membership_types');
    return $wpdb->get_row("SELECT memberships.*, membership_types.name as type
		FROM $table_member_list as memberships,
            $table_types as membership_types
	    	WHERE membership_types.id=memberships.membership_type_id  
				AND memberships.id = $memberId");
}

function get_dependents($memberId)
{
    global $wpdb;
    $table_list = apply_filters('get_plugin_table', 'dependent_list');
    $table_types = apply_filters('get_plugin_table', 'relationship_types');
    return $wpdb->get_results("SELECT dependents.*, relationship_types.name as type FROM 
			$table_list as dependents,
	   		$table_types as relationship_types
	    	WHERE relationship_types.id=dependents.relationship_id
				AND dependents.membership_id =  $memberId ");
}




function get_relationship_types()
{
    global $wpdb;
    $table_name = apply_filters('get_plugin_table', 'relationship_types');
    return $wpdb->get_results("SELECT * FROM $table_name ORDER BY name");
}

function get_status_types()
{
    global $wpdb;
    $table_name = apply_filters('get_plugin_table', 'status_types');
    return $wpdb->get_results("SELECT * FROM $table_name ORDER BY id");
}


function get_memberships_types()
{
    global $wpdb;
    $table_name = apply_filters('get_plugin_table', 'membership_types');
    return $wpdb->get_results("SELECT * FROM $table_name ORDER BY name");
}
function get_status_new()
{
    global $wpdb;
    $table_name = apply_filters('get_plugin_table', 'status_types');
    $_status = $wpdb->get_row("SELECT * FROM $table_name WHERE status_key ='new'");
    return $_status->id;
}


$relationship_types = get_relationship_types();
$membership_types = get_memberships_types();
$status_types = get_status_types();
$status_new = get_status_new();


if (isset($_GET['member_id'])) {
    $member_id = $_GET['member_id'];
    $member = get_member($member_id);
    $dependents = get_dependents($member_id);
}

// handle the viewing and update options
// from the admin screen
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'view_member') {
        $readonly = true;
        $can_update = false;
        $back = true;
    } else {
        $readonly = false;
        $can_update = true;
        $back = true;
    }
} else {
    $readonly = false;
    $can_update = true;
    $back = false;
}

// if new create an empty member;
if (!isset($member)) {
    $member = new stdClass();
    $member->id = 0;
    $member->username = '';
    $member->first_name = '';
    $member->last_name = '';
    $member->address1 = '';
    $member->address2 = '';
    $member->city = '';
    $member->state = '';
    $member->zipcode = '';
    $member->phone = '';
    $member->mobile = '';
    $member->employer = '';
    $member->email = '';
    $member->notes = '';
    $member->membership_type_id = -1;
    $member->status_id = 0;
    $member->join_date = date('Y-m-d');


    $dependents = array();
}

//Set Your Nonce
$ajax_nonce = wp_create_nonce("scs-member-check-string");

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div id="alert" class="alert alert-info alert-dismissible" role="alert" style="display:none">
    <button id="alertClose" type="button" class="close">&times;</button>
    <span id="alertText">Reached the end of the calendar.</span>
</div>

<div id="success" class="alert alert-success alert-dismissible" role="alert" style="display:none">
    <button id="successClose" type="button" class="close">&times;</button>
    <span id="successText">Reached the end of the calendar.</span>
</div>


<div class="container">
    <h2><?= esc_html__('Applicant', PLUGIN_TEXT_DOMAIN) ?> </h2>
    <form id="application_form" class="needs-validation" novalidate>
        <input type="text" hidden name="action" value="member_application" />

        <input type="text" hidden name="id" value="<?= $member->id ?>" />
        <input type="text" hidden name="security" value="<?= $ajax_nonce; ?>" />
        <input type="text" hidden name="dependent_count" value="<?= count($dependents); ?>" id="dependent_count" />

        <div class="form-row">
            <div class="col-md-9 mb-4">
                <label for="membership_type_id"><?= esc_html__('Membership Type', PLUGIN_TEXT_DOMAIN) ?></label>
                <select class="form-control" id="membership_type_id" name="membership_type_id" style="height: calc(2.25rem + 2px); padding: .375rem .75rem;
                     font-size: 1rem; font-weight: 400; line-height: 1.5;" <?= $readonly ? 'readonly' : '' ?>>
                    <?php foreach ($membership_types as $type) { ?>
                        <option value="<?= $type->id ?>" <?= $member->membership_type_id == $type->id ? 'selected="selected"' : ''; ?>><b><?= $type->name ?></b> - <?= $type->description ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-md-3 mb-2">
                <label for="status_id">Membership Status</label>
                <select <?= $member->id == 0 ? 'readonly' : '' ?> <?= $readonly ? 'readonly' : '' ?> class="form-control" id="status_id" name="status_id" style="height: calc(2.25rem + 2px); padding: .375rem .75rem;
                     font-size: 1rem; font-weight: 400; line-height: 1.5;">
                    <?php foreach ($status_types as $type) { ?>
                        <option value="<?= $type->id ?>" <?= $member->id == 0 && $type->id == $status_new ? 'selected="selected"' : $member->status_id == $type->id ? 'selected="selected"' : ''; ?>><?= $type->name ?> </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-6 mb-3">
                <label for="firstName">First name*</label>
                <input type="text" class="form-control" id="firstName" name="first_name" placeholder="First name" value="<?= $member->first_name ?>" <?= $readonly ? 'readonly' : '' ?> />
            </div>
            <div class="col-md-6 mb-3">
                <label for="last_name">Last name*</label>
                <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last name" value="<?= $member->last_name ?>" <?= $readonly ? 'readonly' : '' ?> />
            </div>

        </div>
        <div class="form-row">
            <div class="col-md-12 mb-6">
                <label for="address1">Address Line 1*</label>
                <input type="text" class="form-control" id="address1" name="address1" placeholder="Address 1" value="<?= $member->address1 ?>" <?= $readonly ? 'readonly' : '' ?> />
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-12 mb-6">
                <label for="address2">Address Line 2</label>
                <input type="text" class="form-control" id="address2" name="address2" placeholder="Address 2" value="<?= $member->address2 ?>" <?= $readonly ? 'readonly' : '' ?> />
            </div>

        </div>
        <div class="form-row">
            <div class="col-md-6 mb-3">
                <label for="city">City*</label>
                <input type="text" class="form-control" id="city" name="city" placeholder="City" value="<?= $member->city ?>" <?= $readonly ? 'readonly' : '' ?> />
            </div>
            <div class="col-md-3 mb-3">
                <label for="state">State*</label>
                <select class="form-control" id="state" name="state" placeholder="State" style="height: calc(2.25rem + 2px); padding: .375rem .75rem;
                     font-size: 1rem; font-weight: 400; line-height: 1.5;" <?= $readonly ? 'readonly' : '' ?>>
                    <option value="">--Please Select--</option>
                    <option <?php if ($member->state == "AL") echo 'selected'; ?> value="AL">Alabama</option>
                    <option <?php if ($member->state == "AK") echo 'selected'; ?> value="AK">Alaska</option>
                    <option <?php if ($member->state == "AZ") echo 'selected'; ?> value="AZ">Arizona</option>
                    <option <?php if ($member->state == "AR") echo 'selected'; ?> value="AR">Arkansas</option>
                    <option <?php if ($member->state == "CA") echo 'selected'; ?> value="CA">California</option>
                    <option <?php if ($member->state == "CO") echo 'selected'; ?> value="CO">Colorado</option>
                    <option <?php if ($member->state == "CT") echo 'selected'; ?> value="CT">Connecticut</option>
                    <option <?php if ($member->state == "DE") echo 'selected'; ?> value="DE">Delaware</option>
                    <option <?php if ($member->state == "DC") echo 'selected'; ?> value="DC">District Of Columbia
                    </option>
                    <option <?php if ($member->state == "FL") echo 'selected'; ?> value="FL">Florida</option>
                    <option <?php if ($member->state == "GA") echo 'selected'; ?> value="GA">Georgia</option>
                    <option <?php if ($member->state == "HI") echo 'selected'; ?> value="HI">Hawaii</option>
                    <option <?php if ($member->state == "ID") echo 'selected'; ?> value="ID">Idaho</option>
                    <option <?php if ($member->state == "IL") echo 'selected'; ?> value="IL">Illinois</option>
                    <option <?php if ($member->state == "IN") echo 'selected'; ?> value="IN">Indiana</option>
                    <option <?php if ($member->state == "IA") echo 'selected'; ?> value="IA">Iowa</option>
                    <option <?php if ($member->state == "KS") echo 'selected'; ?> value="KS">Kansas</option>
                    <option <?php if ($member->state == "KY") echo 'selected'; ?> value="KY">Kentucky</option>
                    <option <?php if ($member->state == "LA") echo 'selected'; ?> value="LA">Louisiana</option>
                    <option <?php if ($member->state == "ME") echo 'selected'; ?> value="ME">Maine</option>
                    <option <?php if ($member->state == "MD") echo 'selected'; ?> value="MD">Maryland</option>
                    <option <?php if ($member->state == "MD") echo 'selected'; ?> value="MA">Massachusetts</option>
                    <option <?php if ($member->state == "MI") echo 'selected'; ?> value="MI">Michigan</option>
                    <option <?php if ($member->state == "MN") echo 'selected'; ?> value="MN">Minnesota</option>
                    <option <?php if ($member->state == "MS") echo 'selected'; ?> value="MS">Mississippi</option>
                    <option <?php if ($member->state == "MO") echo 'selected'; ?> value="MO">Missouri</option>
                    <option <?php if ($member->state == "MT") echo 'selected'; ?> value="MT">Montana</option>
                    <option <?php if ($member->state == "NE") echo 'selected'; ?> value="NE">Nebraska</option>
                    <option <?php if ($member->state == "NV") echo 'selected'; ?> value="NV">Nevada</option>
                    <option <?php if ($member->state == "NH") echo 'selected'; ?> value="NH">New Hampshire</option>
                    <option <?php if ($member->state == "NJ") echo 'selected'; ?> value="NJ">New Jersey</option>
                    <option <?php if ($member->state == "NM") echo 'selected'; ?> value="NM">New Mexico</option>
                    <option <?php if ($member->state == "NY") echo 'selected'; ?> value="NY">New York</option>
                    <option <?php if ($member->state == "NC") echo 'selected'; ?> value="NC">North Carolina</option>
                    <option <?php if ($member->state == "ND") echo 'selected'; ?> value="ND">North Dakota</option>
                    <option <?php if ($member->state == "OH") echo 'selected'; ?> value="OH">Ohio</option>
                    <option <?php if ($member->state == "OK") echo 'selected'; ?> value="OK">Oklahoma</option>
                    <option <?php if ($member->state == "OR") echo 'selected'; ?> value="OR">Oregon</option>
                    <option <?php if ($member->state == "PA") echo 'selected'; ?> value="PA">Pennsylvania</option>
                    <option <?php if ($member->state == "RI") echo 'selected'; ?> value="RI">Rhode Island</option>
                    <option <?php if ($member->state == "SC") echo 'selected'; ?> value="SC">South Carolina</option>
                    <option <?php if ($member->state == "SD") echo 'selected'; ?> value="SD">South Dakota</option>
                    <option <?php if ($member->state == "TN") echo 'selected'; ?> value="TN">Tennessee</option>
                    <option <?php if ($member->state == "TX") echo 'selected'; ?> value="TX">Texas</option>
                    <option <?php if ($member->state == "UT") echo 'selected'; ?> value="UT">Utah</option>
                    <option <?php if ($member->state == "VT") echo 'selected'; ?> value="VT">Vermont</option>
                    <option <?php if ($member->state == "VA") echo 'selected'; ?> value="VA">Virginia</option>
                    <option <?php if ($member->state == "WA") echo 'selected'; ?> value="WA">Washington</option>
                    <option <?php if ($member->state == "WV") echo 'selected'; ?> value="WV">West Virginia</option>
                    <option <?php if ($member->state == "WI") echo 'selected'; ?> value="WI">Wisconsin</option>
                    <option <?php if ($member->state == "WY") echo 'selected'; ?> value="WY">Wyoming</option>
                </select>

            </div>
            <div class="col-md-3 mb-3">
                <label for="zipcode">Zip*</label>
                <input type="text" class="form-control zipcode" id="zipcode" name="zipcode" placeholder="Zip" data-inputmask="'mask': '99999[-9999]'" pattern="(\d{5}([\-]\d{4})?)" value="<?= $member->zipcode ?>" <?= $readonly ? 'readonly' : '' ?>>
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-3 mb-2">
                <label for="phone">Phone</label>
                <input type="tel" class="form-control phone" id="phone" name="phone" data-inputmask="'mask': '(999) 999-9999'" placeholder="Phone Number" value="<?= $member->phone ?>" <?= $readonly ? 'readonly' : '' ?> />
            </div>
            <div class="col-md-3 mb-2">
                <label for="mobile">Mobile</label>
                <input type="tel" class="form-control phone" id="mobile" name="mobile" data-inputmask="'mask': '(999) 999-9999'" placeholder="Mobile Number" value="<?= $member->mobile ?>" <?= $readonly ? 'readonly' : '' ?> />
            </div>
            <div class="col-md-3 mb-2">
                <label for="email">Email*</label>
                <input type="text" class="form-control" id="email" name="email" data-inputmask="'alias': 'email'" placeholder="Email" value="<?= $member->email ?>" <?= $readonly ? 'readonly' : '' ?> />
            </div>
            <div class="col-md-3 mb-2">
                <label for="join_date"><?= $member->id == 0 ? 'Application Date' : 'Join Date' ?> </label>
                <input readonly type="text" class="form-control" id="join_date" name="join_date" value="<?= $member->join_date ?>" />

            </div>


        </div>
        <div class="form-row">

            <div class="col-md-3 mb-2">
                <label for="employer">Employer</label>
                <input type="text" class="form-control" id="employer" name="employer" placeholder="Employer" value="<?= $member->employer ?>" <?= $readonly ? 'readonly' : '' ?> />
            </div>
            <div class="col-md-9 mb-4">
                <label for="notes">Application Notes</label>
                <textarea class="form-control" id="notes" name="notes" aria-label="With textarea" <?= $readonly ? 'readonly' : '' ?>><?= $member->notes ?></textarea>
                <small id="passwordHelpBlock" class="form-text text-muted">
                    Please enter sponsors, clubs members you know, and other information relevant to joining our club.
                </small>
            </div>


        </div>
        <div class="form-row">
            <table class="table" id="dependantTable">
                <tbody>
                    <?php

                    $path = realpath(plugin_dir_path(__FILE__) . 'scsmm-dependent.php');

                    for ($i = 1; $i <= count($dependents); $i++) {
                        include $path;
                    }

                    ?>
                </tbody>
            </table>
        </div>
        <button class="btn btn-primary" type="submit" <?= $can_update ? '' : 'hidden' ?>><?= esc_html__('Save', PLUGIN_TEXT_DOMAIN) ?></button>
        <button class="btn btn-secondary" type="button" id="addDependents" <?= $can_update ? '' : 'hidden' ?>><?= esc_html__('Add Dependents', PLUGIN_TEXT_DOMAIN) ?></button>
        <button class="btn btn-secondary" type="button" <?= $back ? '' : 'hidden' ?> onclick="window.location.href = '<?= isset($_REQUEST['referer']) ? esc_url(add_query_arg(array('page' => wp_unslash($_REQUEST['referer'])), admin_url('admin.php'))) : ''; ?>'"><?= esc_html__('Back', 'scsmm') ?>
        </button>

    </form>

</div>
<table style='display:none'>
    <tbody>
        <?php

        $path = realpath(plugin_dir_path(__FILE__) . 'scsmm-dependent.php');

        $i = 0;
        include $path;


        ?>
    </tbody>
</table>