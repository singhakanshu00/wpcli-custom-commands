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

	$namespace_root = 'PMC_Plugin\\';
	$resource       = trim( $class_name, '\\' );

	if ( empty( $resource ) || strpos( $resource, '\\' ) === false || strpos( $resource, $namespace_root ) !== 0 ) {
		// Not our namespace, bail out.
		return;
	}

	// Remove our root namespace.
	$resource = str_replace( $namespace_root, '', $resource );

	$path = explode(
		'\\',
		str_replace( '_', '-', strtolower( $resource ) )
	);

	if ( 'inc' === $path[0] ) {

		switch ( $path[1] ) {
			case 'traits':
				$path[ count( $path ) - 1 ] = 'trait-' . $path[ count( $path ) - 1 ];
				break;

			case 'classes':
				$path[ count( $path ) - 1 ] = 'class-' . $path[ count( $path ) - 1 ];
				break;
			default:
				$path[ count( $path ) - 1 ] = 'class-' . $path[ count( $path ) - 1 ];
				break;
		}
	}

	$class_name    = implode( '/', $path );
	$top_directory = PMC_PLUGIN_DIR;
	$base_path     = $top_directory . '/';
	$file_path     = $base_path . $class_name . '.php';

	if ( file_exists( $file_path ) ) {
		require_once $file_path;
	}
}

// Register the autoloader.
spl_autoload_register( 'PMC_Plugin\Inc\Helper\autoload' );
