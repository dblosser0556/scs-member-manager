<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://webmuehle.at
 * @since      1.0.0
 *
 * @package    scsmm
 * @subpackage scsmm/admin/partials
 */
?>

<?php
if (!current_user_can('manage_options')) {
    wp_die();
}




// handle the submit button
if (isset($_POST['submit'])) {

    // check to see if all data is enters
    if (
        !isset($_POST['subject-email']) ||
        !isset($_POST['to-email']) ||
        !isset($_POST['body'])
    ) {
        $warningmessage = __('You must have to, subject, message to send email', 'scsmm');
    } else {
        $to = $_POST['to-email'];
        $subject = $_POST['subject-email'];
        $body = $_POST['body'];

        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        if (isset($_POST['cc-email']) && !empty($_POST['cc-email'])) {
            $headers[] = 'Cc: ' . $_POST['cc-email'];
        }

        if (isset($_POST['bcc-email']) && !empty($_POST['bcc-email'])) {
            $headers[] = 'Bcc: ' . $_POST['bcc-email'];
        }
        if (isset($_POST['from-email'])) {
            $headers[] = 'From: ' . $_POST['from-email'];
        }

        $send = wp_mail($to, $subject, $body, $headers);

        if ($send) {
            $message = __('Email Successfully Sent', 'scsmm');
        } else {
            $warningmessage = __('Something went wrong', 'scsmm');
        }
    }
}

// handle the return button
if (isset($_POST['return'])) {
    if (isset($_POST['page'])) {
        wp_safe_redirect($_POST['page']);
    } else {
        wp_safe_redirect(get_home_url());
    }
}



// sometime the to list will be passed in by session varable
// sometimes it is passed in by request.

if (!session_id()) {
    session_start();
}

if (isset($_SESSION["to-email"])) {
    $to = $_SESSION["to-email"];
   
}

if (isset($_SESSION['from-page'])) {
    $fromurl = $_SESSION["from-page"];
}

$options = get_option('scsmm');


if (!isset($from)) {
    if (isset($_REQUEST['from'])) {
        $from = wp_unslash($_REQUEST['from']);
    } else {
        if (isset($options['email'])) {
            $from = $options['email'];
        } else {
            $from = '';
        }
    }
}


if (!isset($to)) {
    if (isset($_REQUEST['to'])) {
        $to = wp_unslash($_REQUEST['to']);
    } else {
        $to = '';
    }
}

if (!isset($subject)) {
    if (isset($_REQUEST['subject'])) {
        $subject = wp_unslash($_REQUEST['subject']);
    } else {
        $subject = '';
    }
}

if (!isset($body)) {
    $body = '';
}


?>

<div class="wrap">
    <h2><?php _e('Email Member', $this->plugin_name); ?></h2>
    <?php if (isset($message)) { ?>
    <div id="message" class="notice notice-success is-dismissible inline">
        <p><?= $message ?></p>
    </div>
    <?php
    } ?>
    <?php if (isset($warningmessage)) { ?>
    <div id="warningmessage" class="notice notice-warning is-dismissible inline">
        <p><?= $warningmessage ?></p>
    </div>
    <?php
    } ?>

    <form name="scsmm-mail-form" id="scsmm-mail-form" action="" method="post">
        <input type="hidden" name="page" value="<?= $fromurl ?>" />
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="from-email"><?= esc_html__("From", 'scsmm') ?></label>
                    </th>
                    <td>
                        <input class="regular-text" type="text" name="from-email" id="from-email" value="<?php if (!empty($from)) echo $from; ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="to-email"><?= esc_html__("To", 'scsmm') ?></label>
                    </th>
                    <td>
                        <input class="large-text" type="text" name="to-email" id="to-email" value="<?php if (!empty($to)) echo $to; ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="cc-email"><?= esc_html__("CC", 'scsmm') ?></label>
                    </th>
                    <td>
                        <input class="large-text" type="text" name="cc-email" id="cc-email" value="<?php if (!empty($cc)) echo $cc; ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="bcc-email"><?= esc_html__("BCC", 'scsmm') ?></label>
                    </th>
                    <td>
                        <input class="large-text" type="text" name="bcc-email" id="from-email" value="<?php if (!empty($bcc)) echo $bcc; ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="subject-email"><?= esc_html__("Subject", 'scsmm') ?></label>
                    </th>
                    <td>
                        <input class="large-text" type="text" name="subject-email" id="subject-email" value="<?php if (!empty($subject)) echo $subject; ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="body"><?= esc_html__("Message", 'scsmm') ?></label>
                    </th>
                    <td>
                        <?php wp_editor($body, 'body') ?>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <?php submit_button('Send Email', 'primary', 'submit', false, null); ?>
                        <?php submit_button('Return', 'secondary', 'return', false, null); ?>
                    </td>

                    </td>
                </tr>
            </tbody>

        </table>
    </form>

</div>