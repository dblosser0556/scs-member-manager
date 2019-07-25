<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://
 * @since      1.0.0
 *
 * @package    scsmm
 * @subpackage scsmm/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<?php

if (!current_user_can('manage_options')) {
    wp_die();
}

global $wpdb;
$table_name = $this->getTable('membership_types');

if (isset($_GET['typeID'])) {
    $typeID = (int)$_GET['typeID'];
}



if (isset($_POST['delete']) && isset($_POST['id']) && (int)$_POST['id'] > 0) { // delete
    $wpdb->delete($table_name, array('id' => (int)$_POST['id']));
    remove_type( $_POST['slug']);
}

if (isset($_POST['submit'])) {
    if (isset($_POST['id']) && (int)$_POST['id'] > 0) { // edit
        $wpdb->update(
            $table_name,
            array(
                'name' => $_POST['name']
            ),
            array('id' => (int)$_POST['id']),
            array(
                '%s'
            )
        );
        $typeID = (int)$_POST['id'];
        $message = __('Successfully changed!', 'scsmm');
    } else { // create
        $wpdb->insert(
            $table_name,
            array(
                'name' => $_POST['name']   //Display name
          
            ),
            array(
                '%s',
                
            )
        );

        $message = __('Successfully created!', 'scsmm');
        $typeID = $wpdb->insert_id;
    }
}

if (isset($typeID) && $typeID > 0) {
    $type = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $typeID");
}

if (!isset($type)) {
    $type = new stdClass();
    $type->id = 0;
    $type->name = '';
}

PC::debug($type, 'Type');
PC::debug($type->id, 'Id');
?>
<?php
  
?>


<div class="wrap">
  <a class="page-title-action" href="<?= admin_url("admin.php?page=scsmm-membership-types") ?>"><?= __('Back', 'scsmm'); ?></a>
  <h1 class="wp-heading-inline"><?= (isset($type) && $type->id > 0) ? $type->name . __(' Edit', 'scsmm') : __('Create Type', 'scsmm') ?></h1>
  <hr class="wp-header-end">
  <?php if (isset($message)) { ?>
    <div id="message" class="updated notice is-dismissible">
        <p><?= $message ?></p>
        <button type="button" class="notice-dismiss"></button>
    </div>
      <?php 
    } ?>
  <form method="post">
    <input type="hidden" name="id" value="<?= $type->id ?>" />
    <table>
      <tr>
        <td><?= __('Name', 'scsmm'); ?></td>
        <td><input type="text" name="name" maxlength="255" value="<?= $type->name ?>" required /></td>
      </tr>
     
      <tr>
        <td></td>
        <td><input class="button" type="submit" name="submit" /></td>
      </tr>
      <?php if (isset($type) && $type->id > 0) { ?>
        <tr>
          <td colspan="2"><hr/></td>
        </tr>
        <tr>
          <td><?= __('Delete type', 'scsmm'); ?></td>
          <td><input class="button" type="submit" name="delete" value=<?= __('Delete', 'scsmm'); ?> /></td>
        </tr>
      <?php 
    } ?>
  </form>
</div>

