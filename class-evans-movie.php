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
		$labels = array(
			'name'                => _x( 'Movie', 'Post Type General Name', 'evans-cpt' ),
			'singular_name'       => _x( 'Movie', 'Post Type Singular Name', 'evans-cpt' ),
			'menu_name'           => __( 'Movie', 'evans-cpt' ),
			'name_admin_bar'      => __( 'Movie', 'evans-cpt' ),
			'parent_item_colon'   => __( 'Parent Item:', 'evans-cpt' ),
			'all_items'           => __( 'All Movies', 'evans-cpt' ),
			'add_new_item'        => __( 'Add New Movie', 'evans-cpt' ),
			'add_new'             => __( 'Add New', 'evans-cpt' ),
			'new_item'            => __( 'New Movie', 'evans-cpt' ),
			'edit_item'           => __( 'Edit Movie', 'evans-cpt' ),
			'update_item'         => __( 'Update Movie', 'evans-cpt' ),
			'view_item'           => __( 'View Movie', 'evans-cpt' ),
			'search_items'        => __( 'Search Movies', 'evans-cpt' ),
			'not_found'           => __( 'Not found', 'evans-cpt' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'evans-cpt' ),
		);
		$args = array(
			'label'               => __( 'Movies', 'evans-cpt' ),
			'description'         => __( 'Movies', 'evans-cpt' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'show_in_admin_bar'   => true,	
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
		);
		register_post_type( 'evans_movie', $args );
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