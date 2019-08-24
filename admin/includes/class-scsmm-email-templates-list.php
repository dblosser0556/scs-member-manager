<?php





class Email_Template_List_Table extends SCSWP_List_Table
{
    protected $can_show_edit;

    protected $active_type;

    protected $message;

    protected $error_message;

    protected $plugin_name;

    public function __construct($plugin_name)
    {


        $this->plugin_name = $plugin_name;



        parent::__construct(array(
            'plural'    =>    'emails',    // Plural value used for labels and the objects being listed.
            'singular'    =>    'email',        // Singular label for an object being listed, e.g. 'post'.
            'ajax'        =>    false,        // If true, the parent class will call the _js_vars() method in the footer		
            'screen'    => 'email_template'         //A screen id name, in order to have many lists on one page, must supply unique screen
        ));

        $this->can_show_edit = false;
        $this->message = '';
        $this->error_message = '';

        add_action('admin_head', array($this, 'admin_header'));
    }

    /*
    * Setup the column widths
    * Since we have multiple screen columns with the same name need to be the same
    * width as each of these styles will be added to the page.
    */
    public function admin_header()
    {
        // ensure we are on the correct page
        $page = (isset($_GET['page'])) ? esc_attr($_GET['page']) : false;
        $tab = (isset($_GET['tab'])) ? esc_attr($_GET['tab']) : false;
        if ($this->plugin_name . '-settings' != $page && $tab != 'email_options')
            return;

        // set up column widths
        echo '<style type="text/css">';
        echo '.wp-list-table .column-cb { width: 5%; }';
        echo '.wp-list-table .column-id { width: 5%; }';
        echo '.wp-list-table .column-name { width: 20%; }';
        echo '.wp-list-table .column-recipient_desc { width: 25%; }';
        echo '.wp-list-table .column-sender_desc { width: 25%; }';
        echo '.wp-list-table .column-sender_email { width: 20%; }';
        echo '</style>';
    }

    /**
     * Supply the list of the columns to be displayed.  Should be the same name as the columns from the 
     * sql being displayed.
     *
     * @param object 
     * @return string array of column names and their display name.
     */
    public function get_columns()
    {
        $table_columns = array(
            'cb'                => '<input type = "checkbox" />',
            'id'              => __('id', $this->plugin_name),
            'name'              => __('Name', $this->plugin_name),
            'recipient_desc'   => __('Recipient Description', $this->plugin_name),
            'sender_desc'  => __('Sender Description', $this->plugin_name),
            'sender_email'  => __('Sender Email', $this->plugin_name)
        );
        return $table_columns;
    }

    /**
     * List of the bulk actions.
     *
     * @param object 
     * @return string array of bulk actions and their display name.
     */
    public function get_bulk_actions()
    {

        /*
    * on hitting apply in bulk actions the url params are set as
    * ?action=bulk-download&paged=1&action2=-1
    * 
    * action and action2 are set based on the triggers above and below the table
    * need to pass the tab for the list		 		    
    */
        $actions = array(
            'delete'        => 'Delete Selected'
        );
        return $actions;
    }

    /**
     * Defines what is displayed if the are no items to display
     *
     * @return void
     */
    public function no_items()
    {
        _e('No Email Templates Available', $this->plugin_name);
    }


