<tr <?= $i == 0 ? 'style="display:none"' : '' ?> id="row<?=$i?>" class="dependant-row">
    <td>
        <input type="text" hidden id="dep_id" name="dep_id[]" data-row="row<?=$i?>" value="<?= $i>0 ? $dependents[$i-1]->id : ''?>" />
        <input type="text" hidden id="dep_membership_id" name="dep_membership_id[]" data-row="row<?=$i?>" value="<?= $i>0 ? $dependents[$i-1]->membership_id : ''?>" />
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
                <label for="dep_first_name">First name*</label>
                <input type="text" class="form-control" data-row="row<?=$i?>" id="dep_first_name" name="dep_first_name[]"
                    placeholder="First name" value="<?= $i>0 ? $dependents[$i-1]->first_name : ''?>"  <?= $readonly ? 'readonly' : '' ?>/>
              
            </div>
            <div class="col-md-4 mb-2">
                <label for="lastName">Last name*</label>
                <input type="text" class="form-control" data-row="row<?=$i?>" id="dep_last_name" name="dep_last_name[]"
                    placeholder="Last name" value="<?=  $i>0 ? $dependents[$i-1]->last_name : ''?>"  <?= $readonly ? 'readonly' : '' ?>>
               
            </div>
            <div class="col-md-4 mb-2">
                <label for="dep_relationship_id">Relationship*</label>
                <select class="form-control" id="dep_relationship_id" data-row="row<?=$i?>" name="dep_relationship_id[]"
                    placeholder="Last name"  style="height: calc(2.25rem + 2px); padding: .375rem .75rem;
                     font-size: 1rem; font-weight: 400; line-height: 1.5;" <?= $readonly ? 'readonly' : '' ?> >
                <?php foreach($relationship_types as $r) { ?>
                    <option value="<?= $r->id ?>" <?= $i>0 ? $dependents[$i-1]->relationship_id == $r->id ? 'selected="selected"' : '' : ''; ?> ><?=$r->name?></option>
                <?php } ?>
                </select>
           
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-4 mb-2">
                <label for="dep_phone">Phone</label>
                <input type="tel" class="form-control masked" id="dep_phone" data-row="row<?=$i?>" name="dep_phone[]"
                    data-inputmask="'mask': '(999) 999-9999'" placeholder="Phone"
                    value="<?= $i>0 ? $dependents[$i-1]->phone : '' ?>" <?= $readonly ? 'readonly' : '' ?>/>
            </div>
            <div class="col-md-4 mb-2">
                <label for="dep_mobile">Mobile</label>
                <input type="tel" class="form-control phone" id="dep_mobile" data-row="row<?=$i?>" name="dep_mobile[]"
                    data-inputmask="'mask': '(999) 999-9999'" placeholder="Mobile"
                    value="<?= $i>0 ? $dependents[$i-1]->mobile : '' ?>" <?= $readonly ? 'readonly' : '' ?>/>
               
            </div>
            <div class="col-md-4 mb-2">
                <label for="dep_email">Email</label>
                <input type="text" class="form-control" id="dep_email" data-row="row<?=$i?>" name="dep_email[]"
                data-inputmask="'alias': 'email'"
                    placeholder="Email" value="<?= $i>0 ? $dependents[$i-1]->email : ''?>" <?= $readonly ? 'readonly' : '' ?>/>
                <small id="emailHelp" class="form-text text-muted">Required for automated registration</small>
              
            </div>
        </div>

    </td>
</tr>