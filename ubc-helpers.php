<?php

	/**
	 *
	 * @wordpress-plugin
	 * Plugin Name:       UBC Helpers
	 * Plugin URI:        http://ctlt.ubc.ca/
	 * Description:       A set of utility functions which we can openly use in our other plugins to help maintainability.
	 * Version:           1.0.0
	 * Author:            Richard Tape
	 * Author URI:        http://richardtape.com/
	 * License:           GPL-2.0+
	 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
	 * Text Domain:       ubc-helpers
	 */

	// If this file is called directly, abort.
	if ( ! defined( 'WPINC' ) ) {
		die;
	}

	if( class_exists( 'UBC_Helpers' ) ){
		return;
	}

	class UBC_Helpers
	{

		/**
		 * Method to allow including of a template part from within a plugin. Replicates what locate_template() does in
		 * WordPress core, but allows you to specify the path where to start looking. Falls back to looking in the
		 * 
		 *
		 * @since 0.1
		 *
		 * @param string $startPath The path - probably set via a constant - of where to start looking
		 * @param string|array $templateNames Template file(s) to search for, in order.
		 * @param bool $load If true the template file will be loaded if it is found.
		 * @param bool $requireOnce Whether to require_once or require. Default true. Has no effect if $load is false.
		 * @return string The template filename if one is located.
		 */

		public static function locateTemplateInPlugin( $startPath, $templateNames, $load = false, $requireOnce = true )
		{

			$located = '';

			foreach( (array) $templateNames as $templateName )
			{

				if( !$templateName ){
					continue;
				}

				if( file_exists( untrailingslashit( $startPath ) . '/' . $templateName ) )
				{

					$located =  untrailingslashit( $startPath ) . '/' . $templateName;
					break;

				}
				else if( file_exists( untrailingslashit( STYLESHEETPATH ) . '/' . $templateName ) )
				{
					$located = untrailingslashit( STYLESHEETPATH ) . '/' . $templateName;
					break;

				}
				else if( file_exists( untrailingslashit( TEMPLATEPATH ) . '/' . $templateName ) )
				{

					$located = untrailingslashit( TEMPLATEPATH ) . '/' . $templateName;
					break;
				
				}
			
			}

			if( $load && '' != $located ){
				load_template( $located, $require_once );
			}

			return $located;

		}/* locateTemplateInPlugin() */


		/**
		 * Method to fetch, rather than echo, the contents of a template file
		 * Uses output buffering. Bad times, but at the moment, there's no better way to do this neatly.
		 *
		 * @since 0.1
		 *
		 * @param string $path - the full absolute URL to the template part
		 * @return string $content - the content of that template
		 */

		public static function fetchTemplatePart( $path, $data = false )
		{

			if( !is_array( $data ) ){
				$data = array( $data );
			}

			ob_start();

			include( $path );

			$content = ob_get_contents();

			ob_end_clean();

			return $content;

		}/* fetchTemplatePart() */

	}/* class UBC_Helpers */



	add_action( 'plugins_loaded', 'plugins_loaded__registerUBCHelpers' );

	function plugins_loaded__registerUBCHelpers()
	{

		global $UBC_Helpers;
		$UBC_Helpers = new UBC_Helpers();

	}/* plugins_loaded__registerUBCHelpers() */

?>