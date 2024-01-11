<?php
/**
 * Plugin manifest class.
 *
 * @package PMC_Plugin
 */

namespace PMC_Plugin\Inc\Classes;

use PMC_Plugin\Inc\Traits\Singleton;
use PMC_Plugin\Inc\Classes\Commands\Custom_Command;



/**
 * Class Plugin
 */
class Plugin {

	use Singleton;

	/**
	 * Construct method.
	 */
	protected function __construct() {

		// Load plugin classes.
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			Custom_Command::get_instance();
		}
	}
}
