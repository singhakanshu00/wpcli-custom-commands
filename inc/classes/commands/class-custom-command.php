<?php
/**
 * Class file for custom CLI command.
 *
 * @package PMC_Plugin
 */

namespace PMC_Plugin\Inc\Classes\Commands;

use WP_CLI;
use PMC_Plugin\Inc\Classes\Commands\Command_Helper;

/**
 * Custom CLI command to set post categories and count images.
 */
class Custom_Command extends \WPCOM_VIP_CLI_Command {

	/**
	 * Constructor function for the class.
	 *
	 * @return void
	 */
	public function __construct() {
		// Register the CLI command.
		WP_CLI::add_command( 'category', 'PMC_Plugin\Inc\Classes\Commands\Custom_Command' );
	}

	/**
	 * Set post categories to pmc(parent)->rollingstone(child) and count images in post content.
	 *
	 * ## OPTIONS
	 *
	 * [--post-type=<post_type>]
	 * : The post type to update categories. Default is 'post'.
	 *
	 * ## EXAMPLES
	 *
	 * wp category set_post_categories --post-type=post
	 *
	 * @param array $args Command arguments.
	 * @param array $assoc_args Command associative arguments.
	 */
	public function set_post_categories( $args, $assoc_args ) {
		$post_type = isset( $assoc_args['post-type'] ) ? $assoc_args['post-type'] : 'post';

		$parent_category_old = 'rtCamp';
		$child_category_old  = 'engineering';
		$parent_category_new = 'pmc';
		$child_category_new  = 'rollingstone';

		// Get the category term IDs for rtcamp (parent) and engineering (child).
		$parent_cat_id_old = wpcom_vip_term_exists( $parent_category_old, 'category' );
		$child_cat_id_old  = wpcom_vip_term_exists( $child_category_old, 'category', $parent_cat_id_old );
		$parent_cat_id_new = wpcom_vip_term_exists( $parent_category_new, 'category' );
		$child_cat_id_new  = wpcom_vip_term_exists( $child_category_new, 'category', $parent_cat_id_new );

		$args = array(
			'post_type'      => $post_type,
			'posts_per_page' => 100,
			'paged'          => 1,
			'no_found_rows'  => true,
		);

		$posts_fetched = new \WP_Query( $args );

		if ( $posts_fetched->have_posts() ) {
			while ( $posts_fetched->have_posts() ) {
				$posts_fetched->the_post();

				$post_id = get_the_ID();

				// Setting category.
				Command_Helper::set_category( $post_id, $child_category_new, $parent_category_new, $child_cat_id_new, $parent_cat_id_new );

				// Setting image count.
				Command_Helper::set_image_count( $post_id );

				// Check if it's time to fetch the next page.
				if ( $posts_fetched->current_post + 1 === $posts_fetched->post_count ) {
					// Fetch the next page of posts.
					++$args['paged'];
					$posts_fetched = new \WP_Query( $args );
				}
			}
			WP_CLI::success( __( 'Categories updated and image counts added for posts', 'pmc-plugin' ) );
		} else {
			WP_CLI::error( __( 'No Posts found', 'pmc-plugin' ) );
		}
	}
}
