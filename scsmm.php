<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/dblosser0556
 * @since             1.0.0
 * @package           Scsmm
 *
 * @wordpress-plugin
 * Plugin Name:       Simple Club Member Manager
 * Plugin URI:        https://github.com/dblosser0556/scs_member_manager
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            David Blosser
 * Author URI:        https://github.com/dblosser0556
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       scsmm
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_VERSION', '1.0.0' );

define( 'PLUGIN_NAME', 'scs-member-manager' );

define('PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

define('PLUGIN_URL', plugin_dir_url( __FILE__ ) );

define('PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

define('PLUGIN_TEXT_DOMAIN', 'scsmm');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-scsmm-activator.php
 */
function activate_scsmm() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-scsmm-activator.php';
	Scsmm_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-scsmm-deactivator.php
 */
function deactivate_scsmm() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-scsmm-deactivator.php';
	Scsmm_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_scsmm' );
register_deactivation_hook( __FILE__, 'deactivate_scsmm' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-scsmm.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_scsmm() {

	$plugin = new Scsmm();
	$plugin->run();

}
run_scsmm();
