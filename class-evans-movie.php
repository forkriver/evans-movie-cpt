<?php

/**
 * The Evans_Movie object.
 */
class Evans_Movie {

	const POST_TYPE = 'evans_movie';
	const PREFIX = '_evans_';

	function __construct() {
		add_action( 'init', array( $this, 'create_cpt' ) );	
		
		add_action( 'cmb2_init', array( $this, 'metaboxen' ) );

		add_action( 'admin_notices', array( $this, 'check_for_cmb' ) );
	}

	/**
	 * Create the custom post type.
	 */
	function create_cpt() {
		// create custom post type for "movie"
		$args = array(
			'public' => true,
			'labels' => array(
				'name'			=> __('Movies'),
				'singular_name'	=> __('Movie'),
			),
			'has_archive'	=> true,
			'rewrite'		=> array('slug' => 'movies'),
			'supports' => array('title','editor','thumbnail','custom-fields', 'comments', 'excerpt'),
		);
		register_post_type( self::POST_TYPE, $args );
	}

	/**
	 * Check to see if the CMB2 plugin is installed and active.
	 */
	function check_for_cmb() {
		if( ! function_exists( 'new_cmb2_box' ) ) {
			$class = "update-nag";
			$message = "Please install and/or activate the CMB2 plugin for full functionality.";
	        echo"<div class=\"$class\"> <p>$message</p></div>"; 
		}
	}


	/**
	* Hook in and add a demo metabox. Can only happen on the 'cmb2_init' hook.
	*/
	function metaboxen() {
		// Start with an underscore to hide fields from custom fields list
		$prefix = self::PREFIX;

		$args = array(
			'id' => $prefix . 'movie_metaboxen',
			'title' => __( 'Movie Details', 'evans-movie' ),
			'object_types' => array( self::POST_TYPE ),
			'show_names' => true,
		);

		$meta_boxes = new_cmb2_box( $args );

		//FIELDS:
		// Showtimes (repeatable) X
		// Official URL X 
		// Rotten Tomatoes ID
		// IMDB ID
		// others?

		$meta_boxes->add_field( array(
			'name' => __( 'Showtimes', 'evans-movie' ),
			'desc' => '',
			'id' => $prefix . 'showtimes',
			'type' => 'text_datetime_timestamp',
			'repeatable' => true,
			)	
		);
		$meta_boxes->add_field(
			array(
				'id' => $prefix . 'official_url',
				'name' => __( 'Official URL', 'evans-movie' ),
				'type' => 'text_url',
			)
		);

	}
	
}