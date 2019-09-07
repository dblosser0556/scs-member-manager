<form action="options.php" name="member-manager-options" method="post">
    <?php settings_fields($this->plugin_name); ?>
    <?php
    
    $emails = apply_filters('get_email_templates', $email_id='0');
    $pages = get_pages();

    
    // grab all the options to set save values
    $settings = get_option($this->plugin_name);

    
    $email = $settings['email'];
    $lifetime = $settings['lifetime'];
    $registration_check_page = $settings['registration-check-page'];
    $registration_check_redirect_page = $settings['registration-check-redirect-page'];
    $registration_expired_redirect_page = $settings['registration-expired-redirect-page'];
    $registration_not_found_redirect_page = $settings['registration-not-found-redirect-page'];
    $application_redirect = $settings['application-redirect-page'];
    $contact_page = $settings['contact-page'];
    $contact_success_redirect_page = $settings['contact-success-redirect-page'];
    $registration_success_redirect_page = $settings['registration-success-redirect-page'];
    $member_application_notification_email = $settings['member-application-notification-email'];
    $member_application_confirmation_email = $settings['member-application-confirmation-email'];
    $member_application_approved_email = $settings['member-application-approved-email'];

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
            <tr>
                <th scope="row">
                    <label for="<?php echo $this->plugin_name; ?>-lifetime"><?= esc_html__("Registration Request Lifetime", $this->plugin_name) ?></label>
                </th>
                <td>
                    <input type="number" name="<?php echo $this->plugin_name; ?>[lifetime]" id="<?php echo $this->plugin_name; ?>-number" value="<?php if (!empty($lifetime)) echo $lifetime; ?>">
                </td>
            </tr>
            <!-- Registration Page -->
            <tr>
                <th scope="row">
                    <label for="<?php echo $this->plugin_name; ?>-registration-check-page"><?= esc_html__("Registration Page", $this->plugin_name) ?></label>
                </th>
                <td>
                    <select name="<?php echo $this->plugin_name; ?>[registration-check-page]" id="<?php echo $this->plugin_name; ?>-registration-check-page">
                        <option value="">
                            <?php echo esc_attr(__('Select page')); ?></option>
                        <?php
                        
                        foreach ($pages as $page) {
                            $option = '<option value="' . get_page_link($page->ID) . '"' . selected($registration_check_page, get_page_link($page->ID))  . '>';
                            $option .= $page->post_title;
                            $option .= '</option>';
                            echo $option;
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <!-- Registration Redirect Page -->
            <tr>
                <th scope="row">
                    <label for="<?php echo $this->plugin_name; ?>-registration-check-redirect-page"><?= esc_html__("Registration Check Redirect Page", $this->plugin_name) ?></label>
                </th>
                <td>
                    <select name="<?php echo $this->plugin_name; ?>[registration-check-redirect-page]" id="<?php echo $this->plugin_name; ?>-registration-check-redirect-page">
                        <option value="">
                            <?php echo esc_attr(__('Select page')); ?></option>
                        <?php
                     
                        foreach ($pages as $page) {
                            $option = '<option value="' . get_page_link($page->ID) . '"' . selected($registration_check_redirect_page, get_page_link($page->ID))  . '>';
                            $option .= $page->post_title;
                            $option .= '</option>';
                            echo $option;
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <!-- registration expired Page -->
            <tr>
                <th scope="row">
                    <label for="<?php echo $this->plugin_name; ?>-registration-expired-redirect-page"><?= esc_html__("Registration Expired Page", $this->plugin_name) ?></label>
                </th>
                <td>
                    <select name="<?php echo $this->plugin_name; ?>[registration-expired-redirect-page]" id="<?php echo $this->plugin_name; ?>-registration-expired-redirect-page">
                        <option value="">
                            <?php echo esc_attr(__('Select page')); ?></option>
                        <?php
                        
                        foreach ($pages as $page) {
                            $option = '<option value="' . get_page_link($page->ID) . '"' . selected($registration_expired_redirect_page, get_page_link($page->ID))  . '>';
                            $option .= $page->post_title;
                            $option .= '</option>';
                            echo $option;
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <!-- registration expired Page -->
            <tr>
                <th scope="row">
                    <label for="<?php echo $this->plugin_name; ?>-registration-not-found-redirect-page"><?= esc_html__("Registration Not Found Page", $this->plugin_name) ?></label>
                </th>
                <td>
                    <select name="<?php echo $this->plugin_name; ?>[registration-not-found-redirect-page]" id="<?php echo $this->plugin_name; ?>-registration-not-found-redirect-page">
                        <option value="">
                            <?php echo esc_attr(__('Select page')); ?></option>
                        <?php
                        
                        foreach ($pages as $page) {
                            $option = '<option value="' . get_page_link($page->ID) . '"' . selected($registration_not_found_redirect_page, get_page_link($page->ID))  . '>';
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
            <tr>
                <th scope="row">
                    <label for="<?php echo $this->plugin_name; ?>-contact-page"><?= esc_html__("Contact Page", $this->plugin_name) ?></label>
                </th>
                <td>
                    <select name="<?php echo $this->plugin_name; ?>[contact-page]" id="<?php echo $this->plugin_name; ?>-contact-page">
                        <option value="">
                            <?php echo esc_attr(__('Select page')); ?></option>
                        <?php
                       
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
            <tr>
                <th scope="row">
                    <label for="<?php echo $this->plugin_name; ?>-contact-success-redirect-page"><?= esc_html__("Contact success Redirect Page", $this->plugin_name) ?></label>
                </th>
                <td>
                    <select name="<?php echo $this->plugin_name; ?>[contact-success-redirect-page]" id="<?php echo $this->plugin_name; ?>-contact-success-redirect-page">
                        <option value="">
                            <?php echo esc_attr(__('Select page')); ?></option>
                        <?php
                       
                        foreach ($pages as $page) {
                            $option = '<option value="' . get_page_link($page->ID) . '"' . selected($contact_success_redirect_page, get_page_link($page->ID))  . '>';
                            $option .= $page->post_title;
                            $option .= '</option>';
                            echo $option;
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="<?php echo $this->plugin_name; ?>-registration-success-redirect-page"><?= esc_html__("Registration Success Redirect Page", $this->plugin_name) ?></label>
                </th>
                <td>
                    <select name="<?php echo $this->plugin_name; ?>[registration-success-redirect-page]" id="<?php echo $this->plugin_name; ?>-registration-success-redirect-page">
                        <option value="">
                            <?php echo esc_attr(__('Select page')); ?></option>
                        <?php
                        
                        foreach ($pages as $page) {
                            $option = '<option value="' . get_page_link($page->ID) . '"' . selected($registration_success_redirect_page, get_page_link($page->ID))  . '>';
                            $option .= $page->post_title;
                            $option .= '</option>';
                            echo $option;
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="<?php echo $this->plugin_name; ?>-member-application-notification-email"><?= esc_html__("Member Application Notification Email", $this->plugin_name) ?></label>
                </th>
                <td>
                    <select name="<?php echo $this->plugin_name; ?>[member-application-notification-email]" id="<?php echo $this->plugin_name; ?>-member-application-notification-email">
                        <option value="">
                            <?php echo esc_attr(__('Select Email Template')); ?></option>
                        <?php
                        
                        foreach ($emails as $email) {
                            $option = '<option value="' . $email['id'] . '"' . selected($member_application_notification_email, $email['id'])  . '>';
                            $option .= $email['name'];
                            $option .= '</option>';
                            echo $option;
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="<?php echo $this->plugin_name; ?>-member-application-confirmation-email"><?= esc_html__("Member Application Confirmation Email", $this->plugin_name) ?></label>
                </th>
                <td>
                    <select name="<?php echo $this->plugin_name; ?>[member-application-confirmation-email]" id="<?php echo $this->plugin_name; ?>-member-application-confirmation-email">
                        <option value="">
                            <?php echo esc_attr(__('Select Email Template')); ?></option>
                        <?php
                        
                        foreach ($emails as $email) {
                            $option = '<option value="' . $email['id'] . '"' . selected($member_application_confirmation_email, $email['id'])  . '>';
                            $option .= $email['name'];
                            $option .= '</option>';
                            echo $option;
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="<?php echo $this->plugin_name; ?>-member-application-approved-email"><?= esc_html__("Member Application Approved Email", $this->plugin_name) ?></label>
                </th>
                <td>
                    <select name="<?php echo $this->plugin_name; ?>[member-application-approved-email]" id="<?php echo $this->plugin_name; ?>-member-application-approved-email">
                        <option value="">
                            <?php echo esc_attr(__('Select Email Template')); ?></option>
                        <?php
                      
                        foreach ($emails as $email) {
                            $option = '<option value="' . $email['id'] . '"' . selected($member_application_approved_email, $email['id'])  . '>';
                            $option .= $email['name'];
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