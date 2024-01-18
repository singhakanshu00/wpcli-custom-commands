<?php
/**
 * Class file for custom CLI command.
 *
 * @package PMC_Plugin
 */

namespace PMC_Plugin\Inc\Classes\Commands;

use WP_CLI;
use PMC_Plugin\Inc\Classes\Commands\Command_Helper;
use PMC_Plugin\Inc\Traits\Singleton;

/**
 * Custom CLI command to set post categories and count images.
 */
class Custom_Command extends \WPCOM_VIP_CLI_Command {

	use Singleton;

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
	 * [--post-per-page=<posts_per_page>]
	 * : The number of posts to retrieve per batch. Default is 100.
	 *
	 * ## EXAMPLES
	 *
	 * wp category set_post_categories --post-type=post --post-per-page=100
	 *
	 * @param array $args Command arguments.
	 * @param array $assoc_args Command associative arguments.
	 */
	public function set_post_categories( $args, $assoc_args ) {

		$this->start_bulk_operation();

		$post_type     = isset( $assoc_args['post-type'] ) ? $assoc_args['post-type'] : 'post';
		$post_per_page = isset( $assoc_args['post-per-page'] ) ? $assoc_args['post-per-page'] : 100;

		$parent_category_new = __( 'pmc', 'pmc-plugin' );
		$child_category_new  = __( 'rollingstone', 'pmc-plugin' );

		$args = array(
			'post_type'              => $post_type,
			'posts_per_page'         => $post_per_page,
			'fields'                 => 'ids',
			'paged'                  => 1,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false
		);

		$posts_fetched = new \WP_Query( $args );

		// Get the category term IDs for rtcamp (parent) and engineering (child).
		$parent_cat_id_new = wpcom_vip_term_exists( $parent_category_new, 'category' );
		$child_cat_id_new  = wpcom_vip_term_exists( $child_category_new, 'category', $parent_cat_id_new );
		$new_term          = false;

		// If $parent_cat_id_new is not provided or is not a valid term, creating it.
		if ( ! $parent_cat_id_new || ! term_exists( $parent_category_new, 'category' ) ) {
			$parent_cat_id_new = wp_insert_term( $parent_category_new, 'category' );
			WP_CLI::line( __( 'Success: Created parent category.', 'pmc-plugin' ) );
			$new_term = true;
		}
	
		// If $child_cat_id_new is not provided or is not a valid term, creating it.
		if ( ! $child_cat_id_new || ! term_exists( $child_category_new, 'category', $parent_cat_id_new['term_id'] ) ) {
			$child_cat_id_new = wp_insert_term( $child_category_new, 'category', array( 'parent' => intval( $parent_cat_id_new['term_id'] ) ) );
			WP_CLI::line( __( 'Success: Created child category.', 'pmc-plugin' ) );
			$new_term = true;
		}

		if ( ! $new_term ) {
			WP_CLI::line( __( 'Info: "pmc" and "rollingstone" Categories already exist.', 'pmc-plugin' ) );
		}

		if ( $posts_fetched->have_posts() ) {
			while ( $posts_fetched->have_posts() ) {
				$posts_fetched->the_post();

				$post_id = get_the_ID();

				WP_CLI::line( __( 'Processing post: ', 'pmc-plugin' ) . $post_id );

				// Setting category.
				wp_set_object_terms( $post_id, array( intval( $parent_cat_id_new['term_id'] ), intval( $child_cat_id_new['term_id'] ) ), 'category' );

				WP_CLI::line( __( 'Terms updated', 'pmc-plugin' ) );

				// Setting image count.
				Command_Helper::set_image_count( $post_id );

				WP_CLI::line( __( 'Image count meta data is set', 'pmc-plugin' ) );

				$this->vip_inmemory_cleanup();

				// Check if it's time to fetch the next page.
				if ( $posts_fetched->current_post + 1 === $posts_fetched->post_count ) {
					// Fetch the next page of posts.
					WP_CLI::line( __( 'Pausing for a breathe..' ) );
					sleep( 2 );
					++$args['paged'];
					$posts_fetched = new \WP_Query( $args );
				}
			}
			wp_reset_postdata();
			WP_CLI::success( __( 'Categories updated and image counts added for posts', 'pmc-plugin' ) );
		} else {
			WP_CLI::error( __( 'No Posts found', 'pmc-plugin' ) );
		}

		$this->end_bulk_operation();
	}
}
