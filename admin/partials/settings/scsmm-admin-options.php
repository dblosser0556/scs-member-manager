<form action="options.php" name="member-manager-options" method="post">
    <?php settings_fields($this->plugin_name); ?>
    <?php
    // grab all the options to set save values
    $settings = get_option($this->plugin_name);

    $email = $settings['email'];
    $registration_check_page = $settings['registration-check-page'];
    $registration_check_redirect_page = $settings['registration-check-redirect-page'];
    $registration_expired_redirect_page = $settings['registration-expired-redirect-page'];
    $registration_not_found_redirect_page = $settings['registration-not-found-redirect-page'];
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
                        $pages = get_pages();
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
                        $pages = get_pages();
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
                        $pages = get_pages();
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
                        $pages = get_pages();
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