<?php





class Membership_Type_List_Table extends SCSWP_List_Table
{
    protected $can_show_edit;

    protected $active_type;

    protected $message;

    protected $error_message;

    protected $plugin_name;

    public function __construct($plugin_name)
    {


        $this->plugin_name = $plugin_name;
        //$this->data = $data;


        parent::__construct(array(
            'plural'    =>    'membership_types',    // Plural value used for labels and the objects being listed.
            'singular'    =>    'membership_type',        // Singular label for an object being listed, e.g. 'post'.
            'ajax'        =>    false,        // If true, the parent class will call the _js_vars() method in the footer	
            'screen'    => 'memberships'	
        ));

        $this->can_show_edit = false;
        $this->message = '';
        $this->error_message = '';

        add_action('admin_head', array($this, 'admin_header'));
    }

    public function admin_header()
    {
        // ensure we are on the correct page
        $page = (isset($_GET['page'])) ? esc_attr($_GET['page']) : false;
        $tab = (isset($_GET['tab'])) ? esc_attr($_GET['tab']) : false;
        if ($this->plugin_name . '-settings' != $page  && $tab != 'membership_options')
            return;

        // set up column widths
        echo '<style type="text/css">';
        echo '.wp-list-table .column-cb { width: 5%; }';
        echo '.wp-list-table .column-name { width: 20%; }';
        echo '.wp-list-table .column-description { width: 60%; }';
        echo '.wp-list-table .column-cost { width: 15%; }';
        echo '</style>';
    }

    public function get_columns()
    {
        $table_columns = array(
            'cb'            => '<input type = "checkbox" />',
            'name'          => __('Name', 'scsmm'),
            'description'   => __('Description', 'scsmm'),
            'cost'          => __('Cost', 'scsmm')
        );
        return $table_columns;
    }


    public function no_items()
    {
        _e('No Membership Types Available', 'scsmm');
    }

