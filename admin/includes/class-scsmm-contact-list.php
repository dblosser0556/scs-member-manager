<?php





class Contact_List_Table extends SCSWP_List_Table
{
    protected $can_show_edit;

    protected $active_contact;

    protected $message;

    protected $error_message;

    protected $current_url;

    protected $plugin_name;

    public function __construct($plugin_name)
    {


        $this->plugin_name = $plugin_name;
        //$this->data = $data;


        parent::__construct(array(
            'plural'      =>    'contacts',    // Plural value used for labels and the objects being listed.
            'singular'    =>    'contact',        // Singular label for an object being listed, e.g. 'post'.
            'ajax'        =>    false,        // If true, the parent class will call the _js_vars() method in the footer	
            'screen'      => 'contacts'
        ));

        $this->can_show_edit = false;
        $this->message = '';
        $this->error_message = '';
        $this->current_url = esc_url(add_query_arg(array('page' => wp_unslash($_REQUEST['page'])), admin_url('admin.php')));

        add_action('admin_head', array($this, 'admin_header'));
    }

    public function admin_header()
    {
        // ensure we are on the correct page
        $page = (isset($_GET['page'])) ? esc_attr($_GET['page']) : false;
        if ($this->plugin_name . '-contact-list' != $page)
            return;

        // set up column widths
        echo '<style type="text/css">';
        echo '.wp-list-table .column-cb { width: 5%; }';
        echo '.wp-list-table .column-last_name { width: 10%; }';
        echo '.wp-list-table .column-first_name { width: 10%; }';
        echo '.wp-list-table .column_phone { width: 10%; }';
        echo '.wp-list-table .column-email { width: 15%; }';
        echo '.wp-list-table .column-contact_date { width: 10%; }';
        echo '.wp-list-table .column-comments { width: 40%; }';
        echo '</style>';
    }

    public function get_columns()
    {
        $table_columns = array(
            'cb'            => '<input type = "checkbox" />',
            'last_name'     => __('Last name', 'scsmm'),
            'first_name'    => __('First name', 'scsmm'),
            'phone'         => __('Phone', 'scsmm'),
            'email'         => __('Email', 'scsmm'),
            'contact_date'         => __('Contact Date', 'scsmm'),
            'comments'      => __('Comments', 'scsmm')
        );
        return $table_columns;
    }
    // Returns an associative array containing the bulk action.
    public function get_bulk_actions()
    {

        /*
    * on hitting apply in bulk actions the url paramas are set as
    * ?action=bulk-download&paged=1&action2=-1
    * 
    * action and action2 are set based on the triggers above and below the table	 		    
    */
        $actions = array(
            'delete'        => 'Delete Selected',
            'email'         => 'Email Selected'
        );
        return $actions;
    }

    public function no_items()
    {
        _e('No Contacts Available', 'scsmm');
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
        //$items_per_page = $this->get_items_per_page('contacts_per_page');
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
        $checkbox =  '<label class="screen-reader-text" for="contact_' . $item['id'] . '">'
            . sprintf(__('Select %s'), $item['id']) . '</label>'
            . "<input type='checkbox' name='contacts[]' id='contact_{$item['id']}' value='{$item['id']}' />";
        return sprintf($checkbox);
    }

    protected function column_last_name($item)
    {

        $admin_page_url =  admin_url('admin.php');


        // row actions to update contact data.
        $query_args_edit_contact = array(
            'page'        =>  wp_unslash($_REQUEST['page']),
            'action'    => 'edit_contact',
            'id'        => absint($item['id']),
            '_wpnonce'    => wp_create_nonce('scs-contact-form-check-string'),
        );
        $edit_contact_link = esc_url(add_query_arg($query_args_edit_contact, $admin_page_url));
        $actions['edit_contact'] = '<a href="' . $edit_contact_link . '">' . __('Edit', $this->plugin_name) . '</a>';

        $query_args_delete_contact = array(
            'page'        =>  wp_unslash($_REQUEST['page']),
            'action'    => 'delete_contact',
            'id'        => absint($item['id']),
            '_wpnonce'    => wp_create_nonce('scs-contact-form-check-string'),
        );
        $delete_contact_link = esc_url(add_query_arg($query_args_delete_contact, $admin_page_url));
        $actions['delete_contact'] = '<a href="' . $delete_contact_link . '">' . __('Delete', $this->plugin_name) . '</a>';

        $row_value = '<strong>' . $item['last_name'] . '</strong>';
        return $row_value . $this->row_actions($actions);
    }

