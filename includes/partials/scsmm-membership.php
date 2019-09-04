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

function getTable($table)
{
    global $wpdb;
    return "{$wpdb->prefix}scsmm_{$table}";
}


function get_member($memberId)
{
    global $wpdb;
    $table_member_list = getTable('member_list');
    $table_types = getTable('membership_types');
    return $wpdb->get_row("SELECT memberships.*, membership_types.name as type
		FROM $table_member_list as memberships,
            $table_types as membership_types
	    	WHERE membership_types.id=memberships.membership_type_id  
				AND memberships.id = $memberId");
}

function get_dependents($memberId)
{
    global $wpdb;
    $table_list = getTable('dependent_list');
    $table_types = getTable('relationship_types');
    return $wpdb->get_results("SELECT dependents.*, relationship_types.name as type FROM 
			$table_list as dependents,
	   		$table_types as relationship_types
	    	WHERE relationship_types.id=dependents.relationship_id
				AND dependents.membership_id =  $memberId ");
}




function get_relationship_types()
{
    global $wpdb;
    $table_name = getTable('relationship_types');
    return $wpdb->get_results("SELECT * FROM $table_name ORDER BY name");
}

function get_status_types()
{
    global $wpdb;
    $table_name = getTable('status_types');
    return $wpdb->get_results("SELECT * FROM $table_name ORDER BY id");
}


function get_memberships_types()
{
    global $wpdb;
    $table_name = getTable('membership_types');
    return $wpdb->get_results("SELECT * FROM $table_name ORDER BY name");
}
function get_status_new()
{
    global $wpdb;
    $table_name = getTable('status_types');
    $_status = $wpdb->get_row("SELECT * FROM $table_name WHERE status_key ='new'");
    return $_status->id;
}

function get_success_redirect()
{
    $options = get_option('scsmm');
    if (isset($options['application-redirect-page'])) {
        return $options['application-redirect-page'];
    }
    //todo: handle not set option
    return null;
}



$relationship_types = get_relationship_types();
$membership_types = get_memberships_types();
$status_types = get_status_types();
$status_new = get_status_new();
$redirect = get_success_redirect();

if (isset($_GET['memberid'])) {
    $member_id = $_GET['memberid'];
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
    <form id="applyform" class="needs-validation" novalidate>
        <input type="text" hidden name="action" value="member_application" />
        <input type="text" hidden name="id" value="<?= $member->id ?>" />
        <div class="form-row" style="border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: gray;height:30px;">

        </div>
        <div class="form-row">
            <div class="col-md-9 mb-4">
                <label for="membership_type_id"><?= esc_html__('Membership Type', PLUGIN_TEXT_DOMAIN)?></label>
                <select class="form-control" id="membership_type_id" name="membership_type_id" required style="height: calc(2.25rem + 2px); padding: .375rem .75rem;
                     font-size: 1rem; font-weight: 400; line-height: 1.5;" <?= $readonly ? 'readonly' : '' ?>>
                    <?php foreach ($membership_types as $type) { ?>
                    <option value="<?= $type->id ?>" <?= $member->membership_type_id == $type->id ? 'selected="selected"' : ''; ?>><b><?= $type->name ?></b> - <?= $type->description ?></option>
                    <?php } ?>
                </select>
                <div class="invalid-feedback">
                    Please provide a requested membership type
                </div>
            </div>

            <div class="col-md-3 mb-2">
                <label for="status_id">Membership Status</label>
                <select <?= $member->id == 0 ? 'readonly' : '' ?> <?= $readonly ? 'readonly' : '' ?> class="form-control" id="status_id" name="status_id" required style="height: calc(2.25rem + 2px); padding: .375rem .75rem;
                     font-size: 1rem; font-weight: 400; line-height: 1.5;">
                    <?php foreach ($status_types as $type) { ?>
                    <option value="<?= $type->id ?>" <?= $member->id == 0 && $type->id == $status_new ? 'selected="selected"' : $member->status_id == $type->id ? 'selected="selected"' : ''; ?>><?= $type->name ?> </option>
                    <?php } ?>
                </select>
                <div class="invalid-feedback">
                    Please provide a status
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-4 mb-2">
                <label for="firstName">First name*</label>
                <input type="text" class="form-control" id="firstName" name="first_name" placeholder="First name" value="<?= $member->first_name ?>" required <?= $readonly ? 'readonly' : '' ?> />
                <div class="invalid-feedback">
                    Please provide a first name.
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <label for="lastName">Last name*</label>
                <input type="text" class="form-control" id="lastName" name="last_name" placeholder="Last name" value="<?= $member->last_name ?>" required <?= $readonly ? 'readonly' : '' ?> />
                <div class="invalid-feedback">
                    Please provide a last name.
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <label for="username">Username*</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="inputGroupPrepend">@</span>
                    </div>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" value="<?= $member->username ?>" aria-describedby="inputGroupPrepend" required <?= $readonly ? 'readonly' : '' ?> />
                    <div class="invalid-feedback">
                        Please choose a username.
                    </div>
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-12 mb-6">
                <label for="address1">Address Line 1*</label>
                <input type="text" class="form-control" id="address1" name="address1" placeholder="Address 1" value="<?= $member->address1 ?>" required <?= $readonly ? 'readonly' : '' ?> />
                <div class="invalid-feedback">
                    Please provide an address.
                </div>
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
                <input type="text" class="form-control" id="city" name="city" placeholder="City" value="<?= $member->city ?>" required <?= $readonly ? 'readonly' : '' ?> />
                <div class="invalid-feedback">
                    Please provide a valid city.
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <label for="state">State*</label>
                <select class="form-control" id="state" name="state" placeholder="State" style="height: calc(2.25rem + 2px); padding: .375rem .75rem;
                     font-size: 1rem; font-weight: 400; line-height: 1.5;" required <?= $readonly ? 'readonly' : '' ?>>
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

                <div class="invalid-feedback">
                    Please provide a valid state.
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <label for="zipcode">Zip*</label>
                <input type="text" class="form-control" id="zipcode" name="zipcode" placeholder="Zip" pattern="(\d{5}([\-]\d{4})?)" value="<?= $member->zipcode ?>" required <?= $readonly ? 'readonly' : '' ?>>
                <div class="invalid-feedback">
                    Please provide a valid zip.
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-3 mb-2">
                <label for="phone">Phone</label>
                <input type="tel" class="form-control" id="phone" name="phone" pattern="[\(][0-9]{3}[\)] [0-9]{3}[\-][0-9]{4}" placeholder="Phone Number" value="<?= $member->phone ?>" <?= $readonly ? 'readonly' : '' ?> />
                <small id="emailHelp" class="form-text text-muted">Like (555) 555-5555.</small>
                <div class="invalid-feedback">
                    Please provide a valid phone number.
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <label for="mobile">Mobile</label>
                <input type="tel" class="form-control" id="mobile" name="mobile" pattern="[\(][0-9]{3}[\)] [0-9]{3}[\-][0-9]{4}" placeholder="Mobile Number" value="<?= $member->mobile ?>" <?= $readonly ? 'readonly' : '' ?> />
                <small id="emailHelp" class="form-text text-muted">Like (555) 555-5555.</small>
                <div class="invalid-feedback">
                    Please provide a valid mobile.
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <label for="email">Email*</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required value="<?= $member->email ?>" <?= $readonly ? 'readonly' : '' ?> />
                <div class="invalid-feedback">
                    Please provide a valid email.
                </div>
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
        <button class="btn btn-primary" type="button" id="mmsubmit" <?= $can_update ? '' : 'hidden' ?>><?= esc_html__('Save', PLUGIN_TEXT_DOMAIN) ?></button>
        <button class="btn btn-secondary" type="button" id="addDependents" <?= $can_update ? '' : 'hidden' ?>><?= esc_html__('Add Dependents', PLUGIN_TEXT_DOMAIN) ?></button>
        <button class="btn btn-secondary" type="button" <?= $back ? '' : 'hidden' ?> onclick="window.location.href = '<?php echo esc_url(add_query_arg(array('page' => wp_unslash($_REQUEST['referer'])), admin_url('admin.php'))); ?>'"><?= esc_html__('Back', 'scsmm') ?>
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
<script>
    var dependents = <?= count($dependents) ?>;

    jQuery("button#addDependents").click(function() {
        jQuery('#row0').clone().appendTo('#dependantTable tbody');
        dependents++;
        updateDepNo(0, dependents);
        jQuery('#row' + dependents).css('display', '');

    });

    jQuery("#dependantTable").on('click', '.removeDependant', function(event) {
        let row = event.target.getAttribute('data-row');
        let row_number = event.target.getAttribute('data-row-number');
        jQuery('#' + row).remove();

        // renumber all the rows below the row deleted.
        for (let i = parseInt(row_number) + 1; i <= dependents; i++) {
            updateDepNo(i, i - 1);
        }
        dependents--;
    });

    jQuery("button#mmsubmit").click(function(event) {

        var form = jQuery("#applyform");

        if (form[0].checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
            form[0].classList.add('was-validated');
            return;
        }

        form[0].classList.add('was-validated');


        var applyData = jQuery('#applyform').serialize();
        applyData += '&dependantCount=' + dependents;
        applyData += '&security=' + '<?= $ajax_nonce; ?>';

        jQuery.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: applyData,
            beforeSend: function() {
                jQuery("#loader").show();
            },

            success: function(response) {
                console.log('add success ', response);
                var res = JSON.parse(response);


                if (res.success) {
                    displaySuccess(res.msg);
                    if(res.insert == 'insert')
                        window.location.href = '<?= $redirect ?>';
                } else
                    displayAlert(res.msg);

                jQuery("#loader").hide();
                jQuery("button#delete").show();
            },
            error: function(response) {
                console.log('add error ', response);
					const contentType = response.getResponseHeader("content-type");
					if (contentType && contentType.indexOf("application/json") !== -1) {
						return response.json().then(data => {
							// process your JSON data further
							var errmsg = response.responseText;
							displayAlert(errmsg);
							jQuery("#loader").hide();

						});
					} else {
						var errmsg = response.responseText;
						if (errmsg == '')
							errmsg = response.statusText;
						displayAlert(errmsg);
                        jQuery("#loader").hide();
                        jQuery("button#delete").show();
					}

             
            }
        });
    });

    function updateDepNo(oldRow, newRow) {
        var findrow = 'row' + oldRow;
        //var rows = jQuery('#'+ findrow + ':first').length;
        //var cells = jQuery('[data-row=' + findrow + ']').length;
        //var all = jQuery('#'+ findrow + ':first').find('[data-row=' + findrow + ']').length;
        //alert(rows);
        //alert(cells);
        //alert(all);

        jQuery('#' + findrow + ':first').find('[data-row=' + findrow + ']').each(function(index) {
            //, '[data-row=' + findrow + ']'
            switch (this.id) {
                case "id":
                    this.name = "id" + newRow;
                    jQuery(this).attr('data-row', "row" + newRow);
                    break;
                case "membership_id":
                    this.name = "membership_id" + newRow;
                    jQuery(this).attr('data-row', "row" + newRow);
                    break;
                case "first_name":
                    this.name = "first_name" + newRow;
                    jQuery(this).attr('data-row', "row" + newRow);
                    break;

                case "last_name":
                    this.name = "last_name" + newRow;
                    jQuery(this).attr('data-row', "row" + newRow);
                    break;
                case "relationship_id":
                    this.name = "relationship_id" + newRow;
                    jQuery(this).attr('data-row', "row" + newRow);
                    break;
                case "phone":
                    this.name = "phone" + newRow;
                    jQuery(this).attr('data-row', "row" + newRow);
                    break;
                case "mobile":
                    this.name = "mobile" + newRow;
                    jQuery(this).attr('data-row', "row" + newRow);
                    break;
                case "email":
                    this.name = "email" + newRow;
                    jQuery(this).attr('data-row', "row" + newRow);
                    break;

            }

        });

        // fix the header
        jQuery('#header-row' + oldRow + ':first').each(function(index) {
            jQuery(this).text('Dependant ' + newRow);
            this.id = 'header-row' + newRow;
        });

        // fix the table row id
        jQuery('#' + findrow + ':first').each(function(index) {
            this.id = 'row' + newRow;
        });

        // fix the delete button
        jQuery('#button-row' + oldRow + ':first').each(function(index) {
            jQuery(this).attr('data-row', 'row' + newRow);
            jQuery(this).attr('data-row-number', newRow);
            this.id = 'row' + newRow;
        });

    }







    function displayAlert(msg) {
        jQuery("#alertText").html(msg);
        jQuery("#alert").show();
    }

    function displaySuccess(msg) {
        console.log('display success fired');
        jQuery("#successText").html(msg);
        jQuery("#success").show();
    }
</script>