<tr <?= $i == 0 ? 'style="display:none"' : '' ?> id="row<?=$i?>" class="dependant-row">
    <td>
        <input type="text" hidden id="id" name="id<?=$i?>" data-row="row<?=$i?>" value="<?= $i>0 ? $dependents[$i-1]->id : ''?>" />
        <input type="text" hidden id="membershipid" name="membershipid" data-row="row<?=$i?>" value="<?= $i>0 ? $dependents[$i-1]->membershipid : ''?>" />
        <div class="row" style="border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: gray;height:35px; 
                overflow:auto; display: inline-block; width: 100%">

            <div style="float:left;line-height:30px;">
                <h5 id="header-row<?=$i?>">Dependant <?=$i?></h5>
            </div>
            <button id="button-row<?=$i?>" <?= $readonly ? 'hidden' : '' ?> class="removeDependant" style="float:right" type="button" class="close" id="removeDependant" data-row="row<?=$i?>"
                data-row-number="<?=$i?>">&times;</button>

        </div>
        <div class="form-row">
            <div class="col-md-4 mb-2">
                <label for="firstName">First name*</label>
                <input type="text" class="form-control" data-row="row<?=$i?>" id="firstname" name="firstname<?=$i?>"
                    placeholder="First name" value="<?= $i>0 ? $dependents[$i-1]->firstname : ''?>" required <?= $readonly ? 'readonly' : '' ?>/>
                <div class="invalid-feedback">
                    Please provide a first name.
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <label for="lastName">Last name*</label>
                <input type="text" class="form-control" data-row="row<?=$i?>" id="lastname" name="lastname<?=$i?>"
                    placeholder="Last name" value="<?=  $i>0 ? $dependents[$i-1]->lastname : ''?>" required <?= $readonly ? 'readonly' : '' ?>>
                <div class="invalid-feedback">
                    Please provide a last name.
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <label for="relationship">Relationship*</label>
                <select class="form-control" id="relationshipid" data-row="row<?=$i?>" name="relationshipid<?=$i?>"
                    placeholder="Last name" required style="height: calc(2.25rem + 2px); padding: .375rem .75rem;
                     font-size: 1rem; font-weight: 400; line-height: 1.5;" <?= $readonly ? 'readonly' : '' ?> >
                <?php foreach($relationship_types as $r) { ?>
                    <option value="<?= $r->id ?>" <?= $i>0 ? $dependents[$i-1]->relationshipid == $r->id ? 'selected="selected"' : '' : ''; ?> ><?=$r->name?></option>
                <?php } ?>
                </select>
                <div class="invalid-feedback">
                    Please provide a relationship
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-4 mb-2">
                <label for="phone">Phone</label>
                <input type="tel" class="form-control" id="phone" data-row="row<?=$i?>" name="phone<?=$i?>"
                    pattern="[\(][0-9]{3}[\)] [0-9]{3}[\-][0-9]{4}" placeholder="Phone Number"
                    value="<?= $i>0 ? $dependents[$i-1]->phone : '' ?>" <?= $readonly ? 'readonly' : '' ?>/>
                <small id="emailHelp" class="form-text text-muted">Like (555) 555-5555.</small>
                <div class="invalid-feedback">
                    Please provide a valid phone number.
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <label for="mobile">Mobile</label>
                <input type="tel" class="form-control" id="mobile" data-row="row<?=$i?>" name="mobile<?=$i?>"
                    pattern="[\(][0-9]{3}[\)] [0-9]{3}[\-][0-9]{4}" placeholder="Mobile Number"
                    value="<?= $i>0 ? $dependents[$i-1]->mobile : '' ?>" <?= $readonly ? 'readonly' : '' ?>/>
                <small id="emailHelp" class="form-text text-muted">Like (555) 555-5555.</small>
                <div class="invalid-feedback">
                    Please provide a valid mobile.
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" data-row="row<?=$i?>" name="email<?=$i?>"
                    placeholder="Email" value="<?= $i>0 ? $dependents[$i-1]->email : ''?>" <?= $readonly ? 'readonly' : '' ?>/>

                <div class="invalid-feedback">
                    Please provide a valid email.
                </div>
            </div>
        </div>

    </td>
</tr>