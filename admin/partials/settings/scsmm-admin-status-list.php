<?php
$nonce = wp_create_nonce( 'edit_type_nonce' );

$query_args_edit_member = array(
    'page'        =>  wp_unslash($_REQUEST['page']),
    'action'    => 'add_status',
    'tab'       => 'status_options',
    '_wpnonce'    => wp_create_nonce('edit_type_nonce'),
);

$new_status_link = esc_url(add_query_arg($query_args_edit_member, admin_url('admin.php')));

$query_args_edit_submit = array(
    'page'        =>  wp_unslash($_REQUEST['page']),
    'action'    => 'save_status',
    'tab'       => 'status_options',
    '_wpnonce'    => wp_create_nonce('edit_type_nonce'),
);
$submit_status_link = esc_url(add_query_arg($query_args_edit_submit, admin_url('admin.php')));

$query_args_edit_cancel = array(
    'page'        =>  wp_unslash($_REQUEST['page']),
    'action'    => 'cancel_status',
    'tab'       => 'status_options',
    '_wpnonce'    => wp_create_nonce('edit_type_nonce'),
);
$cancel_status_link = esc_url(add_query_arg($query_args_edit_cancel, admin_url('admin.php')));
?>

<div class="wrap">

    <h1 class="wp-heading-inline"><?php _e("Membership Type List", $this->plugin_name); ?></h1>
    <a href="<?= $new_status_link ?>" class="page-title-action"><?= esc_html__("Add New", $this->plugin_name) ?> </a>
    <hr class="wp-header-end">
    <?php if ($this->status_list_table->get_message() !== '')  { ?>
    <div id="message" class="notice notice-success is-dismissible inline">
        <p><?= $this->status_list_table->get_message() ?></p>
        <button type="button" class="notice-dismiss"></button>
    </div>
    <?php 
    } ?>
    <?php if ($this->status_list_table->get_error_message() !== '') { ?>
    <div class="notice notice-error  is-dismissible inline">
        <p><?= $this->status_list_table->get_error_message() ?></p>
        <button type="button" class="notice-dismiss"></button>
    </div>
      <?php 
    } ?>
    <div id="scsmm-status-type-list">
        <div id="scsmm-post-body">
            <form id="scsmm-status-type-list-form" action="" method="get">
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                <?php
                $this->status_list_table->search_box(__('Find', $this->plugin_name), 'scsmm-status-type-find');
                $this->status_list_table->display();
                ?>
            </form>
        </div>
    </div>
</div>
<?php if ($this->status_list_table->show_edit()) { ?>
 
    <div class="wrap">
    <h2 class="wp-heading-inline"><?php _e("Membership Type List", $this->plugin_name); ?></h2>
        <form action="" name="scsmm-status-type" method="post">
            <input type="hidden" name="id" value="<?= $this->status_list_table->get_active_type('id')?>" />
            <input type="hidden" name="action" value="save_status" />
            <input type="hidden" name="_wpnonce" value="<?= $nonce ?>" />
            <table class="wp-list-table widefat">
                <thead>
                    <tr>
                        <th scope="col" class="manage-column column-name">
                            <?= esc_html__("Name*", $this->plugin_name) ?>
                        </th>
                        <th scope="col" class="manage-column column-description">
                            <?= esc_html__("Workflow Order", $this->plugin_name) ?>
                        </th>
                        <th scope="col" class="manage-column column-cost">
                            <?= esc_html__("Workflow Action", $this->plugin_name) ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
    
                    <tr>
                        <td class="name column-name">
                            <input style="width: 100%" type="text" name="name" id="name" required value="<?= $this->status_list_table->get_active_type('name');?>">
                        </td>
                        <td class="description column-description">
                            <input style="width: 100%" type="text" name="description" id="description" value="<?= $this->status_list_table->get_active_type('description') ?>">
                        </td>
                        <td class="cost column-cost">
                            <input style="width: 100%" type="text" name="cost" id="cost" value="<?= $this->status_list_table->get_active_type('cost'); ?>">
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php submit_button( __("Save", $this->plugin_name), 'primary', 'save_status', false, null ) ?>
            <a class='button button-secondary' href="<?= $cancel_status_link ?>"><?= esc_html__("Cancel", $this->plugin_name) ?></a>
        </form>
    </div>
 <?php } ?> 