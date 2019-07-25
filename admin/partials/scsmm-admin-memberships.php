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
    <table class="table">
        <thead>
            <tr>
                <th class="manage-column column-title column-primary"><?=__('Type', 'scsmm');?></th>
                <th class="manage-column column-title column-primary"><?=__('Status', 'scsmm');?></th>
                <th class="manage-column column-title column-primary"><?=__('FirstName', 'scsmm');?></th>
                <th class="manage-column column-title column-primary"><?=__('LastName', 'scsmm');?></th>
                <th class="manage-column column-title column-primary"><?=__('Join Date', 'scsmm');?></th>
                <th class="manage-column column-title"><?=__('Show All', 'scsmm');?></th>
                <th class="manage-column column-title"><?=__('Action', 'scsmm');?></th>
            </tr>
        </thead>
        <tbody>
            <?php
$i = 0;
while ($i < sizeof($memberships)) {
    $item = $memberships[$i];?>
            <tr>
                <td><?=$item->type?></td>
                <td><?=$item->status?></td>
                <td><?=$item->firstname?></td>
                <td><?=$item->lastname?></td>
                <td>
                    <?=date_i18n(get_option('date_format'), strtotime($item->joindate))?>
                </td>
                <td>
                <td>
                    <a class="page-action" href="<?= admin_url("admin.php?page=scsmm-membership&memberid={$item->id}") ?>"><?= __('Edit', 'scsmm');?></a>
                </td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?=$item->id?>" />
                        <a class="page_action" type="submit" name="delete"><?= __('Delete', 'scsmm');?></a>

                    </form>
                </td>
            </tr>

        </tbody>
    </table>
    <?php $i++;
}?>

    </tbody>
    </table>
    <p></p>
</div>
