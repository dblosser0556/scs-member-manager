<?php
$nonce = wp_create_nonce( 'edit_type_nonce' );

$query_args_edit_member = array(
    'page'        =>  wp_unslash($_REQUEST['page']),
    'action'    => 'add_relationship_type',
    'tab'       => 'relationship_options',
    '_wpnonce'    => wp_create_nonce('edit_type_nonce'),
);

$new_relationship_type_link = esc_url(add_query_arg($query_args_edit_member, admin_url('admin.php')));

$query_args_edit_submit = array(
    'page'        =>  wp_unslash($_REQUEST['page']),
    'action'    => 'save_relationship_type',
    'tab'       => 'relationship_options',
    '_wpnonce'    => wp_create_nonce('edit_type_nonce'),
);
$submit_relationship_type_link = esc_url(add_query_arg($query_args_edit_submit, admin_url('admin.php')));

$query_args_edit_cancel = array(
    'page'        =>  wp_unslash($_REQUEST['page']),
    'action'    => 'cancel_relationship_type',
    'tab'       => 'relationship_options',
    '_wpnonce'    => wp_create_nonce('edit_type_nonce'),
);
$cancel_relationship_type_link = esc_url(add_query_arg($query_args_edit_cancel, admin_url('admin.php')));
?>

<div class="wrap">

    <h1 class="wp-heading-inline"><?= esc_html__("Relationship Type List", $this->plugin_name); ?></h1>
    <a href="<?= $new_relationship_type_link ?>" class="page-title-action"><?= esc_html__("Add New", $this->plugin_name) ?> </a>
    <hr class="wp-header-end">
    <?php if ($this->relationship_type_list_table->get_message() !== '')  { ?>
    <div id="message" class="notice notice-success is-dismissible inline">
        <p><?= $this->relationship_type_list_table->get_message() ?></p>
        <button type="button" class="notice-dismiss"></button>
    </div>
    <?php 
    } ?>
    <?php if ($this->relationship_type_list_table->get_error_message() !== '') { ?>
    <div class="notice notice-error  is-dismissible inline">
        <p><?= $this->relationship_type_list_table->get_error_message() ?></p>
        <button type="button" class="notice-dismiss"></button>
    </div>
      <?php 
    } ?>
    <div id="scsmm-relationship-type-list">
        <div id="scsmm-post-body">
            <form id="scsmm-relationship-type-list-form" action="" method="get">
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                <input type="hidden" name="tab" value="<?php echo $_REQUEST['tab'] ?>" />
                <?php
                $this->relationship_type_list_table->search_box(__('Find', $this->plugin_name), 'scsmm-relationship-type-find');
                $this->relationship_type_list_table->display();
                ?>
            </form>
        </div>
    </div>
</div>
<?php if ($this->relationship_type_list_table->show_edit()) { ?>
 
    <div class="wrap">
    <h2 class="wp-heading-inline"><?php _e("relationship Type List", $this->plugin_name); ?></h2>
        <form action="" name="scsmm-relationship-type" method="post">
            <input type="hidden" name="id" value="<?= $this->relationship_type_list_table->get_active_type('id')?>" />
            <input type="hidden" name="action" value="save_relationship_type" />
            <input type="hidden" name="_wpnonce" value="<?= $nonce ?>" />
            <table class="wp-list-table widefat">
                <thead>
                    <tr>
                        <th scope="col" class="manage-column column-name">
                            <?= esc_html__("Name*", $this->plugin_name) ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
    
                    <tr>
                        <td class="name column-name">
                            <input style="width: 100%" type="text" name="name" id="name" required value="<?= $this->relationship_type_list_table->get_active_type('name');?>">
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php submit_button( __("Save", $this->plugin_name), 'primary', 'save_relationship_type', false, null ) ?>
            <a class='button button-secondary' href="<?= $cancel_relationship_type_link ?>"><?= esc_html__("Cancel", $this->plugin_name) ?></a>
        </form>
    </div>
 <?php } ?> 