<?php


if (!class_exists('SCSWP_List_Table')) {
    //require_once( ABSPATH . 'wp-admin/includes/screen.php' );
    //require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
    require_once(plugin_dir_path(dirname(__FILE__)) . 'includes/libraries/class-membership-list.php');
}


class Member_List_Table extends SCSWP_List_Table
{
    protected $plugin_name;
    public function __construct($plugin_name)
    {


        $this->plugin_name = $plugin_name;
        //$this->data = $data;


        parent::__construct(array(
            'plural'    =>    'members',    // Plural value used for labels and the objects being listed.
            'singular'    =>    'member',        // Singular label for an object being listed, e.g. 'post'.
            'ajax'        =>    false,        // If true, the parent class will call the _js_vars() method in the footer		
        ));
    }

    public function get_columns()
    {
        $table_columns = array(
            'cb'        => '<input type = "checkbox" />',
            'type'      => __('Type', 'scsmm'),
            'status'    => __('Status', 'scsmm'),
            'membername' => __('Name', 'scsmm'),
            'email'     => __('Email', 'scsmm'),
            'phone'     => __('Phone', 'scsmm'),
            'joindate'  => __('Join Date', 'scsmm')
        );
        return $table_columns;
    }

    public function no_items()
    {
        _e('No Members Available', 'scsmm');
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
        $items_per_page = $this->get_items_per_page('members_per_page');
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
        $checkbox =  '<label class="screen-reader-text" for="member_' . $item['id'] . '">'
            . sprintf(__('Select %s'), $item['id']) . '</label>'
            . "<input type='checkbox' name='members[]' id='member_{$item['id']}' value='{$item['id']}' />";
        return sprintf($checkbox);
    }

    protected function column_membername($item)
    {

        $admin_page_url =  admin_url('admin.php');

        // row actions to view usermeta.
        $query_args_view_member = array(
            'page'        =>  wp_unslash($_REQUEST['page']),
            'action'    => 'view_member',
            'memberid'        => absint($item['id']),
            '_wpnonce'    => wp_create_nonce('view_member_nonce'),
        );
        $view_member_link = esc_url(add_query_arg($query_args_view_member, $admin_page_url));
        $actions['view_member'] = '<a href="' . $view_member_link . '">' . __('View', $this->plugin_name) . '</a>';

        // row actions to add usermeta.
        $query_args_edit_member = array(
            'page'        =>  wp_unslash($_REQUEST['page']),
            'action'    => 'edit_member',
            'memberid'        => absint($item['id']),
            '_wpnonce'    => wp_create_nonce('edit_member_nonce'),
        );
        $edit_member_link = esc_url(add_query_arg($query_args_edit_member, $admin_page_url));
        $actions['edit_member'] = '<a href="' . $edit_member_link . '">' . __('Edit', $this->plugin_name) . '</a>';


        $row_value = '<strong>' . $item['membername'] . '</strong>';
        return $row_value . $this->row_actions($actions);
    }

    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'type':
            case 'status':
            case 'email':
            case 'phone':
            case 'joindate':
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
            'membername' => 'membername',
            'type' => 'type',
            'status' => 'status',
            'joindate' => 'joindate'
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

        $orderby = (isset($_GET['orderby'])) ? esc_sql($_GET['orderby']) : 'status';
        $order = (isset($_GET['order'])) ? esc_sql($_GET['order']) : 'ASC';

        $sql = "SELECT memberships.*, Concat(memberships.firstname, ' ', memberships.lastname) as membername, 
            membership_types.name as type,
            status_types.name as status FROM 
        {$this->getTable('member_list')} as memberships,
           {$this->getTable('membership_types')} as membership_types,
        {$this->getTable('status_types')} as status_types   
            WHERE membership_types.id=memberships.membershiptypeid AND
                status_types.id=memberships.statusid 
            ORDER BY $orderby  $order";




        return $wpdb->get_results($sql, ARRAY_A);
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
    public function get_bulk_actions()
    {
        /*
	 * on hitting apply in bulk actions the url paramas are set as
	 * ?action=bulk-download&paged=1&action2=-1
	 * 
	 * action and action2 are set based on the triggers above and below the table		 		    
	 */
        $actions = array(
            'bulk-download' => 'Download Member Data',
            'delete'        => 'Delete Member Data',
            'email'         => 'Send Email to Group'
        );
        return $actions;
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

        if ('view_member' === $the_table_action) {
            $nonce = wp_unslash($_REQUEST['_wpnonce']);
            // verify the nonce.
            if (!wp_verify_nonce($nonce, 'view_member_nonce')) {
                $this->invalid_nonce_redirect();
            } else {
                $this->page_edit_member();
                $this->graceful_exit();
            }
        }

        if ('edit_member' === $the_table_action) {
            $nonce = wp_unslash($_REQUEST['_wpnonce']);
            // verify the nonce.
            if (!wp_verify_nonce($nonce, 'edit_member_nonce')) {
                $this->invalid_nonce_redirect();
            } else {
                $this->page_edit_member();
                $this->graceful_exit();
            }
        }


        if ((isset($_REQUEST['action']) && $_REQUEST['action'] === 'bulk-download') || (isset($_REQUEST['action2']) && $_REQUEST['action2'] === 'bulk-download')) {
            $nonce = wp_unslash($_REQUEST['_wpnonce']);
            /*
             * Note: the nonce field is set by the parent class
             * wp_nonce_field( 'bulk-' . $this->_args['plural'] );	 
             */
            if (!wp_verify_nonce($nonce, 'bulk-users')) { // verify the nonce.
                $this->invalid_nonce_redirect();
            } else {
                //include_once('views/partials-wp-list-table-demo-bulk-download.php');
                //
                $this->graceful_exit();
            }
        }
        if ((isset($_REQUEST['action']) && $_REQUEST['action'] === 'delete') || (isset($_REQUEST['action2']) && $_REQUEST['action2'] === 'delete')) {
            /*todo - handle delete action 
        */ }
        if ((isset($_REQUEST['action']) && $_REQUEST['action'] === 'email') || (isset($_REQUEST['action2']) && $_REQUEST['action2'] === 'email')) {
            /*todo - create email page 
        */ }
    }

    /**
     * View/Edit a member detail data.
     *
     * @since   1.0.0
     * 
     * @param int $user_id  user's ID	 
     */
    public function page_edit_member()
    {
     
        $file = plugin_dir_path(dirname(__FILE__)) . 'includes/partials/scsmm-membership.php';
        include_once($file);
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
