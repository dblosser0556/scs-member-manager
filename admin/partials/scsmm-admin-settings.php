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
    if ($active_tab == 'redirect_options') { 
        require_once PLUGIN_DIR . 'admin/partials/settings/scsmm-admin-options.php';
    } else if ($active_tab == 'membership_options') {
        require_once PLUGIN_DIR . 'admin/partials/settings/scsmm-admin-membership-type-list.php';
    } else if ($active_tab == 'relationship_options') {
        require_once PLUGIN_DIR . 'admin/partials/settings/scsmm-admin-relationship-type-list.php';
    } else if ($active_tab == 'status_options') {
        require_once PLUGIN_DIR . 'admin/partials/settings/scsmm-admin-status-list.php';
    }    else if ($active_tab == 'email_options') {
            require_once PLUGIN_DIR . 'admin/partials/settings/scsmm-admin-email-templates-list.php';
    }  ?>
</div>