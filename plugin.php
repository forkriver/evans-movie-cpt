<?php
/**
 * Plugin loader file.
 *
 * @package evans-movie-cpt
 */
 
/**
 * Plugin Name: Evans Movie Post Type
 * Description: Provide the post type and associated tools for the Evans site
 * Plugin URI:  http://patj.ca/wp/plugins/evans-movie-cpt
 * Version:     1.0.0
 * Author:      Patrick Johanneson
 * Author URI:  http://patrickjohanneson.com/
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /languages
 * Text Domain: evans-cpt
 */
require_once( 'class-evans-movie.php' );

/**
 * Create CPT and flush the rewrite rules.
 */
function evmc_plugin_turn_on() {
	Evans_Movie::create_cpt();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'evmc_plugin_turn_on' );

/**
 * Clear out the CPT from the rewrite rules.
 */
function evmc_plugin_turn_off() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'evmc_plugin_turn_off' );

/**
 * @todo Rotten Tomatoes API key (admin pages)
 * @todo TheMovieDB API key (admin pages)
 */

// local LAMP debugging stuff
if( file_exists(  plugin_dir_path( __FILE__ ) . 'local-stuff.php' ) ) {
	include_once( plugin_dir_path( __FILE__ ) . 'local-stuff.php' );
}

if( ! function_exists( '_dump' ) ) :
	function _dump( $x ) {
		if ( ! current_user_can( 'update_core' ) ) {
			return;
		}
		echo( '<pre>' . PHP_EOL );
		if( is_object( $x ) || is_array( $x ) ) {
			print_r( $x );
		} else {
			echo( $x );
		}
		echo( '</pre>' . PHP_EOL );
	}
endif;	// ! function_exists( '_dump' )

/**
 * Instantiate the class, yo
 */
new Evans_Movie();