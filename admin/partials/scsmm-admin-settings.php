<?php

/**
 * Provide a settings view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://github.com/dblosser0556
 * @since      1.0.0
 *
 * @package    Scsmm
 * @subpackage Scsmm/admin/partials
 */


?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<?php
if (!current_user_can('manage_options')) {
    wp_die();
}
?>

<div class="wrap">
    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>

    <?php
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'redirect_options';
    ?>

    <h2 class="nav-tab-wrapper">
        <a href="?page=scsmm-settings&tab=redirect_options" class="nav-tab <?php echo $active_tab == 'redirect_options' ? 'nav-tab-active' : ''; ?>">Redirect Options</a>
        <a href="?page=scsmm-settings&tab=membership_options" class="nav-tab <?php echo $active_tab == 'membership_options' ? 'nav-tab-active' : ''; ?>">Type Options</a>
        <a href="?page=scsmm-settings&tab=relationship_options" class="nav-tab <?php echo $active_tab == 'relationship_options' ? 'nav-tab-active' : ''; ?>">Relationships</a>
        <a href="?page=scsmm-settings&tab=status_options" class="nav-tab <?php echo $active_tab == 'status_options' ? 'nav-tab-active' : ''; ?>">Status</a>
        <a href="?page=scsmm-settings&tab=email_options" class="nav-tab <?php echo $active_tab == 'email_options' ? 'nav-tab-active' : ''; ?>">Email</a>
    </h2>
    <?php
    if ($active_tab == 'redirect_options') { ?>
    <form action="options.php" name="member-manager-options" method="post">
        <?php settings_fields($this->plugin_name); ?>
        <?php
            // grab all the options to set save values
            $settings = get_option($this->plugin_name);

            $email = $settings['email'];
            $contact_page = $settings['contact-page'];
            $contact_redirect = $settings['contact-redirect-page'];
            $application_page = $settings['application-page'];
            $application_redirect = $settings['application-redirect-page'];

            ?>
        <table class="form-table">
            <tbody>
                <!-- Member Manager Email -->
                <tr>
                    <th scope="row">
                        <label for="<?php echo $this->plugin_name; ?>-email"><?= esc_html__("Member Manager Email", $this->plugin_name) ?></label>
                    </th>
                    <td>
                        <input type="email" name="<?php echo $this->plugin_name; ?>[email]" id="<?php echo $this->plugin_name; ?>-email" value="<?php if (!empty($email)) echo $email; ?>">
                    </td>
                </tr>
                <!-- Contact Page -->
                <tr>
                    <th scope="row">
                        <label for="<?php echo $this->plugin_name; ?>-contact-page"><?= esc_html__("Contact Page", $this->plugin_name) ?></label>
                    </th>
                    <td>
                        <select name="<?php echo $this->plugin_name; ?>[contact-page]" id="<?php echo $this->plugin_name; ?>-contact-page">
                            <option value="">
                                <?php echo esc_attr(__('Select page')); ?></option>
                            <?php
                                $pages = get_pages();
                                foreach ($pages as $page) {
                                    $option = '<option value="' . get_page_link($page->ID) . '"' . selected($contact_page, get_page_link($page->ID))  . '>';
                                    $option .= $page->post_title;
                                    $option .= '</option>';
                                    echo $option;
                                }
                                ?>
                        </select>
                    </td>
                </tr>
                <!-- Contact Redirect Page -->
                <tr>
                    <th scope="row">
                        <label for="<?php echo $this->plugin_name; ?>-contact-redirect-page"><?= esc_html__("Contact Redirect Page", $this->plugin_name) ?></label>
                    </th>
                    <td>
                        <select name="<?php echo $this->plugin_name; ?>[contact-redirect-page]" id="<?php echo $this->plugin_name; ?>-contact-redirect-page">
                            <option value="">
                                <?php echo esc_attr(__('Select page')); ?></option>
                            <?php
                                $pages = get_pages();
                                foreach ($pages as $page) {
                                    $option = '<option value="' . get_page_link($page->ID) . '"' . selected($contact_redirect, get_page_link($page->ID))  . '>';
                                    $option .= $page->post_title;
                                    $option .= '</option>';
                                    echo $option;
                                }
                                ?>
                        </select>
                    </td>
                </tr>

                <!-- Application Page -->
                <tr>
                    <th scope="row">
                        <label for="<?php echo $this->plugin_name; ?>-application-page"><?= esc_html__("Application Page", $this->plugin_name) ?></label>
                    </th>
                    <td>
                        <select name="<?php echo $this->plugin_name; ?>[application-page]" id="<?php echo $this->plugin_name; ?>-application-page">
                            <option value="">
                                <?php echo esc_attr(__('Select page')); ?></option>
                            <?php
                                $pages = get_pages();
                                foreach ($pages as $page) {
                                    $option = '<option value="' . get_page_link($page->ID) . '"' . selected($application_page, get_page_link($page->ID))  . '>';
                                    $option .= $page->post_title;
                                    $option .= '</option>';
                                    echo $option;
                                }
                                ?>
                        </select>
                    </td>
                </tr>
                <!-- Application Redirect Page -->
                <tr>
                    <th scope="row">
                        <label for="<?php echo $this->plugin_name; ?>-application-redirect-page"><?= esc_html__("Application Redirect Page", $this->plugin_name) ?></label>
                    </th>
                    <td>
                        <select name="<?php echo $this->plugin_name; ?>[application-redirect-page]" id="<?php echo $this->plugin_name; ?>-application-redirect-page">
                            <option value="">
                                <?php echo esc_attr(__('Select page')); ?></option>
                            <?php
                                $pages = get_pages();
                                foreach ($pages as $page) {
                                    $option = '<option value="' . get_page_link($page->ID) . '"' . selected($application_redirect, get_page_link($page->ID))  . '>';
                                    $option .= $page->post_title;
                                    $option .= '</option>';
                                    echo $option;
                                }
                                ?>
                        </select>
                    </td>
                </tr>



            </tbody>

        </table>
        <p class="submit"><input type="submit" value="Save Changes" class="button-primary" name="Submit"></p>
    </form>
    <?php } else if ($active_tab == 'membership_options') {
        require_once PLUGIN_DIR . 'admin/partials/settings/scsmm-admin-membership-type-list.php';
    } else if ($active_tab == 'relationship_options') {
        require_once PLUGIN_DIR . 'admin/partials/settings/scsmm-admin-relationship-type-list.php';
    } else if ($active_tab == 'status_options') {
        require_once PLUGIN_DIR . 'admin/partials/settings/scsmm-admin-status-list.php';
    }    else if ($active_tab == 'email_options') {
            require_once PLUGIN_DIR . 'admin/partials/settings/scsmm-admin-email-templates-list.php';
    }  ?>
</div>