<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/dblosser0556
 * @since      1.0.0
 *
 * @package    Scsmm
 * @subpackage Scsmm/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Scsmm
 * @subpackage Scsmm/includes
 * @author     David Blosser <blosserdl@gmail.com>
 */
class Scsmm_Common
{



    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;


    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */

    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }


    public function send_email_with_template($email_id, $to = '', $from = '', $member = '')
    {

        if (!isset($email_id) || ($to == '' && !isset($member))) return false;


        global $wpdb;

        // get the email template
        $email = $this->get_email_templates($email_id);

        // parse the message
        $message = $this->parse_email_template($email, $member);
        $options = get_option(PLUGIN_TEXT_DOMAIN);

        if ($from == '') {
            if (isset($options['email']) && $options['email'] != '') {
                $from =  $options['email'];
            } else {
                $from =  get_option('admin_email');
            }
        }

        // get the email addresses
        if ($to == '')
            $to = $member['email'];

        // Get the message subject 
        // todo store this in email table
        $subject = $email['subject'];

        // setup  the headers
        $headers[] = 'From: ' . $from;
        $headers[] = 'Cc: ' . $from;
        $headers[] = 'Content-Type: text/html; charset=UTF-8';

        // send the email
        $sent = wp_mail($to, $subject, $message, $headers);
        return $sent;
    }

    /**
     * Parse the email template to replace variables with text from database
     *
     * @param array $email
     * @param array $member
     * @param string $url
     * @return string 
     */
    private function parse_email_template($email, $member, $url = '')
    {

        // go first through all the keys of the member as they are all allowed values
        $keys = array_keys($member);
        $content = $email['email_text'];

        foreach ($keys as $key) {
            $replace = "/\{" . $key . "\}/";
            $content = preg_replace($replace, $member[$key], $content);
        }



        preg_match('/\{registration_url\}/', $content, $matches);

        if (count($matches) > 0) {
            $options = get_option(PLUGIN_TEXT_DOMAIN);
            $check_page_url = $options['registration-check-page'];

            if (isset($check_page_url)  && $check_page_url != '') {
                $link = esc_url(add_query_arg('key', $member['registration_key'], $check_page_url));
                $url = '<a href=' . $link . '>' . __('Register With Us', PLUGIN_TEXT_DOMAIN) . '</a>';

                $content = preg_replace('/\{registration_url\}/', $url, $content);
            }
        }

        return $content;
    }

    public function get_plugin_table($table)
    {
        global $wpdb;
        return "{$wpdb->prefix}scsmm_{$table}";
    }

    public function get_email_templates($email_id = 0)
    {

        global $wpdb;


        $sql = "SELECT * FROM {$this->get_plugin_table('email_templates')}";

        if ($email_id != 0) {
            $sql .=  " WHERE id = $email_id";

            // get the email template
            $email = $wpdb->get_row(
                $sql,
                ARRAY_A
            );
        } else {
            $email = $wpdb->get_results($sql, ARRAY_A);
        }
        return $email;
    }


    public function save_contact($id = 0)
    {




        // check to ensure we know the requester
        if (!check_ajax_referer('scs-contact-form-check-string', 'security')) {
            $reg_errors = new WP_Error;
            $reg_errors->add('invalid nonce', 'invalid nonce');
            return $reg_errors;
        }


        $phone     =                 sanitize_text_field($_POST['phone']);
        $email      =           sanitize_email($_POST['email']);
        $first_name =           sanitize_text_field($_POST['first_name']);
        $last_name  =           sanitize_text_field($_POST['last_name']);
        $comments =             sanitize_textarea_field($_POST['comments']);
        $contact_date =         sanitize_text_field($_POST['contact_date']);

        $reg_errors = $this->contact_validation($email, $first_name, $last_name);

        if (count($reg_errors->errors) > 0) {
            return $reg_errors;
        }


        if ($this->complete_contact_request($id, $email, $first_name, $last_name, $phone, $comments, $contact_date)) {
            $results['success'] = true;
            $results['msg'] = 'Contact details successfully added.';
            return $results;
           
        } else {
            $reg_errors = new WP_Error;
            $reg_errors->add('error with saving contact', 'Something went wrong. See your administrator.');
            return $reg_errors;
        }
    }

    private function contact_validation($email, $first_name, $last_name)
    {
        $reg_errors = new WP_Error;

        if (empty($email)  || empty($first_name)  || empty($last_name)) {
            $reg_errors->add('field', 'Required form field is missing');
        }

        return $reg_errors;
    }

    private function complete_contact_request($id, $email, $first_name, $last_name, $phone, $comments, $contact_date)
    {
        global $wpdb;

        $contact = array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'phone' => $phone,
            'comments' => $comments,
            'contact_date' => $contact_date
        );
        if ($id === 0) {
            $count = $wpdb->insert(
                $this->get_plugin_table('contact_list'),
                $contact
            );
        } else {
            $count = $wpdb->update(
                $this->get_plugin_table('contact_list'),
                $contact,
                array('id' => $id)
            );
        }

        return $count;
    }
}