    /**
     * Required to be called before the list is displayed.
     * Used to create the list of columns, and fetch and filter the data.
     *
     * @return void
     */
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
        $items_per_page = $this->get_items_per_page('email_per_page');
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
            . "<input type='checkbox' name='emails[]' id='email_{$item['id']}' value='{$item['id']}' />";
        return sprintf($checkbox);
    }

    /**
     * Sets up the actions available for the name column
     *
     * @param [type] $item
     * @return string
     */
    protected function column_name($item)
    {

        $admin_page_url =  admin_url('admin.php');


        // row actions to edit item.
        $query_args_edit_member = array(
            'page'        =>  wp_unslash($_REQUEST['page']),
            'action'    => 'edit_email',
            'tab'       => 'email_options',
            'id'        => absint($item['id']),
            '_wpnonce'    => wp_create_nonce('edit_type_nonce'),
        );
        $edit_member_link = esc_url(add_query_arg($query_args_edit_member, $admin_page_url));
        $actions['edit_email'] = '<a href="' . $edit_member_link . '">' . __('Edit', $this->plugin_name) . '</a>';

        // row actions to edit item.
        $query_args_delete_member = array(
            'page'        =>  wp_unslash($_REQUEST['page']),
            'action'    => 'delete_email',
            'tab'       => 'email_options',
            'id'        => absint($item['id']),
            '_wpnonce'    => wp_create_nonce('edit_type_nonce'),
        );
        $delete_member_link = esc_url(add_query_arg($query_args_delete_member, $admin_page_url));

        $actions['delete_email'] = '<a href="' . $delete_member_link . '">' . __('Delete', $this->plugin_name) . '</a>';

        $row_value = '<strong>' . $item['name'] . '</strong>';
        return $row_value . $this->row_actions($actions);
    }

    /**
     * Determines the displayed value for each row and column of the list
     * if there is not a specific column function provided.
     *
     * @param array $item
     * @param string $column_name
     * @return string
     */
    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'id':
            case 'recipient_desc':
            case 'sender_desc':
            case 'sender_email':
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
            'recipient_desc' => 'recipient_desc'
        );
        return $sortable_columns;
    }

    private function getTable($table)
    {
        global $wpdb;
        return "{$wpdb->prefix}scsmm_{$table}";
    }

    /*
     * fetch the list of items from the database
    */
    public function fetch_table_data()
    {
        global $wpdb;

        $orderby = (isset($_GET['orderby'])) ? esc_sql($_GET['orderby']) : 'name';
        $order = (isset($_GET['order'])) ? esc_sql($_GET['order']) : 'ASC';


        $table_name = $this->getTable('email_templates');
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


    public function handle_table_actions()
    {

        /*
         * Note: Table bulk_actions can be identified by checking $_REQUEST['action'] and $_REQUEST['action2']
         * action - is set if checkbox from top-most select-all is set, otherwise returns -1
         * action2 - is set if checkbox the bottom-most select-all checkbox is set, otherwise returns -1
         */

        // check for individual row actions
        $the_table_action = $this->current_action();

        // trap the edit action from the name column
        if ('edit_email' === $the_table_action) {
            $nonce = wp_unslash($_REQUEST['_wpnonce']);
            // verify the nonce.
            if (!wp_verify_nonce($nonce, 'edit_type_nonce')) {
                $this->invalid_nonce_redirect();
            } else {
                $this->page_edit_email($_REQUEST['id']);
            }
        }

        // trap the new button action
        if ('add_email' === $the_table_action) {
            $nonce = wp_unslash($_REQUEST['_wpnonce']);
            // verify the nonce.
            if (!wp_verify_nonce($nonce, 'edit_type_nonce')) {
                $this->invalid_nonce_redirect();
            } else {
                $this->page_add_email();
            }
        }
        // trap the save button from the edit form.
        if ('save_email' === $the_table_action) {
            $nonce = wp_unslash($_REQUEST['_wpnonce']);
            // verify the nonce.
            if (!wp_verify_nonce($nonce, 'edit_type_nonce')) {
                $this->invalid_nonce_redirect();
            } else {
                $this->page_save_email();
            }
        }

        // trap the delete action from the name column
        if ('delete_email' === $the_table_action) {
            $nonce = wp_unslash($_REQUEST['_wpnonce']);
            // verify the nonce.
            if (!wp_verify_nonce($nonce, 'edit_type_nonce')) {
                $this->invalid_nonce_redirect();
            } else {
                $this->page_delete_email($_REQUEST['id']);
            }
        }

        // trap the cancel button on the edit form to hide the form.
        if ('cancel_email' === $the_table_action) {
            $nonce = wp_unslash($_REQUEST['_wpnonce']);
            // verify the nonce.
            if (!wp_verify_nonce($nonce, 'edit_type_nonce')) {
                $this->invalid_nonce_redirect();
            } else {
                $this->page_cancel_email();
            }
        }

        // trap the delete action from the bulk actions list.
        if ((isset($_REQUEST['action']) && $_REQUEST['action'] === 'delete') || (isset($_REQUEST['action2']) && $_REQUEST['action2'] === 'delete')) {
            $nonce = wp_unslash($_REQUEST['_wpnonce']);
            if (!wp_verify_nonce($nonce, 'bulk-emails')) { // verify the nonce.
                $this->invalid_nonce_redirect();
            } else {
                $this->page_delete_emails($_REQUEST['emails']);
            }
        }
    }

    /**
     * Handles the edit action
     * Set the item to be displayed and can show edit flag
     * 
     * @param string $item id
     * @return void
     */
    public function page_edit_email($id)
    {
        global $wpdb;
        $table_name = $this->getTable('email_templates');
        $this->active_type = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $id");
        $this->can_show_edit = true;
    }

    /**
     * Handles the add action
     *
     * @return void
     */
    public function page_add_email()
    {

        // create an empty array for input
        $type = new stdClass();
        $type->id = 0;
        $type->name = '';
        $type->recipient_desc = '';
        $type->sender_desc  = '';
        $type->sender_email = '';
        $type->email_text  = '';

        $this->active_type = $type;
        $this->can_show_edit = true;
    }

    /**
     * Handles the save action from the edit form
     *
     * @return void
     */
    public function page_save_email()
    {
        global $wpdb;
        $table_name = $this->getTable('email_templates');

        if (!isset($_POST['name'])) {
            $this->message = __('You must have a name', $this->plugin_name);
            return;
        }

        if (isset($_POST['id']) && (int) $_POST['id'] > 0) { // edit
            $wpdb->update(
                $table_name,
                array(
                    'name'              => sanitize_text_field($_POST['name']),
                    'recipient_desc'    => sanitize_text_field($_POST['recipient_desc']),
                    'sender_desc'       => sanitize_text_field($_POST['sender_desc']),
                    'sender_email'      => sanitize_email($_POST['sender_email']),
                    'email_text'        => wp_kses_post($_POST['emailtext'])
                ),
                array('id' => (int) $_POST['id']),
                array(
                    '%s',
                    '%s',
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
                    'name'              => sanitize_text_field($_POST['name']),
                    'recipient_desc'    => sanitize_text_field($_POST['recipient_desc']),
                    'sender_desc'       => sanitize_text_field($_POST['sender_desc']),
                    'sender_email'      => sanitize_email($_POST['sender_email']),
                    'email_text'        => wp_kses_post($_POST['emailtext'])
                ),
                array(
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s'
                )
            );

            $this->message = __('Successfully created!', $this->plugin_name);
        }
    }
    /**
     * Handles the cancel action from the edit form, closes the form
     *
     * @return void
     */
    public function page_cancel_email()
    {
        $this->can_show_edit = false;
    }

    /**
     * Handles the delete action from the name column and
     * is called from the bulk action for each row selected.
     *
     * @param string $id
     * @return void
     */
    public function page_delete_email($id)
    {
        global $wpdb;
        // todo: add edit before delete
        // check to see if any members have this email
        //$table_name = $this->getTable('member_list');
        //$results =  $wpdb->get_results("SELECT * FROM $table_name WHERE email_id = $id", ARRAY_A);
        //if (count($results) > 0) {
        //    $this->error_message = __('There are memberships at this email.  The email cannot be deleted.');
        //    return;
       // }
        // delete the email 
        $table_name = $this->getTable('email_templates');
        $wpdb->delete($table_name, array('id' => (int) $id));
        $this->message = __('Successfully deleted!', $this->plugin_name);
    }

    /**
     * Handles the delete action from the bulk action
     *
     * @param string $ids
     * @return void
     */
    public function page_delete_emails($ids)
    {
        foreach ($ids as $id) {
            $this->page_delete_email($id);
        }
    }

    /**
     * Return the can show edit flag
     *
     * @return boolean
     */
    public function show_edit()
    {
        return $this->can_show_edit;
    }

    /**
     * Return the values from the active item 
     *
     * @param string $attr
     * @return string
     */
    public function get_active_type($attr)
    {
        switch ($attr) {
            case 'id':
                return $this->active_type->id;
                break;
            case 'name':
                return $this->active_type->name;
                break;
            case 'recipient_desc':
                return $this->active_type->recipient_desc;
                break;
            case 'sender_desc':
                return $this->active_type->sender_desc;
                break;
            case 'sender_email':
                return $this->active_type->sender_email;
                break;
            case 'email_text':
                return $this->active_type->email_text;
                break;
            default:
                return '';
                break;
        }
    }

    /**
     * Return a success message
     *
     * @return string
     */
    public function get_message()
    {
        return $this->message;
    }

    /**
     * Returns the error message
     *
     * @return string
     */
    public function get_error_message()
    {
        return $this->error_message;
    }


    /**
     * Stop execution and exit
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
