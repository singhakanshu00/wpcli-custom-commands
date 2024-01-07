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
	 * Sets the category for a given post.
	 *
	 * @param int    $post_id The ID of the post.
	 * @param string $child_category_new The name of the new child category.
	 * @param string $parent_category_new The name of the new parent category.
	 * @param int    $child_cat_id_new The ID of the new child category.
	 * @param int    $parent_cat_id_new The ID of the new parent category.
	 * @param string $child_category_old The name of the old child category.
	 * @param int    $child_cat_id_old The ID of the old child category.
	 * @return void
	 */
	public static function set_category( $post_id, $child_category_new, $parent_category_new, $child_cat_id_new, $parent_cat_id_new, $child_category_old, $child_cat_id_old ) {

		if ( ! has_term( $child_category_new, 'category', $post_id ) ) {
			// Check if parent category exists, create if not.
			if ( ! $parent_cat_id_new ) {
				$parent_cat_id_new = wp_insert_term( $parent_category_new, 'category' );
			}

			// Check if child category exists, create if not.
			if ( ! $child_cat_id_new ) {
				$child_cat_id_new = wp_insert_term( $child_category_new, 'category', array( 'parent' => $parent_cat_id_new['term_id'] ) );
			}

			wp_set_post_categories( $post_id, array( $child_cat_id_new['term_id'] ), true );

			if ( has_term( $child_category_old, 'category', $post_id ) ) {
				// Remove the old engineering category term from the post.
				wp_remove_object_terms( $post_id, intval( $child_cat_id_old['term_id'] ), 'category' );
			}
		}
	}

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
		$content              = get_the_content();

		$image_count       = preg_match_all( '/<img [^>]+>/', $content, $matches );
		$total_image_count = $featured_image_count + $image_count;

		update_post_meta( $post_id, '_pmc_image_counts', $total_image_count );
	}
}
