<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://webmuehle.at
 * @since      1.0.0
 *
 * @package    scsmm
 * @subpackage scsmm/admin/partials
 */
?>

<?php
if (!current_user_can('manage_options')) {
  wp_die();
}

global $wpdb;
$table_name = $this->getTable('membership_types');
$membershiptypes = $wpdb->get_results("SELECT * FROM $table_name ORDER BY name");

?>

<div class="container">
  <div id="alert" class="alert alert-info alert-dismissible" role="alert" style="display:none">
    <button id="alertClose" type="button" class="close">&times;</button>
    <span id="alertText">Something went wrong.</span>
  </div>

  <div id="success" class="alert alert-success alert-dismissible" role="alert" style="display:none">
    <button id="successClose" type="button" class="close">&times;</button>
    <span id="successText">Something went right.</span>
  </div>
  <h1><?= __('Manage Membership Types', 'scsmm'); ?></h1>

  <table class="table" id="typeTable">
    <thead>
      <tr>
        <th> <?= __('Name', 'scsmm'); ?></th>
        <th><?= __('Description', 'scsmm'); ?></th>
        <th><?= __('Edit', 'scsmm'); ?></th>
        <th><?= __('Delete', 'scsmm'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php for ($i = 0; $i < sizeof($membershiptypes); $i++) {
        $item = $membershiptypes[$i]; ?>
        <tr>
          <td><?= $item->name ?></td>
          <td><?= $item->description ?></td>
          <td><a id="editType" class="page-action" href="#" data-item="<?= $item->id ?>"><?= __('Edit', 'scsmm'); ?></a></td>
          <td><a id="deleteType" class="page-action" href="#" data-item="<?= $item->id ?>"><?= __('Delete', 'scsmm'); ?></a></td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
  <div>
    <button type="button" class="btn btn-secondary" id="createType" data-item="0"><?= __('Create', 'scsmm'); ?></button>
  </div>
</div>


<div class="modal fade" id="typeModal" tabindex="-1" role="dialog" aria-labelledby="membertypemodal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="memberTypeModalTitle">Edit Member Type</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formTypeModal">
          <input type="text" hidden id="modalId" name="id" value="0" />
          <div class="form-group row">
            <label for="modalName" class="col-sm-2 col-form-label">Name</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="modalName" name="name" placeholder="Input name" required>
            </div>
            <div class="invalid-feedback">
                Please provide a name for the membership type.
            </div>
          </div>
          <div class="form-group row">
            <label for="modalDesc" class="col-sm-2 col-form-label">Description</label>
            <div class="col-sm-10">
              <textarea type="text" class="form-control" id="modalDesc" name="description" placeholder="Input Description"></textarea>
            </div>
          </div>
          <div class="form-group row">
            <label for="modalCost" class="col-sm-2 col-form-label">Cost</label>
            <div class="input-group col-sm-10">
              <div class="input-group-prepend">
                <span class="input-group-text">$</span>
              </div>
              <input type="text" class="form-control" id="modalCost" name="cost" placeholder="Input Cost">
              <div class="input-group-append">
                <span class="input-group-text">.00</span>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button id="closeModal" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button id="saveChanges" type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

<script>
  /*
  jQuery('#typeTable').on('click', '#editMember', function(event) {
    var id = event.target.attributes['data-item'].value;
    var typeData = 'action=membership_types_update&requestType=get&&name=blank&id=' + id;
    jQuery.when(sendRequest(typeData)).then(function(data, textStatus, jqXHR) {
      var results = jQuery.parseJSON(data);
      if (results.success == "1") {
        jQuery('#modalId').val(results.result['id']);
        jQuery('#modalName').val(results.result['name']);
        jQuery('#modalDesc').val(results.result['description']);
        jQuery('#modalCost').val(results.result['cost']);
        jQuery('#typeModal').modal();
      } else {
        displayAlert(results.message);
      };
    });
  });

  jQuery('#typeTable').on('click', '#deleteMember', function(event) {
    var id = event.target.attributes['data-item'].value;
    var typeData = 'action=membership_types_update&requestType=delete&name="blank"&id=' + id;
    jQuery.when(sendRequest(typeData)).then(function(data, textStatus, jqXHR) {
      alert(jqXRH.status);
    });

    alert("delete " + id);
  });

  jQuery('#createMember').click(function(event) {
    jQuery('#typeModal').modal();
  });

  // handle the save changes button on the modal
  jQuery('#saveChanges').click(function(event) {


    var form = jQuery("#formTypeModal");

    if (form[0].checkValidity() === false) {
      event.preventDefault();
      event.stopPropagation();
      form[0].classList.add('was-validated');
      return;
    }

    form[0].classList.add('was-validated');

    var typeData = jQuery('#formTypeModal').serialize();
    typeData += '&action=membership_types_update&requestType=save';

    jQuery.when(sendRequest(typeData)).then(function(data, textStatus, jqXHR) {
      var results = jQuery.parseJSON(data);
      if (results.success == "1") {
        displaySuccess(results.msg);
      } else {
        displayAlert(results.msg);

      }
      // close the modal
      jQuery('#memberTypeModal').modal();
    });
  });

  jQuery('#closeModal').click(function(event) {
    jQuery('#typeModal').modal();
  });

  function sendRequest(typeData) {
    return jQuery.ajax({
      type: "POST",
      url: '/wp-admin/admin-ajax.php',
      data: typeData
    });
  } */
</script>