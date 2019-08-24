<?php
$nonce = wp_create_nonce('edit_type_nonce');

$query_args_edit_member = array(
    'page'        =>  wp_unslash($_REQUEST['page']),
    'action'    => 'add_email',
    'tab'       => 'email_options',
    '_wpnonce'    => wp_create_nonce('edit_type_nonce'),
);

$new_email_link = esc_url(add_query_arg($query_args_edit_member, admin_url('admin.php')));

$query_args_edit_submit = array(
    'page'        =>  wp_unslash($_REQUEST['page']),
    'action'    => 'save_email',
    'tab'       => 'email_options',
    '_wpnonce'    => wp_create_nonce('edit_type_nonce'),
);
$submit_email_link = esc_url(add_query_arg($query_args_edit_submit, admin_url('admin.php')));

$query_args_edit_cancel = array(
    'page'        =>  wp_unslash($_REQUEST['page']),
    'action'    => 'cancel_email',
    'tab'       => 'email_options',
    '_wpnonce'    => wp_create_nonce('edit_type_nonce'),
);
$cancel_email_link = esc_url(add_query_arg($query_args_edit_cancel, admin_url('admin.php')));
?>

<div class="wrap">

    <h1 class="wp-heading-inline"><?php _e("Email Templates", $this->plugin_name); ?></h1>
    <a href="<?= $new_email_link ?>" class="page-title-action"><?= esc_html__("Add New", $this->plugin_name) ?> </a>
    <hr class="wp-header-end">
    <?php if ($this->email_template_list_table->get_message() !== '') { ?>
    <div id="message" class="notice notice-success is-dismissible inline">
        <p><?= $this->email_template_list_table->get_message() ?></p>
        <button type="button" class="notice-dismiss"></button>
    </div>
    <?php
    } ?>
    <?php if ($this->email_template_list_table->get_error_message() !== '') { ?>
    <div class="notice notice-error  is-dismissible inline">
        <p><?= $this->email_template_list_table->get_error_message() ?></p>
        <button type="button" class="notice-dismiss"></button>
    </div>
    <?php
    } ?>
    <div id="scsmm-email-type-list">
        <div id="scsmm-post-body">
            <form id="scsmm-email-type-list-form" action="" method="get">
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                <input type="hidden" name="tab" value="<?php echo $_REQUEST['tab'] ?>" />
                <?php
                $this->email_template_list_table->search_box(__('Find', $this->plugin_name), 'scsmm-email-type-find');
                $this->email_template_list_table->display();
                ?>
            </form>
        </div>
    </div>
</div>
<?php if ($this->email_template_list_table->show_edit()) { ?>

<div class="wrap">
    <h2 class="wp-heading-inline"><?php _e("Template", $this->plugin_name); ?></h2>
    <form action="" name="scsmm-email-type" method="post">
        <input type="hidden" name="id" value="<?= $this->email_template_list_table->get_active_type('id') ?>" />
        <input type="hidden" name="action" value="save_email" />
        <input type="hidden" name="_wpnonce" value="<?= $nonce ?>" />
        <table class="wp-list-table widefat">
            <thead>
                <tr>
                    <th scope="col" class="manage-column column-name">
                        <?= esc_html__("Name", $this->plugin_name) ?>
                    </th>
                    <th scope="col" class="manage-column column-recipient_desc">
                        <?= esc_html__("Recipient Description", $this->plugin_name) ?>
                    </th>
                    <th scope="col" class="manage-column column-sender_desc">
                        <?= esc_html__("Sender Description", $this->plugin_name) ?>
                    </th>
                    <th scope="col" class="manage-column column-sender_email">
                        <?= esc_html__("Sender Email*", $this->plugin_name) ?>
                    </th>
                </tr>
            </thead>
            <tbody>

                <tr>
                    <td class="name column-sender_email">
                        <input style="width: 100%" type="text" name="name" id="sender_email" required value="<?= $this->email_template_list_table->get_active_type('name'); ?>">
                    </td>
                    <td class="recipient_desc column-recipient_desc">
                        <input style="width: 100%" type="text" name="recipient_desc" id="recipient_desc" required value="<?= $this->email_template_list_table->get_active_type('recipient_desc') ?>">
                    </td>
                    <td class="sender_desc column-sender_desc">
                        <input style="width: 100%" type="text" name="sender_desc" id="sender_desc" required value="<?= $this->email_template_list_table->get_active_type('sender_desc'); ?>">
                    </td>
                    <td class="sender_email column-sender_email">
                        <input style="width: 100%" type="text" name="sender_email" id="sender_email" required value="<?= $this->email_template_list_table->get_active_type('sender_email'); ?>">
                    </td>

                </tr>
                <tr>
                    <td colspan="4">
                        <?php
                            $settings = array('editor_height' => '250');
                            $emailtext = $this->email_template_list_table->get_active_type('email_text');
                            wp_editor($emailtext, 'emailtext', $settings);
                            ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php submit_button(__("Save", $this->plugin_name), 'primary', 'save_email', false, null) ?>
        <a class='button button-secondary' href="<?= $cancel_email_link ?>"><?= esc_html__("Cancel", $this->plugin_name) ?></a>
    </form>
</div>
<?php } ?>