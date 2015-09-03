<?php
namespace UBC;
/**
 * Plugin Name:     UBC Helpers
 * Plugin URI:		http://ctlt.ubc.ca/
 * Description:     A set of utility functions which we can openly use in our other plugins to help maintainability.
 * Version:         0.2.0
 * Author:			Richard Tape
 * Author URI:		http://blogs.ubc.ca/mbcx9rvt
 * License:	        GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     ubc-helpers
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Helpers {

	/**
	 * Method to allow including of a template part from within a plugin. Replicates what locate_template() does in
	 * WordPress core, but allows you to specify the path where to start looking. Falls back to looking in the
	 *
	 * @since 1.0.0
	 *
	 * @param string $startPath The path - probably set via a constant - of where to start looking
	 * @param string|array $templateNames Template file(s) to search for, in order.
	 * @param bool $load If true the template file will be loaded if it is found.
	 * @param bool $requireOnce Whether to require_once or require. Default true. Has no effect if $load is false.
	 * @return string The template filename if one is located.
	 */

	public static function locate_template_part_in_plugin( $startPath, $templateNames, $load = false, $requireOnce = true ) {

		$located = '';

		foreach ( (array) $templateNames as $templateName ) {

			if ( ! $templateName ) {
				continue;
			}

			if ( file_exists( untrailingslashit( $startPath ) . '/' . $templateName ) ) {

				$located = untrailingslashit( $startPath ) . '/' . $templateName;
				break;

			} else if ( file_exists( untrailingslashit( STYLESHEETPATH ) . '/' . $templateName ) ) {
				$located = untrailingslashit( STYLESHEETPATH ) . '/' . $templateName;
				break;

			} else if ( file_exists( untrailingslashit( TEMPLATEPATH ) . '/' . $templateName ) ) {

				$located = untrailingslashit( TEMPLATEPATH ) . '/' . $templateName;
				break;

			}
		}

		if ( $load && '' !== $located ) {
			load_template( $located, $require_once );
		}

		return $located;

	}/* locate_template_part_in_plugin() */


	/**
	 * Method to fetch, rather than echo, the contents of a template file
	 * Uses output buffering. Bad times, but at the moment, there's no better way to do this neatly.
	 *
	 * @since 1.0.0
	 *
	 * @param string $path - the full absolute URL to the template part
	 * @param array $data - Any data to make available to the template part
	 * @return string $content - the content of that template
	 */

	public static function fetch_template_part( $path, $data = false ) {

		// If we have any data, ensure it's an array
		if ( $data && ! is_array( $data ) ) {
			$data = array( $data );
		}

		ob_start();

		include( $path );

		$content = ob_get_contents();

		ob_end_clean();

		return $content;

	}/* fetchTemplatePart() */



	/**
	 * Fetch tags for a post and outputs them as a string
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $post_id - The ID of a post
	 * @param (string) $property - Which proprty of the tag object to fetch (name|slug|id etc.)
	 * @param (string) $separator - What to separate multiple tags with
	 * @return (string) The tags separated by $separator
	 */

	public static function get_plain_tags( $post_id = null, $property = 'name', $separator = ' ' ) {

		// Sanitize input
		$post_id 	= absint( $post_id );
		$property 	= sanitize_title_with_dashes( $property );
		$separator	= wp_kses_post( $separator );

		// If no post ID passed, assume in the loop
		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}

		// Start fresh
		$htmlstr 	= '';
		$posttags 	= get_the_tags( $post_id );

		if ( ! $posttags ) {
			return $htmlstr;
		}

		foreach ( $posttags as $key => $tag ) {
			$htmlstr .= $tag->$property . $separator;
		}

		return esc_html( $htmlstr );

	}/* get_plain_tags() */



	/**
	 * Fetches all $property of all terms for all taxonomies for the post
	 * type of the post
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $post_id - The ID of a post
	 * @param (string) $property - Which proprty of the term object to fetch (name|slug|id etc.)
	 * @param (string) $separator - What to separate multiple terms with
	 * @return (string) The terms separated by $separator
	 */

	public static function get_plain_terms( $post_id = null, $property = 'slug', $separator = ' ' ) {

		// Sanitize input
		$post_id 	= absint( $post_id );
		$property 	= sanitize_title_with_dashes( $property );
		$separator	= wp_kses_post( $separator );

		// If no post ID passed, assume in the loop
		if ( empty( $post_id ) ) {

			global $post;
			$post_id = $post->ID;
		}

		$post_type 	= get_post_type( $post_id );
		$taxonomies = get_object_taxonomies( $post_type, 'objects' );

		$outterm = '';

		foreach ( $taxonomies as $taxonomy_slug => $taxonomy ) {

			$terms = get_the_terms( $post_id, $taxonomy_slug );

			if ( empty( $terms ) ) {
				continue;
			}

			foreach ( $terms as $term ) {
				$outterm .= $term->$property . $separator;
			}
		}

		return esc_html( $outterm );

	}/* get_plain_terms() */



	/**
	 * Fetch the current platform set as a define in the wp-config.php file
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return bool|string the defined platform
	 */

	public static function get_current_platform() {

		if ( ! defined( 'CTLT_PLATFORM' ) ) {
			return 'Unknown Platform. Define CTLT_PLATFORM in wp-config.php';
		}

		return CTLT_PLATFORM;

	}/* get_current_platform() */


	/**
	 * Fetch the curent environment (verf, prod, local) set as a define in the wp-config.php file
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return bool|string the defined environment
	 */

	public static function get_current_environment() {

		if ( ! defined( 'CTLT_ENVIRONMENT' ) ) {
			return 'Unknown Environment. Define CTLT_ENVIRONMENT in wp-config.php';
		}

		return CTLT_ENVIRONMENT;

	}/* get_current_environment() */


}/* class UBC_Helpers */


// Fire up the helpers nice and early
add_action( 'plugins_loaded', '\UBC\plugins_loaded__register_ubc_helpers', 2 );


/**
 * Instantiate our plugin helpers
 *
 * @since 1.0.0
 *
 * @param null
 * @return null
 */

function plugins_loaded__register_ubc_helpers() {

	global $UBC_Helpers;
	$UBC_Helpers = new \UBC\Helpers();

}/* plugins_loaded__registerUBCHelpers() */
