<?php

/* 
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/screen.php' );
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
} */

class Member_List_Table extends WP_List_Table {
    protected $plugin_name;
    private $data;
    public function __construct( $plugin_name, $data ) {

		
		$this->plugin_name = $plugin_name;
        $this->data = $data;

        /*
		parent::__construct( array( 
				'plural'	=>	'users',	// Plural value used for labels and the objects being listed.
				'singular'	=>	'user',		// Singular label for an object being listed, e.g. 'post'.
				'ajax'		=>	false,		// If true, the parent class will call the _js_vars() method in the footer		
            ) );
            */
    }
    
    public function get_columns()
    {
        $table_columns = array(
            'cb'        => '<input type = "checkbox" />',
            'type'      => __('Type', 'scsmm'),
            'status'    => __('Status', 'scsmm'),
            'firstname' => __('First Name', 'scsmm'),
            'lastname'  => __('Last Name', 'scsmm'),
            'email'     => __('Email', 'scsmm'),
            'phone'     => __('Phone', 'scsmm'),
            'joindate'  => __('Join Date', 'scsmm')
        );
        return $table_columns;
    }

    public function no_items() {
        _e('No Members Available', 'scsmm');
    }

    public function prepare_items() {
        
        $this->_column_headers = $this->get_column_info();

        $this->items = $this->data;

    }

    public function column_default( $item, $column_name ) {		
        switch ( $column_name ) {			
            case 'type':
            case 'status':
            case 'firstname':
            case 'lastname':
            case 'email':
            case 'phone':
            case 'joindate':
                return $item[$column_name];
            default:
              return $item[$column_name];
        }
    }

   
}