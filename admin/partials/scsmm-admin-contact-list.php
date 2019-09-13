<?php
$nonce = wp_create_nonce( 'scs-contact-form-check-string' );

$query_args_edit_member = array(
    'page'        =>  wp_unslash($_REQUEST['page']),
    'action'    => 'add_contact',
    '_wpnonce'    => wp_create_nonce('scs-contact-form-check-string'),
);

$new_contact_link = esc_url(add_query_arg($query_args_edit_member, admin_url('admin.php')));

$query_args_edit_submit = array(
    'page'        =>  wp_unslash($_REQUEST['page']),
    'action'    => 'save_contact',
    '_wpnonce'    => wp_create_nonce('scs-contact-form-check-string'),
);
$submit_contact_link = esc_url(add_query_arg($query_args_edit_submit, admin_url('admin.php')));

$query_args_edit_cancel = array(
    'page'        =>  wp_unslash($_REQUEST['page']),
    'action'    => 'cancel_contact',
    '_wpnonce'    => wp_create_nonce('scs-contact-form-check-string'),
);
$cancel_contact_link = esc_url(add_query_arg($query_args_edit_cancel, admin_url('admin.php')));
?>

<div class="wrap">

    <h1 class="wp-heading-inline"><?= esc_html__("Contact List", $this->plugin_name); ?></h1>
    <a href="<?= $new_contact_link ?>" class="page-title-action"><?= esc_html__("Add New", $this->plugin_name) ?> </a>
    <hr class="wp-header-end">
    <?php if ($this->contact_list_table->get_message() !== '')  { ?>
    <div id="message" class="notice notice-success is-dismissible inline">
        <p><?= $this->contact_list_table->get_message() ?></p>
        <button type="button" class="notice-dismiss"></button>
    </div>
    <?php 
    } ?>
    <?php if ($this->contact_list_table->get_error_message() !== '') { ?>
    <div class="notice notice-error  is-dismissible inline">
        <p><?= $this->contact_list_table->get_error_message() ?></p>
        <button type="button" class="notice-dismiss"></button>
    </div>
      <?php 
    } ?>
    <div id="scsmm-contact-list">
        <div id="scsmm-post-body">
            <form id="scsmm-contact-list-form" action="" method="get">
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                <?php
                $this->contact_list_table->search_box(__('Find', $this->plugin_name), 'scsmm-contact-find');
                $this->contact_list_table->display();
                ?>
            </form>
        </div>
    </div>
</div>
<?php if ($this->contact_list_table->show_edit()) { ?>
 
    <div class="wrap">
    <h2 class="wp-heading-inline"><?php _e("Contact", $this->plugin_name); ?></h2>
        <form action="" name="scsmm-contact" method="post">
            <input type="hidden" name="id" value="<?= $this->contact_list_table->get_active_contact('id')?>" />
            <input type="hidden" name="action" value="save_contact" />
            <input type="hidden" name="_wpnonce" value="<?= $nonce ?>" />
            <table class="wp-list-table widefat">
                <thead>
                    <tr>
                        <th scope="col" class="manage-column column-last_name">
                            <?= esc_html__("Last name*", $this->plugin_name) ?>
                        </th>
                        <th scope="col" class="manage-column column-first_name">
                            <?= esc_html__("First name*", $this->plugin_name) ?>
                        </th>
                        <th scope="col" class="manage-column column-phone">
                            <?= esc_html__("Phone", $this->plugin_name) ?>
                        </th>
                        <th scope="col" class="manage-column column-email">
                            <?= esc_html__("Email", $this->plugin_name) ?>
                        </th>
                        <th scope="col" class="manage-column column-contact_date">
                            <?= esc_html__("Contact Date", $this->plugin_name) ?>
                        </th>
                        <th scope="col" class="manage-column column-comments">
                            <?= esc_html__("Comments", $this->plugin_name) ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
    
                    <tr>
                       
                        <td class="last_name column-last-name">
                            <input style="width: 100%" type="text" name="last_name" id="last_name" required value="<?= $this->contact_list_table->get_active_contact('last_name');?>">
                        </td>
                        <td class="first_name column-first-name">
                            <input style="width: 100%" type="text" name="first_name" id="first_name" required value="<?= $this->contact_list_table->get_active_contact('first_name');?>">
                        </td>
                        <td class="email column-email">
                            <input style="width: 100%" type="text" name="email" id="email" required value="<?= $this->contact_list_table->get_active_contact('email');?>">
                        </td>
                        <td class="phone column-phone">
                            <input style="width: 100%" type="text" name="phone" id="phone" value="<?= $this->contact_list_table->get_active_contact('phone');?>">
                        </td>
                        <td class="contact_date column-contact-date">
                            <input style="width: 100%" type="text" name="contact_date" id="contact_date" value="<?= $this->contact_list_table->get_active_contact('contact_date');?>">
                        </td>
                        <td class="comments column-comments">
                            <input style="width: 100%" type="text" name="comments" id="comments" value="<?= $this->contact_list_table->get_active_contact('comments');?>">
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php submit_button( __("Save", $this->plugin_name), 'primary', 'save_contact', false, null ) ?>
            <a class='button button-secondary' href="<?= $cancel_contact_link ?>"><?= esc_html__("Cancel", $this->plugin_name) ?></a>
        </form>
    </div>
 <?php } ?> 