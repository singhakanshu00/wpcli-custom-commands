<?php
/**
 * Plugin Name: PMC Plugin
 * Description: All backend functionality will take place in this plugin.
 *              Like, creating custom cli commands.
 * Plugin URI:  https://rtcamp.com
 * Author:      rtCamp
 * Author URI:  https://rtcamp.com
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Version:     1.0
 * Text Domain: pmc-plugin
 *
 * @package PMC_Plugin
 */

/**
 * Importing autoloading file.
 */
require_once __DIR__ . '/inc/helper/autoloader.php';

define( 'PMC_PLUGIN_DIR', __DIR__ );

use PMC_Plugin\Inc\Classes\Commands\Custom_Command;

// Register CLI command to set post categories and count images.
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	new Custom_Command();
}
