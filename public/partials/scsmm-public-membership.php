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
global $wpdb;

$relationships = $this->get_relationships();

if (isset($_GET['memberid'])) {
    $memberid = $_GET['memberid'];
    $member = $this->get_member($memberid);
    $dependants = $this->get_dependants($memberid);
}

if(!isset($member)) {
    $member = new stdClass();
    $member->id = 0;
    $member->firstName = '';
    $member->lastName = '';
    $member->userName = '';
    $member->address1 = '';
    $member->address2 = '';
    $member->city = '';
    $member->state = '';
    $member->zipcode = '';
    $member->homephone = '';
    $member->mobile = '';
    $member->email = '';
    $member->notes = '';

    $dependants = array();
}

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

    <form id="applyform" class="needs-validation" novalidate>
        <input type="text" hidden name="action" value="member_registration" />
        <input type="text" hidden name="id" value="<?=$member->id?>" />
        <div class="form-row" style="border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: gray;height:30px;">
            <div style="float:left;line-height:30px;">
                <h5>Applicant</h5>
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-4 mb-2">
                <label for="firstName">First name</label>
                <input type="text" class="form-control" id="firstName" name="firstName" placeholder="First name"
                    value="<?=$member->firstname?>" required />
                <div class="invalid-feedback">
                    Please provide a first name.
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <label for="lastName">Last name</label>
                <input type="text" class="form-control" id="lastName" name="lastName" placeholder="Last name"
                    value="<?=$member->lastname?>" required />
                <div class="invalid-feedback">
                    Please provide a last name.
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <label for="username">Username</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="inputGroupPrepend">@</span>
                    </div>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username"
                        aria-describedby="inputGroupPrepend" required />
                    <div class="invalid-feedback">
                        Please choose a username.
                    </div>
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-12 mb-6">
                <label for="address1">Address Line 1</label>
                <input type="text" class="form-control" id="address1" name="address1" placeholder="Address 1"
                    value="<?=$member->address1?>" required />
                <div class="invalid-feedback">
                    Please provide an address.
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-12 mb-6">
                <label for="address2">Address Line 2</label>
                <input type="text" class="form-control" id="address2" name="address2" placeholder="Address 2"
                    value="<?=$member->address2?>" />
            </div>

        </div>
        <div class="form-row">
            <div class="col-md-6 mb-3">
                <label for="city">City</label>
                <input type="text" class="form-control" id="city" name="city" placeholder="City"
                    value="<?=$member->city?>" required />
                <div class="invalid-feedback">
                    Please provide a valid city.
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <label for="state">State</label>
                <select class="form-control" id="state" name="state" placeholder="State" 
                style="height: calc(2.25rem + 2px); padding: .375rem .75rem;
                     font-size: 1rem; font-weight: 400; line-height: 1.5;" required>
                    <option <?php if ($member->state == "AL") echo 'selected' ; ?> value="AL">Alabama</option>
                    <option <?php if ($member->state == "AK") echo 'selected' ; ?> value="AK">Alaska</option>
                    <option <?php if ($member->state == "AZ") echo 'selected' ; ?> value="AZ">Arizona</option>
                    <option <?php if ($member->state == "AR") echo 'selected' ; ?> value="AR">Arkansas</option>
                    <option <?php if ($member->state == "CA") echo 'selected' ; ?> value="CA">California</option>
                    <option <?php if ($member->state == "CO") echo 'selected' ; ?> value="CO">Colorado</option>
                    <option <?php if ($member->state == "CT") echo 'selected' ; ?> value="CT">Connecticut</option>
                    <option <?php if ($member->state == "DE") echo 'selected' ; ?> value="DE">Delaware</option>
                    <option <?php if ($member->state == "DC") echo 'selected' ; ?> value="DC">District Of Columbia</option>
                    <option <?php if ($member->state == "FL") echo 'selected' ; ?> value="FL">Florida</option>
                    <option <?php if ($member->state == "GA") echo 'selected' ; ?> value="GA">Georgia</option>
                    <option <?php if ($member->state == "HI") echo 'selected' ; ?> value="HI">Hawaii</option>
                    <option <?php if ($member->state == "ID") echo 'selected' ; ?> value="ID">Idaho</option>
                    <option <?php if ($member->state == "IL") echo 'selected' ; ?> value="IL">Illinois</option>
                    <option <?php if ($member->state == "IN") echo 'selected' ; ?> value="IN">Indiana</option>
                    <option <?php if ($member->state == "IA") echo 'selected' ; ?> value="IA">Iowa</option>
                    <option <?php if ($member->state == "KS") echo 'selected' ; ?> value="KS">Kansas</option>
                    <option <?php if ($member->state == "KY") echo 'selected' ; ?> value="KY">Kentucky</option>
                    <option <?php if ($member->state == "LA") echo 'selected' ; ?> value="LA">Louisiana</option>
                    <option <?php if ($member->state == "ME") echo 'selected' ; ?> value="ME">Maine</option>
                    <option <?php if ($member->state == "MD") echo 'selected' ; ?> value="MD">Maryland</option>
                    <option <?php if ($member->state == "MD") echo 'selected' ; ?> value="MA">Massachusetts</option>
                    <option <?php if ($member->state == "MI") echo 'selected' ; ?> value="MI">Michigan</option>
                    <option <?php if ($member->state == "MN") echo 'selected' ; ?> value="MN">Minnesota</option>
                    <option <?php if ($member->state == "MS") echo 'selected' ; ?> value="MS">Mississippi</option>
                    <option <?php if ($member->state == "MO") echo 'selected' ; ?> value="MO">Missouri</option>
                    <option <?php if ($member->state == "MT") echo 'selected' ; ?> value="MT">Montana</option>
                    <option <?php if ($member->state == "NE") echo 'selected' ; ?> value="NE">Nebraska</option>
                    <option <?php if ($member->state == "NV") echo 'selected' ; ?> value="NV">Nevada</option>
                    <option <?php if ($member->state == "NH") echo 'selected' ; ?> value="NH">New Hampshire</option>
                    <option <?php if ($member->state == "NJ") echo 'selected' ; ?> value="NJ">New Jersey</option>
                    <option <?php if ($member->state == "NM") echo 'selected' ; ?> value="NM">New Mexico</option>
                    <option <?php if ($member->state == "NY") echo 'selected' ; ?> value="NY">New York</option>
                    <option <?php if ($member->state == "NC") echo 'selected' ; ?> value="NC">North Carolina</option>
                    <option <?php if ($member->state == "ND") echo 'selected' ; ?> value="ND">North Dakota</option>
                    <option <?php if ($member->state == "OH") echo 'selected' ; ?> value="OH">Ohio</option>
                    <option <?php if ($member->state == "OK") echo 'selected' ; ?> value="OK">Oklahoma</option>
                    <option <?php if ($member->state == "OR") echo 'selected' ; ?> value="OR">Oregon</option>
                    <option <?php if ($member->state == "PA") echo 'selected' ; ?> value="PA">Pennsylvania</option>
                    <option <?php if ($member->state == "RI") echo 'selected' ; ?> value="RI">Rhode Island</option>
                    <option <?php if ($member->state == "SC") echo 'selected' ; ?> value="SC">South Carolina</option>
                    <option <?php if ($member->state == "SD") echo 'selected' ; ?> value="SD">South Dakota</option>
                    <option <?php if ($member->state == "TN") echo 'selected' ; ?> value="TN">Tennessee</option>
                    <option <?php if ($member->state == "TX") echo 'selected' ; ?> value="TX">Texas</option>
                    <option <?php if ($member->state == "UT") echo 'selected' ; ?> value="UT">Utah</option>
                    <option <?php if ($member->state == "VT") echo 'selected' ; ?> value="VT">Vermont</option>
                    <option <?php if ($member->state == "VA") echo 'selected' ; ?> value="VA">Virginia</option>
                    <option <?php if ($member->state == "WA") echo 'selected' ; ?> value="WA">Washington</option>
                    <option <?php if ($member->state == "WV") echo 'selected' ; ?> value="WV">West Virginia</option>
                    <option <?php if ($member->state == "WI") echo 'selected' ; ?> value="WI">Wisconsin</option>
                    <option <?php if ($member->state == "WY") echo 'selected' ; ?> value="WY">Wyoming</option>
                </select>

                <div class="invalid-feedback">
                    Please provide a valid state.
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <label for="zipcode">Zip</label>
                <input type="text" class="form-control" id="zipcode" name="zipcode" placeholder="Zip"
                    pattern="(\d{5}([\-]\d{4})?)" value="<?=$member->zipcode?>" required>
                <div class="invalid-feedback">
                    Please provide a valid zip.
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-4 mb-2">
                <label for="phone">Phone</label>
                <input type="tel" class="form-control" id="phone" name="phone"
                    pattern="[\(][0-9]{3}[\)] [0-9]{3}[\-][0-9]{4}" placeholder="Phone Number"
                    value="<?=$member->homephone?>">
                <small id="emailHelp" class="form-text text-muted">Like (555) 555-5555.</small>
                <div class="invalid-feedback">
                    Please provide a valid phone number.
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <label for="mobile">Mobile</label>
                <input type="tel" class="form-control" id="mobile" name="mobile"
                    pattern="[\(][0-9]{3}[\)] [0-9]{3}[\-][0-9]{4}" placeholder="Mobile Number"
                    value="<?=$member->mobile?>" />
                <small id="emailHelp" class="form-text text-muted">Like (555) 555-5555.</small>
                <div class="invalid-feedback">
                    Please provide a valid mobile.
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required
                    value="<?=$member->email?>" />
                <div class="invalid-feedback">
                    Please provide a valid email.
                </div>
            </div>


        </div>
        <div class="form-row">
            <table class="table" id="dependantTable">
                <tbody>
                    <?php
for ($i = 0; $i < count($dependants); $i++) {
    include plugin_dir_path(dirname(__FILE__)) . "tmpls/curdependant.php";
}
?>
                </tbody>
            </table>
        </div>
        <button class="btn btn-primary" type="button" id="mmsubmit">Submit form</button>
        <button class="btn btn-secondary" type="button" id="addDependants">Add Dependants</button>
    </form>

</div>

<script>
var tmpl_path = '<?=(plugins_url() . "/scs-member-manager/public/tmpls/newdependant.html")?>'
</script>