    public function column_contact_date($item)
    {
        return date('Y-m-d', strtotime($item['contact_date']));
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
            'last_name' => 'last_name'
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

        $orderby = (isset($_GET['orderby'])) ? esc_sql($_GET['orderby']) : 'last_name';
        $order = (isset($_GET['order'])) ? esc_sql($_GET['order']) : 'ASC';


        $table_name = $this->getTable('contact_list');
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


        if ('edit_contact' === $the_table_action) {
            $nonce = wp_unslash($_REQUEST['_wpnonce']);
            // verify the nonce.
            if (!wp_verify_nonce($nonce, 'scs-contact-form-check-string')) {
                $this->invalid_nonce_redirect();
            } else {
                $this->page_edit_contact($_REQUEST['id']);
                //$this->graceful_exit();
            }
        }

        // handle the new button
        if ('add_contact' === $the_table_action) {
            $nonce = wp_unslash($_REQUEST['_wpnonce']);
            // verify the nonce.
            if (!wp_verify_nonce($nonce, 'scs-contact-form-check-string')) {
                $this->invalid_nonce_redirect();
            } else {
                $this->page_add_contact();
            }
        }
        if ('save_contact' === $the_table_action) {
            $nonce = wp_unslash($_REQUEST['_wpnonce']);
            // verify the nonce.
            if (!wp_verify_nonce($nonce, 'scs-contact-form-check-string')) {
                $this->invalid_nonce_redirect();
            } else {
                $this->page_save_contact();
                //$this->graceful_exit($this->current_url);
            }
        }

        if ('delete_contact' === $the_table_action) {
            $nonce = wp_unslash($_REQUEST['_wpnonce']);
            // verify the nonce.
            if (!wp_verify_nonce($nonce, 'scs-contact-form-check-string')) {
                $this->invalid_nonce_redirect();
            } else {
                $this->page_delete_contact($_REQUEST['id']);
                //$this->graceful_exit($this->current_url);
            }
        }

        if ('cancel_contact' === $the_table_action) {
            $nonce = wp_unslash($_REQUEST['_wpnonce']);
            // verify the nonce.
            if (!wp_verify_nonce($nonce, 'scs-contact-form-check-string')) {
                $this->invalid_nonce_redirect();
            } else {
                $this->page_cancel_contact();
                //$this->graceful_exit();
            }
        }

        if ((isset($_REQUEST['action']) && $_REQUEST['action'] === 'delete') || (isset($_REQUEST['action2']) && $_REQUEST['action2'] === 'delete')) {
            $nonce = wp_unslash($_REQUEST['_wpnonce']);
            if (!wp_verify_nonce($nonce, 'bulk-contacts')) { // verify the nonce.
                $this->invalid_nonce_redirect();
            } else {
                $this->page_delete_contacts($_REQUEST['contacts']);
                //$this->graceful_exit();
            }
        }
        if ((isset($_REQUEST['action']) && $_REQUEST['action'] === 'email') || (isset($_REQUEST['action2']) && $_REQUEST['action2'] === 'email')) {
            $nonce = wp_unslash($_REQUEST['_wpnonce']);
            if (!wp_verify_nonce($nonce, 'bulk-contacts')) { // verify the nonce.
                $this->invalid_nonce_redirect();
            } else {
                $this->page_email_contacts($_REQUEST['contacts']);
                //$this->graceful_exit();
            }
        }
    }

    /**
     * View/Edit a contact detail data.
     *
     * @since   1.0.0
     * 
     * @param int $user_id  user's ID	 
     */
    public function page_edit_contact($contact)
    {
        global $wpdb;
        $table_name = $this->getTable('contact_list');
        $this->active_contact = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $contact");
        $this->can_show_edit = true;
    }
    public function page_add_contact()
    {
        $this->can_show_edit = true;

        // create an empty class for input
        $contact = new stdClass();
        $contact->id = 0;
        $contact->first_name = '';
        $contact->last_name = '';
        $contact->email = '';
        $contact->phone = '';
        $contact->contact_date = date('Y-m-d');
        $contact->comments = '';

        $this->active_contact = $contact;
    }

    public function page_save_contact()
    {

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $results = apply_filters('save_contact', $id);

        if (is_wp_error($results)) {
            if (count($results->errors) > 0) {
                $message = "";
                foreach ($results->get_error_messages() as $error_message) {
                    $message .=  $error_message . '<br>';
                }
                $this->error_message =  $message;
                return;
            }
        }
        // open to 

        $this->message = $results['msg'];
    }

    public function page_cancel_contact()
    {
        $this->can_show_edit = false;
    }

    public function page_delete_contact($contact)
    {
        global $wpdb;

        $table_name = $this->getTable('contact_list');
        $wpdb->delete($table_name, array('id' => (int) $contact));
        $this->message = __('Successfully deleted!', $this->plugin_name);
    }


    public function page_delete_contacts($contacts)
    {
        global $wpdb;
        $contacts = implode(',', array_map('absint', $contacts));
        $table_name = $this->getTable('contact_list');
        $wpdb->query("DELETE FROM $table_name WHERE id IN ($contacts)");
        $this->message = __('Successfully deleted multiple contacts!', $this->plugin_name);
     
    }

    /**
     * Handle the email contacts action 
     *
     * @param int $ids
     * @return void
     */
    public function page_email_contacts($contacts)
    {
        global $wpdb;

        $ids = implode(',', array_map('absint', $contacts));
        $sql = "SELECT email FROM 
            {$this->getTable('contact_list')} 
        WHERE id in ($ids) ";

        $contacts = $wpdb->get_results($sql, ARRAY_A);

        $to = '';
        
        foreach ($contacts as $contact) {
            if ($to == "") $to = $contact['email'];
            else $to .= "," . $contact['email'];
        } 

        if (!session_id()) {
            session_start();
        }

        $_SESSION["to-email"] = $to;
        $_SESSION["from-page"] = add_query_arg(array('page' => $this->plugin_name . "-contact-list"), admin_url('admin.php'));

        // open to 
        $url = add_query_arg(array('page' => $this->plugin_name . "-email"), admin_url('admin.php'));
        wp_safe_redirect($url);
    }


    public function show_edit()
    {
        return $this->can_show_edit;
    }

    public function get_active_contact($attr)
    {
        switch ($attr) {
            case 'id':
                return $this->active_contact->id;
                break;
            case 'first_name':
                return $this->active_contact->first_name;
                break;
            case 'last_name':
                return $this->active_contact->last_name;
                break;
            case 'email':
                return $this->active_contact->email;
                break;
            case 'phone':
                return $this->active_contact->phone;
                break;
            case 'comments':
                return $this->active_contact->comments;
                break;
            case 'contact_date':
                return $this->active_contact->contact_date;
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
    public function graceful_exit($url = '')
    {
        if ($url !== '') {
            wp_safe_redirect($url);
        }
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
