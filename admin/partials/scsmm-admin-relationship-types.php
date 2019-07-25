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
  $membershiptypes = $wpdb->get_results("SELECT * FROM $table_name ORDER BY name");

  ?>

<div class="wrap">
  <h1 class="wp-heading-inline"><?= __('Manage Relationship Types', 'scsmm');?></h1>
  <a class="page-title-action" href="<?= admin_url("admin.php?page=scsmm-relationship-type") ?>"><?= __('Create', 'scsmm');?></a>
  <hr class="wp-header-end">

  <table class="wp-list-table widefat fixed striped posts">
    <thead>
      <tr>
        <th class="manage-column column-title column-primary"><?= __('Name', 'scsmm');?></th>
        <th class="manage-column column-title"><?= __('Action', 'scsmm');?></th>
      </tr>
    </thead>
    <tbody>
      <?php for($i=0;$i<sizeof($membershiptypes);$i++) { $item = $membershiptypes[$i]; ?>
        <tr>
          <td><?= $item->name ?></td>
          <td><a class="page-action" href="<?= admin_url("admin.php?page=scsmm-relationship-type&typeID={$item->id}") ?>"><?= __('Edit', 'scsmm');?></a>
        </tr>
      <?php } ?>
    </tbody>
  </table>
  <p></p>
</div>
