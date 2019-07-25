<tr id="row<?=$i+1?>" class="dependant-row">
    <td>

        <div class="row" style="border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: gray;height:35px; 
                overflow:auto; display: inline-block; width: 100%">

            <div style="float:left;line-height:30px;">
                <h5 id="header-row<?=$i+1?>">Dependant <?=$i+1?></h5>
            </div>
            <button style="float:right" type="button" class="close" id="removeDependant" data-row="row<?=$i+1?>"
                data-row-number="<?=$i+1?>">&times;</button>

        </div>
        <div class="form-row">
            <div class="col-md-4 mb-2">
                <label for="firstName">First name</label>
                <input type="text" class="form-control" data-row="row<?=$i+1?>" id="firstname" name="firstName<?=$i+1?>"
                    placeholder="First name" value="<?=$dependants[$i]->firstname?>" required />
                <div class="invalid-feedback">
                    Please provide a first name.
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <label for="lastName">Last name</label>
                <input type="text" class="form-control" data-row="row<?=$i+1?>" id="lastname" name="lastName<?=$i+1?>"
                    placeholder="Last name" value="<?=$dependants[$i]->lastname?>" required>
                <div class="invalid-feedback">
                    Please provide a last name.
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <label for="relationship">Relationship</label>
                <select class="form-control" id="relationship" data-row="row<?=$i+1?>" name="relationship<?=$i+1?>"
                    placeholder="Last name" required style="height: calc(2.25rem + 2px); padding: .375rem .75rem;
                     font-size: 1rem; font-weight: 400; line-height: 1.5;">
                <?php foreach($relationships as $r) { ?>
                    <option value="<?= $r->id ?>" <?= $dependants[$i]->relationshipid == $r->id ? 'selected="selected"' : ''; ?> ><?=$r->name?></option>
                <?php } ?>
                </select>
                <div class="invalid-feedback">
                    Please provide a last name.
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-4 mb-2">
                <label for="phone">Phone</label>
                <input type="tel" class="form-control" id="phone" data-row="row<?=$i+1?>" name="phone<?=$i+1?>"
                    pattern="[\(][0-9]{3}[\)] [0-9]{3}[\-][0-9]{4}" placeholder="Phone Number"
                    value="<?=$dependants[$i]->phone?>" />
                <small id="emailHelp" class="form-text text-muted">Like (555) 555-5555.</small>
                <div class="invalid-feedback">
                    Please provide a valid phone number.
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <label for="mobile">Mobile</label>
                <input type="tel" class="form-control" id="mobile" data-row="row<?=$i+1?>" name="mobile<?=$i+1?>"
                    pattern="[\(][0-9]{3}[\)] [0-9]{3}[\-][0-9]{4}" placeholder="Mobile Number"
                    value="<?=$dependants[$i]->mobile?>" />
                <small id="emailHelp" class="form-text text-muted">Like (555) 555-5555.</small>
                <div class="invalid-feedback">
                    Please provide a valid mobile.
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" data-row="row<?=$i+1?>" name="email<?=$i+1?>"
                    placeholder="Email" value="<?=$dependants[$i]->email?>" />

                <div class="invalid-feedback">
                    Please provide a valid email.
                </div>
            </div>
        </div>

    </td>
</tr>