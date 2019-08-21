<?php
global $wpdb;
$table_name = $this->getTable('membership_types');


$query_args_edit_member = array(
    'page'        =>  wp_unslash($_REQUEST['page']),
    'action'    => 'new_membership_type',
    'tab'       => 'types_options',
    '_wpnonce'    => wp_create_nonce('new_membership_type_nonce'),
);

$new_membership_type_link = esc_url(add_query_arg($query_args_edit_member, admin_url('admin.php')));

if (isset($_GET['membership_typeid'])) {
    $typeID = (int) $_GET['membership_typeid'];
}



if (isset($_POST['delete']) && isset($_POST['id']) && (int) $_POST['id'] > 0) { // delete
    $wpdb->delete($table_name, array('id' => (int) $_POST['id']));
    remove_type($_POST['slug']);
}

if (isset($_POST['submit'])) {
    if (isset($_POST['id']) && (int) $_POST['id'] > 0) { // edit
        $wpdb->update(
            $table_name,
            array(
                'name' => $_POST['name']
            ),
            array('id' => (int) $_POST['id']),
            array(
                '%s'
            )
        );
        $typeID = (int) $_POST['id'];
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
    $type->description = '';
    $type->cost = '';
}

if (isset($_GET['action'])  && $_GET['action']  == 'new_membership_type') {
    $show = true;
} else {
    $show = false;
}
?>
<div class="wrap">

    <h2 class="wp-heading-inline"><?php _e('WP Membership Type List', $this->plugin_name); ?></h2>
    <a href="<?= $new_membership_type_link ?>" class="page-title-action">Add New</a>
    <hr class="wp-header-end">
    <div id="scsmm-membership-type-list">
        <div id="scsmm-post-body">
            <form id="scsmm-membership-type-list-form" action="" method="get">
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                <?php
                $this->membership_type_list_table->search_box(__('Find', $this->plugin_name), 'scsmm-membership-type-find');
                $this->membership_type_list_table->display();
                ?>
            </form>
        </div>
    </div>
</div>
<?php if ($show) { ?>
<div class="wrap">
    <form action="" name="scsmm-membership-type" method="post">
        <input type="hidden" name="id" value="<?= $type->id ?>" />
        <table class="wp-list-table widefat">
            <thead>
                <tr>
                    <th scope="col" class="manage-column column-name">
                        <?= esc_html__("Name", $this->plugin_name) ?>
                    </th>
                    <th scope="col" class="manage-column column-description">
                        <?= esc_html__("Description", $this->plugin_name) ?>
                    </th>
                    <th scope="col" class="manage-column column-cost">
                        <?= esc_html__("Cost", $this->plugin_name) ?>
                    </th>
                </tr>
            </thead>
            <tbody>

                <tr>
                    <td class="name column-name">
                        <input style="width: 100%" type="text" name="name" id="name" value="<?php $type->name; ?>">
                    </td>
                    <td class="description column-description">
                        <input style="width: 100%" type="text" name="description" id="description" value="<?php $type->description; ?>">
                    </td>
                    <td class="cost column-cost">
                        <input style="width: 100%" type="text" name="cost" id="cost" value="<?php $type->cost; ?>">
                    </td>
                </tr>
            </tbody>
        </table>
        <?php submit_button("Add or Update", 'primary', 'submit', false, null); ?>
    </form>
</div>
<?php } ?>