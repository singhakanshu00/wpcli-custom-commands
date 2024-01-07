<?php
/**
 * Autoloader class to autoload classes in the pmc-plugin package.
 *
 * @package PMC_Plugin
 */

namespace PMC_Plugin\Inc\Helper;

/**
 * Auto loader function.
 *
 * @param string $class_name Source namespace.
 *
 * @return void
 */
function autoload( $class_name ) {
	$class_name                           = str_replace( array( '\\', '_' ), array( '/', '-' ), $class_name );
	$class_name                           = strtolower( $class_name );
	$seperator                            = explode( '/', $class_name );
	$seperator[ count( $seperator ) - 1 ] = 'class-' . $seperator[ count( $seperator ) - 1 ];
	array_shift( $seperator );
	$class_name    = implode( '/', $seperator );
	$top_directory = PMC_PLUGIN_DIR;
	$base_path     = $top_directory . '/';
	$file_path     = $base_path . $class_name . '.php';

	if ( file_exists( $file_path ) ) {
		require_once $file_path;
	}
}

// Register the autoloader.
spl_autoload_register( 'PMC_Plugin\Inc\Helper\autoload' );