    public function prepare_items()
    {

        // check if a search was performed.
        $item_search_key = isset($_REQUEST['s']) ? wp_unslash(trim($_REQUEST['s'])) : '';

        // set up the columns for display
        $this->_column_headers = $this->get_column_info();

        // check and process any actions such as bulk actions.
        $this->handle_table_actions();

        // get the data from the database
        $table_data = $this->fetch_table_data();
        if ($item_search_key) {
            $table_data = $this->filter_table_data($table_data, $item_search_key);
        }

        // set up pagination
        //$items_per_page = $this->get_items_per_page('membership_types_per_page');
        $items_per_page = 5;
        $table_page = $this->get_pagenum();

        $this->items = array_slice($table_data, (($table_page - 1) * $items_per_page), $items_per_page);


        // set the pagination arguments		
        $total_items = count($table_data);
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page'    => $items_per_page,
            'total_pages' => ceil($total_items / $items_per_page)
        ));
    }

    /**
     * Get value for checkbox column.
     *
     * @param object $item  A row's data.
     * @return string Text to be placed inside the column <td>.
     */
    protected function column_cb($item)
    {
        $checkbox =  '<label class="screen-reader-text" for="type_' . $item['id'] . '">'
            . sprintf(__('Select %s'), $item['id']) . '</label>'
            . "<input type='checkbox' name='membership_types[]' id='membership_type_{$item['id']}' value='{$item['id']}' />";
        return sprintf($checkbox);
    }

    protected function column_name($item)
    {

        $admin_page_url =  admin_url('admin.php');


        // row actions to edit item.
        $query_args_edit_member = array(
            'page'        =>  wp_unslash($_REQUEST['page']),
            'action'    => 'edit_membership_type',
            'tab'       => 'membership_options',
            'id'        => absint($item['id']),
            '_wpnonce'    => wp_create_nonce('edit_type_nonce'),
        );
        $edit_member_link = esc_url(add_query_arg($query_args_edit_member, $admin_page_url));
        $actions['edit_membership_type'] = '<a href="' . $edit_member_link . '">' . __('Edit', $this->plugin_name) . '</a>';

        // row actions to edit item.
        $query_args_delete_member = array(
            'page'        =>  wp_unslash($_REQUEST['page']),
            'action'    => 'delete_membership_type',
            'tab'       => 'membership_options',
            'id'        => absint($item['id']),
            '_wpnonce'    => wp_create_nonce('edit_type_nonce'),
        );
        $delete_member_link = esc_url(add_query_arg($query_args_delete_member, $admin_page_url));

        $actions['delete_membership_type'] = '<a href="' . $delete_member_link . '">' . __('Delete', $this->plugin_name) . '</a>';

        $row_value = '<strong>' . $item['name'] . '</strong>';
        return $row_value . $this->row_actions($actions);
    }


    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'description':
            case 'cost':
                return $item[$column_name];
            default:
                return $item[$column_name];
        }
    }

    /*
     * set up the list of sortable columns
    */
    protected function get_sortable_columns()
    {
        /*
         * actual sorting still needs to be done by prepare_items.
         * specify which columns should have the sort icon.	
         */
        $sortable_columns = array(
            'name' => 'name',
            'cost' => 'cost'
        );
        return $sortable_columns;
    }

    private function getTable($table)
    {
        global $wpdb;
        return "{$wpdb->prefix}scsmm_{$table}";
    }

    /*
     * set up the list of sortable columns
    */
    public function fetch_table_data()
    {
        global $wpdb;

        $orderby = (isset($_GET['orderby'])) ? esc_sql($_GET['orderby']) : 'name';
        $order = (isset($_GET['order'])) ? esc_sql($_GET['order']) : 'ASC';


        $table_name = $this->getTable('membership_types');
        $results =  $wpdb->get_results("SELECT * FROM $table_name ORDER BY $orderby $order", ARRAY_A);
        return $results;
    }

    // filter the table data based on the search key
    public function filter_table_data($table_data, $search_key)
    {
        $filtered_table_data = array_values(array_filter($table_data, function ($row) use ($search_key) {
            foreach ($row as $row_val) {
                if (stripos($row_val, $search_key) !== false) {
                    return true;
                }
            }
        }));
        return $filtered_table_data;
    }

    // Returns an associative array containing the bulk action.
    /*public function get_bulk_actions()
    {
        /*
	 * on hitting apply in bulk actions the url params are set as
	 * ?action=bulk-download&paged=1&action2=-1
	 * 
	 * action and action2 are set based on the triggers above and below the table		 		    
	 
        $actions = array(
            'delete_membership_types'        => 'Delete Member Data',
        );
        return $actions;
    } */


    public function handle_table_actions()
    {

        /*
         * Note: Table bulk_actions can be identified by checking $_REQUEST['action'] and $_REQUEST['action2']
         * action - is set if checkbox from top-most select-all is set, otherwise returns -1
         * action2 - is set if checkbox the bottom-most select-all checkbox is set, otherwise returns -1
         */

        // check for individual row actions
        $the_table_action = $this->current_action();


        if ('edit_membership_type' === $the_table_action) {
            $nonce = wp_unslash($_REQUEST['_wpnonce']);
            // verify the nonce.
            if (!wp_verify_nonce($nonce, 'edit_type_nonce')) {
                $this->invalid_nonce_redirect();
            } else {
                $this->page_edit_membership_type($_REQUEST['id']);
            }
        }
        // handle the new button
        if ('add_membership_type' === $the_table_action) {
            $nonce = wp_unslash($_REQUEST['_wpnonce']);
            // verify the nonce.
            if (!wp_verify_nonce($nonce, 'edit_type_nonce')) {
                $this->invalid_nonce_redirect();
            } else {
                $this->page_add_membership_type();
            }
        }
        if ('save_membership_type' === $the_table_action) {
            $nonce = wp_unslash($_REQUEST['_wpnonce']);
            // verify the nonce.
            if (!wp_verify_nonce($nonce, 'edit_type_nonce')) {
                $this->invalid_nonce_redirect();
            } else {
                $this->page_save_membership_type();
            }
        }

        if ('delete_membership_type' === $the_table_action) {
            $nonce = wp_unslash($_REQUEST['_wpnonce']);
            // verify the nonce.
            if (!wp_verify_nonce($nonce, 'edit_type_nonce')) {
                $this->invalid_nonce_redirect();
            } else {
                $this->page_delete_membership_type($_REQUEST['id']);
            }
        }

        if ('cancel_membership_type' === $the_table_action) {
            $nonce = wp_unslash($_REQUEST['_wpnonce']);
            // verify the nonce.
            if (!wp_verify_nonce($nonce, 'edit_type_nonce')) {
                $this->invalid_nonce_redirect();
            } else {
                $this->page_cancel_membership_type();
            }
        }

        if ((isset($_REQUEST['action']) && $_REQUEST['action'] === 'delete_membership_types') || (isset($_REQUEST['action2']) && $_REQUEST['action2'] === 'delete_membership_types')
        ) {
            $nonce = wp_unslash($_REQUEST['_wpnonce']);
            if (!wp_verify_nonce($nonce, 'bulk-membership_types')) { // verify the nonce.
                $this->invalid_nonce_redirect();
            } else {
                $this->page_delete_membership_types($_REQUEST['membership_types']);
            }
            /*todo - handle delete action 
        */
        }
    }

    /**
     * View/Edit a member detail data.
     *
     * @since   1.0.0
     * 
     * @param int $user_id  user's ID	 
     */
    public function page_edit_membership_type($type)
    {
        global $wpdb;
        $table_name = $this->getTable('membership_types');
        $this->active_type = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $type");
        $this->can_show_edit = true;
    }
    public function page_add_membership_type()
    {
        $this->can_show_edit = true;

        // create an empty class for input
        $type = new stdClass();
        $type->id = 0;
        $type->name = '';
        $type->description = '';
        $type->cost = '';
        $this->active_type = $type;
    }

    public function page_save_membership_type()
    {
        global $wpdb;
        $table_name = $this->getTable('membership_types');

        if (!isset($_POST['name'])) {
            $this->message = __('You must have a name', $this->plugin_name);
            return;
        }

        if (isset($_POST['id']) && (int) $_POST['id'] > 0) { // edit
            $wpdb->update(
                $table_name,
                array(
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'cost'          => $_POST['cost']
                ),
                array('id' => (int) $_POST['id']),
                array(
                    '%s',
                    '%s',
                    '%s'
                )
            );

            $this->message = __('Successfully changed!', $this->plugin_name);
        } else { // create
            $wpdb->insert(
                $table_name,
                array(
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'cost'          => $_POST['cost']
                ),
                array(
                    '%s',
                    '%s',
                    '%s'
                )
            );

            $this->message = __('Successfully created!', $this->plugin_name);

        }
    }

    public function page_cancel_membership_type()
    {
        $this->can_show_edit = false;
    }

    public function page_delete_membership_type($type) {
        global $wpdb;

        $table_name = $this->getTable('membership_types');
        $wpdb->delete($table_name, array('id' => (int)$type));
        $this->message = __('Successfully deleted!', $this->plugin_name);
    }
    public function page_delete_membership_types($types)
    {

        //todo
        foreach($types as $type) {
            $this->page_delete_membership_type($type);
        }
    }

    public function show_edit()
    {
        return $this->can_show_edit;
    }

    public function get_active_type($attr)
    {
        switch ($attr) {
            case 'id':
                return $this->active_type->id;
                break;
            case 'name':
                return $this->active_type->name;
                break;
            case 'description':
                return $this->active_type->description;
                break;
            case 'cost':
                return $this->active_type->cost;
                break;
        }
    }
    /**
     * provide message
     *
     * @since    1.0.0
     * 
     * @return message
     */

    public function get_message()
    {
        return $this->message;
    }

    /**
     * provide message
     *
     * @since    1.0.0
     * 
     * @return message
     */

    public function get_error_message()
    {
        return $this->error_message;
    }


    /**
     * Stop execution and exit
     *
     * @since    1.0.0
     * 
     * @return void
     */
    public function graceful_exit()
    {
        exit;
    }

    /**
     * Die when the nonce check fails.
     *
     * @since    1.0.0
     * 
     * @return void
     */
    public function invalid_nonce_redirect()
    {
        wp_die(
            __('Invalid Nonce', $this->plugin_name),
            __('Error', $this->plugin_name),
            array(
                'response'     => 403,
                'back_link' =>  esc_url(add_query_arg(array('page' => wp_unslash($_REQUEST['page'])), admin_url('admin.php'))),
            )
        );
    }
}
