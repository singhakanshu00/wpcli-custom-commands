<?php
/**
 * Helper class for managing post categories within PMC Plugin.
 *
 * @package PMC_Plugin
 */

namespace PMC_Plugin\Inc\Classes\Commands;

/**
 * Class Command_Helper
 */
class Command_Helper {

	/**
	 * Sets the image count for a given post.
	 *
	 * This function counts the number of images in the post content
	 * and updates the post meta with the total image count.
	 *
	 * @param int $post_id The ID of the post.
	 *
	 * @return void
	 */
	public static function set_image_count( $post_id ) {

		// Count images in post content and update post meta.
		$featured_image_count = has_post_thumbnail( $post_id ) ? 1 : 0;
		$content              = get_post_field( 'post_content', $post_id );

		// Case: Image or Gallery block.
		$image_count_block = substr_count( $content, '<img ' );

		// Case: Gallery Shortcode.
		$image_count_shortcode = 0;

		if ( preg_match_all( '/' . get_shortcode_regex() . '/s', $content, $matches, PREG_SET_ORDER ) ) {
			foreach ( $matches as $shortcode ) {
				$gallery = do_shortcode( $shortcode[0] );

				$image_count_shortcode += substr_count( $gallery, '<img ' );
			}
		}

		$total_image_count = $featured_image_count + $image_count_block + $image_count_shortcode;

		\WP_CLI::line( __( 'Image Count: ', 'pmc-plugin' ) . $total_image_count );

		update_post_meta( $post_id, '_pmc_image_counts', $total_image_count );
	}
}
