<?php

/**
 * The Evans_Movie object.
 */
class Evans_Movie {

	const POST_TYPE = 'evans_movie';
	const PREFIX = '_evans_';

	function __construct() {
		add_action( 'init', array( $this, 'create_cpt' ) );
		add_action( 'init', array( $this, 'cmb_init' ), PHP_INT_MAX );

		add_filter( 'cmb_meta_boxes', array( $this, 'metaboxes' ) );
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
			'supports' => array('title','editor','thumbnail','comments'),
		);
		register_post_type( self::POST_TYPE, $args );
	}

	function cmb_init() {
		if( ! class_exists( 'CMB_Meta_Box' ) ) {
			require_once( 'lib/custom-meta-boxes/custom-meta-boxes.php' );
		}
	}

	function metaboxes( $metaboxes = array() ) {
		$prefix = self::PREFIX;

		$url_group = array(
			array(
				'id' => 'url',
				'name' => __( 'URL', 'evans-cpt' ),
				'type' => 'text_url',
				'cols' => 6,
			),
			array( 
				'id' => 'url_name',
				'name' => __( 'Link text', 'evans-cpt' ),
				'type' => 'text',
				'cols' => 6,
			),

		);
		$fields = array(
			// Showtime(s)
			array(
				'id' => 'showtime',
				'name' => __( 'Showtimes', 'evans-cpt' ),
				'type' => 'datetime_unix',
				'repeatable' => true,
				'repeatable_max' => 5,
				'sortable' => true,
			),

			// URL group (official, IMDB, ... )
			array(
				'id' => 'url',
				'name' => __( 'URL(s)', 'evans-cpt' ),
				'type' => 'group',
				'fields' => $url_group,
				'repeatable' => true,
			)

		);

		$metaboxes[] = array(
			'id' => $prefix . 'movie_meta',
			'title' => __( 'Movie Meta', 'evans-cpt' ),
			'fields' => $fields,
			'pages' => array( self::POST_TYPE ),
			'priority' => 'high',

		);

		return $metaboxes;

	}
	
}