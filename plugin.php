<?php
/**
 * Plugin Name: Evans Movie Post Type
 * Description: Provide the post type and associated tools for the Evans site
 * Plugin URI:  http://patj.ca/wp/plugins/evans-movie-cpt
 * Version:     1.0
 * Author:      Patrick Johanneson
 * Author URI:  http://patrickjohanneson.com/
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /languages
 * Text Domain: evans-cpt
 */
require_once( 'class-evans-movie.php' );

/**
 * @todo Activation & deactivation hooks that flush rewrite rules
 * @todo Rotten Tomatoes API key (admin pages)
 * @todo TheMovieDB API key (admin pages)
 */

new Evans_Movie();