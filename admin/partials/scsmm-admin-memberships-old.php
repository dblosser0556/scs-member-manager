<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://webmuehle.at
 * @since      1.0.3
 *
 * @package    scsmm
 * @subpackage scsmm/admin/partials
 */
?>

<?php

if (!current_user_can('manage_options')) {
    wp_die();
}

# $this->cleanUpmemberships();

if (isset($_POST['id']) && isset($_POST['delete'])) {
    $this->delete_membership($_POST['id']);
    $message = "Deleted Membership";
}

if (isset($_POST['id']) && isset($_POST['edit'])) {
    $this->edit_membership($_POST['id']);
    $message = "Edit Membership";
}

$memberships = $this->get_memberships();

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="container">
    <h1 class="wp-heading-inline"><?=__('Memberships List', 'scsmm');?></h1>
    <hr class="wp-header-end">
    <?php if (isset($message)) {?>
    <div id="message" class="updated notice is-dismissible">
        <p><?=$message?></p>
        <button type="button" class="notice-dismiss"></button>
    </div>
    <?php
}?>
    <table class="table" id="memberstable">
        <thead>
            <tr>
                <th class="manage-column column-title column-primary"><?=__('Select', 'scsmm');?></th>
                <th class="manage-column column-title column-primary"><?=__('Type', 'scsmm');?></th>
                <th class="manage-column column-title column-primary"><?=__('Status', 'scsmm');?></th>
                <th class="manage-column column-title column-primary"><?=__('First Name', 'scsmm');?></th>
                <th class="manage-column column-title column-primary"><?=__('Last Name', 'scsmm');?></th>
                <th class="manage-column column-title column-primary"><?=__('Email', 'scsmm');?></th>
                <th class="manage-column column-title column-primary"><?=__('Join Date', 'scsmm');?></th>
                <th class="manage-column column-title"><?=__('Edit', 'scsmm');?></th>
                <th class="manage-column column-title"><?=__('Delete', 'scsmm');?></th>
            </tr>
        </thead>
        <tbody>
            <?php
$i = 0;
while ($i < sizeof($memberships)) {
    $item = $memberships[$i];?>
            <tr>
                <td><input class="form-check-input" type="checkbox" value="" id="memberCheck"></td>
                <td><?=$item->type?></td>
                <td><?=$item->status?></td>
                <td><?=$item->first_name?></td>
                <td><?=$item->last_name?></td>
                <td><?=$item->email?></td>
                <td><?=date_i18n(get_option('date_format'), strtotime($item->join_date))?></td>
                <td>
                    <a class="page-action"
                        href="<?= admin_url("admin.php?page=scsmm-membership&memberid={$item->id}") ?>">
                        <?= __('Edit', 'scsmm');?></a>
                </td>
                <td>
                    <a class="page_action" name="delete" href=""
                        row-data="<?=$item->id?>"><?= __('Delete', 'scsmm');?></a>
                </td>
            </tr>


            <?php $i++;}?>

        </tbody>
    </table>
</div>

<script>
jQuery('#memberstable').on('change', '#memberCheck', function(event) {
    if (this.checked) {
        jQuery(this).parent().parent().addClass('table-active')
    } else {
        jQuery(this).parent().parent().removeClass('table-active');
    }

});
</script>