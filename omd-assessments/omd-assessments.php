<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://openminds.com
 * @since             1.0.0
 * @package           Omd_Assessments
 *
 * @wordpress-plugin
 * Plugin Name:       OMD Assessments
 * Plugin URI:        https://omd-assessments
 * Description:       This is a description of the plugin.
 * Version:           1.0.0
 * Author:            Billy Fischbach
 * Author URI:        https://openminds.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       omd-assessments
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
define( 'OMD_ASSESSMENTS_VERSION', '1.0.0' );
define( 'OMD_ASSESSMENTS_URL', plugin_dir_url( __FILE__ ) );
define( 'OMD_ASSESSMENTS_DIR', plugin_dir_path( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-omd-assessments-activator.php
 */
function activate_omd_assessments() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-omd-assessments-activator.php';
	Omd_Assessments_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-omd-assessments-deactivator.php
 */
function deactivate_omd_assessments() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-omd-assessments-deactivator.php';
	Omd_Assessments_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_omd_assessments' );
register_deactivation_hook( __FILE__, 'deactivate_omd_assessments' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-omd-assessments.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_omd_assessments() {

	$plugin = new Omd_Assessments();
	$plugin->run();

}
run_omd_assessments();
