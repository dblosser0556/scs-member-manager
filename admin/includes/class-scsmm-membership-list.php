<?php





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

        add_action('admin_head', array($this, 'admin_header'));
    }

    public function admin_header()
    {
        // ensure we are on the correct page
        $page = (isset($_GET['page'])) ? esc_attr($_GET['page']) : false;
        if ($this->plugin_name . '-member-list' != $page)
            return;

        // set up column widths
        echo '<style type="text/css">';
        echo '.wp-list-table .column-cb { width: 5%; }';
        echo '.wp-list-table .column-membername { width: 30%; }';
        echo '.wp-list-table .column-type { width: 10%; }';
        echo '.wp-list-table .column-status { width: 10%; }';
        echo '.wp-list-table .column-email { width: 15%; }';
        echo '.wp-list-table .column-phone { width: 10%;}';
        echo '.wp-list-table .column-id { width: 5%;}';
        echo '.wp-list-table .column-join_date { width: 15%;}';
        echo '</style>';
    }

    public function get_columns()
    {
        $table_columns = array(
            'cb'        => '<input type = "checkbox" />',
            'membername' => __('Name', 'scsmm'),
            'type'      => __('Type', 'scsmm'),
            'status'    => __('Status', 'scsmm'),
            'email'     => __('Email', 'scsmm'),
            'phone'     => __('Phone', 'scsmm'),
            'id'        => __('Id', 'scsmm'),
            'join_date'  => __('Join Date', 'scsmm')
        );
        return $table_columns;
    }

    public function get_hidden_columns()
    {
        $table_columns = array(
            'username'  => __('User Name', PLUGIN_TEXT_DOMAIN),
            'first_name'  => __('First Name', PLUGIN_TEXT_DOMAIN),
            'last_name'  => __('Last Name', PLUGIN_TEXT_DOMAIN),
            'address1'  => __('Address1', PLUGIN_TEXT_DOMAIN),
            'address2'  => __('Address2', PLUGIN_TEXT_DOMAIN),
            'city'      => __('City', PLUGIN_TEXT_DOMAIN),
            'state'     => __('State', PLUGIN_TEXT_DOMAIN),
            'zipcode'   => __('Zip', PLUGIN_TEXT_DOMAIN)
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

        // row actions to view member data
        $query_args_view_member = array(
            'page'        =>  wp_unslash($_REQUEST['page']),
            'action'    => 'view_member',
            'memberid'        => absint($item['id']),
            '_wpnonce'    => wp_create_nonce('view_member_nonce'),
        );
        $view_member_link = esc_url(add_query_arg($query_args_view_member, $admin_page_url));
        $actions['view_member'] = '<a href="' . $view_member_link . '">' . __('View', $this->plugin_name) . '</a>';

        // row actions to update member data.
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

    public function column_status($item)
    {
        $admin_page_url =  admin_url('admin.php');

        global $wpdb;
        $sql  = "SELECT * FROM {$this->getTable('status_types')} WHERE ID = {$item['status_id']}";
        $work_flow_action = $wpdb->get_row($sql, ARRAY_A);

        // workflow should come in a pattern of
        // action:query_param=value,query_param=value;action:query_param=value,  ...
        // first get the group of action by splitting on semi-colon
        // the only two supported additional query parameters are
        // final_status and email 
        $avail_actions = explode(';', $work_flow_action['work_flow_action']);

        // go through the list of these action
        foreach ($avail_actions as $avail_action) {

            // get the action and the set of parameters
            $action_parts = explode(":", $avail_action);
            $query_params = explode(",", $action_parts[1]);

            // set up the defaults 
            $query_args = array(
                'page'          =>  wp_unslash($_REQUEST['page']),
                'action'        => 'change_status',
                'memberid'      => absint($item['id']),
                '_wpnonce'      => wp_create_nonce('change_status_nonce'),
            );

            // add the final additonal parameters
            foreach ($query_params as $query_param) {
                $parts = explode("=", $query_param);
                $query_args[$parts[0]] = $parts[1];
            }

            //create the link
            $link = esc_url(add_query_arg($query_args, $admin_page_url));

            $actions[$action_parts[0]] = '<a href="' . $link . '">' . $action_parts[0] . '</a>';
        }

        $row_value = '<strong>' . $item['status'] . '</strong>';

        if (count($actions)) {
            return $row_value . $this->row_actions($actions);
        } else {
            return $row_value;
        }
    }

    public function column_join_date($item)
    {
        return date('Y-m-d', strtotime($item['join_date']));
    }

    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'type':
            case 'status':
            case 'email':
            case 'phone':
            case 'join_date':
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
            'join_date' => 'join_date'
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

        $sql = "SELECT memberships.*, Concat(memberships.first_name, ' ', memberships.last_name) as membername, 
            membership_types.name as type,
            status_types.name as status FROM 
        {$this->getTable('member_list')} as memberships,
           {$this->getTable('membership_types')} as membership_types,
        {$this->getTable('status_types')} as status_types   
            WHERE membership_types.id=memberships.membership_type_id AND
                status_types.id=memberships.status_id 
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
                $this->page_edit_member('view_member', $_REQUEST['memberid']);
                $this->graceful_exit();
            }
        }

        if ('edit_member' === $the_table_action) {
            $nonce = wp_unslash($_REQUEST['_wpnonce']);
            // verify the nonce.
            if (!wp_verify_nonce($nonce, 'edit_member_nonce')) {
                $this->invalid_nonce_redirect();
            } else {
                $this->page_edit_member('edit_member', $_REQUEST['memberid']);
                $this->graceful_exit();
            }
        }

        if ('change_status' === $the_table_action) {
            $nonce = wp_unslash($_REQUEST['_wpnonce']);
            if (!wp_verify_nonce($nonce, 'change_status_nonce')) {
                $this->invalid_nonce_redirect();
            } else {
                $this->page_change_status();
            }
        }

        if ((isset($_REQUEST['action']) && $_REQUEST['action'] === 'bulk-download') || (isset($_REQUEST['action2']) && $_REQUEST['action2'] === 'bulk-download')) {
            $nonce = wp_unslash($_REQUEST['_wpnonce']);
            /*
             * Note: the nonce field is set by the parent class
             * wp_nonce_field( 'bulk-' . $this->_args['plural'] );	 
             */
            if (!wp_verify_nonce($nonce, 'bulk-members')) { // verify the nonce.
                $this->invalid_nonce_redirect();
            } else {
                $this->page_download_members($_REQUEST['members']);
                $this->graceful_exit();
            }
        }
        if ((isset($_REQUEST['action']) && $_REQUEST['action'] === 'delete') || (isset($_REQUEST['action2']) && $_REQUEST['action2'] === 'delete')) {
            $nonce = wp_unslash($_REQUEST['_wpnonce']);
            if (!wp_verify_nonce($nonce, 'bulk-members')) { // verify the nonce.
                $this->invalid_nonce_redirect();
            } else {
                $this->page_delete_members($_REQUEST['members']);
                $this->graceful_exit();
            }
            /*todo - handle delete action 
        */
        }
        if ((isset($_REQUEST['action']) && $_REQUEST['action'] === 'email') || (isset($_REQUEST['action2']) && $_REQUEST['action2'] === 'email')) {
            $nonce = wp_unslash($_REQUEST['_wpnonce']);
            if (!wp_verify_nonce($nonce, 'bulk-members')) { // verify the nonce.
                $this->invalid_nonce_redirect();
            } else {
                $this->page_email_members($_REQUEST['members']);
                $this->graceful_exit();
            }
        }
    }

    /**
     * View/Edit a member detail data.
     *
     * @since   1.0.0
     * 
     * @param int $user_id  user's ID	 
     */
    public function page_edit_member($action, $memberid)
    {
        $query_args = array(
            'page' => $this->plugin_name . '-membership',
            'action' => $action,
            'memberid' => $memberid,
            'referer' => $_REQUEST['page']
        );

        $page = add_query_arg($query_args, admin_url('admin.php'));
        wp_safe_redirect($page);
        //$file = PLUGIN_DIR . 'includes/partials/scsmm-membership.php';
        //include_once($file);
    }

    public function page_email_members($ids)
    {
        global $wpdb;

        $ids = "";
        foreach ($ids as $id) {
            if ($ids == '') $ids = $id;
            else $ids .=  ',' . $id;
        }
        $sql = "SELECT memberships.email FROM 
            {$this->getTable('member_list')} as memberships
        WHERE id in (" . $ids . ") ";

        $members = $wpdb->get_results($sql);

        $to = "";
        foreach ($members as $member) {
            if ($to == "") $to = $member->email;
            else $to .= "," . $member->email;
        }

        if (!session_id()) {
            session_start();
        }

        $_SESSION["to-email"] = $to;
        $_SESSION["from-page"] = add_query_arg(array('page' => $this->plugin_name . "-member-list"), admin_url('admin.php'));

        // open to 
        $url = add_query_arg(array('page' => $this->plugin_name . "-email"), admin_url('admin.php'));
        wp_safe_redirect($url);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function page_change_status()
    {
        if (isset($_REQUEST['status_final'])) {

            global $wpdb;

            $status_final = $_REQUEST['status_final'];

            $count = $wpdb->update(
                $this->getTable('member_list'),
                array('status_id' => $status_final),
                array('id' => $_REQUEST['memberid']),
                array('%d')
            );

            if (!isset($count)) {
                $this->errormessage = 'Something went wrong is setting the final status';
            }
        }

        if (isset($_REQUEST['email'])) {
            
            $id = ($_REQUEST['email']);
            $memberid = ($_REQUEST['memberid']);
            

            // get the member
            $member = $wpdb->get_row( 
            "SELECT * FROM {$this->getTable('member-list')} 
                WHERE id = $memberid", ARRAY_A
            );

            // get the email template
            $email = $wpdb->get_row( 
                "SELECT * FROM {$this->getTable('email-templates')} 
                    WHERE id = $id", ARRAY_A);
             
            // sender is stored in the options
             $options = get_option(PLUGIN_TEXT_DOMAIN);

             if (isset($options['email'])) {
                 $from =  $options['email']; 
             } else {
                 $from =  get_option('admin_email');
             }
            // get the email addresses
            $to = $member->email;
             
             // get the url of the check registration option
             $check_page_url = $options['registration-check-page'];


            // parse the message
            $message = $this->parse_email_template($email->email_text, $member, $check_page_url);

            
            
           

            // Get the message subject 
            // todo store this in email table
            $subject = "Congratlations for Successfully Applying";
            
            // setup  the headers
            $headers[] = 'From: ' . $from;
            $headers[] = 'Cc: ' . $from;
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
            
            // send the email
            wp_mail($to, $subject, $message, $headers);
            

        }

        $url = add_query_arg(array('page' => $_REQUEST['page']), admin_url('admin.php'));
        wp_safe_redirect($url);
    }
    public function page_download_members($ids)
    {

        //todo
    }

    public function page_delete_members($ids)
    {

        //todo
    }

    private function parse_email_template($template, $member, $url ) {
        
        // go first through all the keys of the member as they are all allowed values
        $keys = array_keys($member);
        $content = $template;

        foreach($keys as $key) {
            $replace = "{" . $key . "}";
            $content = str_replace($replace, $member[$key], $content);
        }
        $url .= '&key=' . $member['registration_key'];

        $content = str_replace('{registation_url}', $url, $content);

        return $content;
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
