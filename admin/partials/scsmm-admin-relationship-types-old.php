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
  $table_name = $this->getTable('relationship_types');
  $types = $wpdb->get_results("SELECT * FROM $table_name ORDER BY name");

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
  <h3><?= __('Manage Relationship Types', 'scsmm'); ?></h3>

  <table class="table" id="typeTable">
    <thead>
      <tr>
        <th> <?= __('Name', 'scsmm'); ?></th>
        <th><?= __('Edit', 'scsmm'); ?></th>
        <th><?= __('Delete', 'scsmm'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php for ($i = 0; $i < sizeof($types); $i++) {
        $item = $types[$i]; ?>
        <tr>
          <td><?= $item->name ?></td>
          <td><a id="editType" class="page-action" href="#" data-table="relationship_types" data-item="<?= $item->id ?>"><?= __('Edit', 'scsmm'); ?></a></td>
          <td><a id="deleteType" class="page-action" href="#" data-table="relationship_types" data-item="<?= $item->id ?>"><?= __('Delete', 'scsmm'); ?></a></td>
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
          <input type="text" hidden id="modalTable" name="tableName" value="relationship_types" />
          <div class="form-group row">
            <label for="modalName" class="col-sm-2 col-form-label">Name</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="modalName" name="name" placeholder="Input name" required>
            </div>
            <div class="invalid-feedback">
                Please provide a name for the relationship type.
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


