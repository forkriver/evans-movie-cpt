<?php

/**
 * The Evans_Movie object.
 */
class Evans_Movie {

	/**
	 * Set the constants.
	 */
	const POST_TYPE = 'evans_movie';
	const PREFIX = '_evans_';

	/**
	 * Create the class.
	 */
	function __construct() {
		add_action( 'init', array( $this, 'create_cpt' ) );
		add_action( 'init', array( $this, 'cmb_init' ), PHP_INT_MAX );
		add_action( 'init', array( $this, 'fix_showtimes' ) );

		add_action( 'after_setup_theme', array( $this, 'featured_image_size' ) );

		add_filter( 'cmb_meta_boxes', array( $this, 'metaboxes' ) );

		// Filters for the front page
		add_filter( 'the_content', array( $this, 'front_page_content' ) );

		/**
		 * Rewrite stuff
		 */
		add_action( 'init', array( $this, 'add_rewrites' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );

		add_filter( 'template_include', array( $this, 'template_selector' ) );

	}

	/**
	 * Create the custom post type.
	 */
	function create_cpt() {

		$rewrite = array(
			'slug' => 'movie',
			);
		$labels = array(
			'name'                => _x( 'Movies', 'Post Type General Name', 'evans-cpt' ),
			'singular_name'       => _x( 'Movie', 'Post Type Singular Name', 'evans-cpt' ),
			'menu_name'           => __( 'Movies', 'evans-cpt' ),
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
			'rewrite'             => $rewrite,
		);
		register_post_type( self::POST_TYPE, $args );

	}

	function featured_image_size() {
		// Register the post_thumbnail size for the hero image.
		$no_crop = false;	// More semantically useful
		add_image_size( self::POST_TYPE . '_hero', 960, 9999, $no_crop );
	}

	/**
	 * Initialize the CMBs.
	 */
	function cmb_init() {
		if( ! class_exists( 'CMB_Meta_Box' ) ) {
			require_once( 'lib/custom-meta-boxes/custom-meta-boxes.php' );
		}
	}

	/**
	 * Add metaboxes to the POST_TYPE
	 * @param array $metaboxes
	 * @return array $metaboxes
	 */
	function metaboxes( $metaboxes = array() ) {
		$prefix = self::PREFIX;

		$url_group = array(
			array(
				'id' => $prefix . 'url',
				'name' => __( 'URL', 'evans-cpt' ),
				'type' => 'text_url',
				'cols' => 6,
			),
			array( 
				'id' => $prefix . 'url_name',
				'name' => __( 'Link text', 'evans-cpt' ),
				'type' => 'text',
				'cols' => 6,
			),

		);
		$fields = array(
			// Showtime(s)
			array(
				'id' => $prefix . 'showtime',
				'name' => __( 'Showtimes', 'evans-cpt' ),
				'type' => 'datetime_unix',
				'repeatable' => true,
				'repeatable_max' => 5,
				'sortable' => true,
			),

			// URL group (official, IMDB, ... )
			array(
				'id' => $prefix . 'url',
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

	/**
	 * Switch any showtimes to time() format.
	 */
	function fix_showtimes() {
		$already_done = get_option( Evans_Movie::PREFIX . 'dates_fixed' );
		if( $already_done ) {
			return;
		}
		$args = array(
			'numberposts' => -1,
			'post_type' => Evans_Movie::POST_TYPE,
			);
		$movies = get_posts( $args );
		if ( $movies && is_array( $movies ) ) {
			foreach( $movies as $movie ) {
				$showtimes = get_post_meta( $movie->ID, Evans_Movie::PREFIX . 'showtime' );
				$new = array();
				foreach( $showtimes as $s ) {
					if( ! is_numeric( $s ) ) {
						$new[] = strtotime( $s );
					}
				}
				if( ! empty( $new ) ) {
					update_post_meta( $movie->ID, Evans_Movie::PREFIX . 'showtime', $new );
				}
			}
		}
		update_option( Evans_Movie::PREFIX . 'dates_fixed', time() );

	}

	/**
	 * Filter the front page to display the next upcoming movie.
	 * @param string $content
	 * @return string The filtered content.
	 * @todo Generate content when there are no upcoming movies (eg, summer or Xmas break).
	 */
	function front_page_content( $content ) {

		if( is_front_page() ) {

			$movie = $this->get_next_movie();

			if( $movie->have_posts() ) {
				$movie->the_post();
				$movie_url = get_the_permalink( get_the_ID() );
				$content = '';
				$content .= '<div class="movie">' . PHP_EOL;
				$content .= '<a href="' . $movie_url . '">';
				$content .= get_the_post_thumbnail( get_the_ID(), self::POST_TYPE . '_hero' );
				/**
				 * @todo -- remove the 'height' and 'width' from the returned <img /> tag -- fingers crossed for a filter
				 */
				$content .= '</a>' . PHP_EOL;

				$content .= '<div class="showtimes">' . PHP_EOL;
				$content .= '<h1 class="movie-title"><a href="' . $movie_url . '">' . get_the_title() . '</a></h1>'. PHP_EOL;
				$times = get_post_meta( get_the_ID(), self::PREFIX . 'showtime' );
				if( $times ) {
					// Let's just make sure they're in the right order
					sort( $times );
					$content .= '<p class="times">' . PHP_EOL;
					foreach( $times as $time ) {
						$content .= date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $time );
						if( $time != end( $times ) ) {
							$content .= ' | ';
						}
					}
					$content .= '</p><!-- .times -->' . PHP_EOL;
					$last_show = end( $times );
				}

				if( ! $last_show ) {
					$last_show = time();
				}

				$content .= '</div><!-- .showtimes -->' . PHP_EOL;

				$content .= '</div><!-- .movie -->' . PHP_EOL;

				// get the rest of the upcoming movies and make a list
				$upcoming_movies = $this->get_future_movies( $last_show );
				if( $upcoming_movies->have_posts() ) {
					$content .= '<div class="row">' . PHP_EOL;
					$content .= '<div class="twelve columns centered">' . PHP_EOL;
					$content .= '<h2>Upcoming movies</h2>' . PHP_EOL;
					$content .= '</div><!-- .twelve columns -->' . PHP_EOL;
					$content .= '</div><!-- .row -->' . PHP_EOL;
					$content .= '<div class="row upcoming-movies">' . PHP_EOL;
					while( $upcoming_movies->have_posts() ) {
						$content .= '<div class="four columns">' . PHP_EOL;
						$upcoming_movies->the_post();
						$content .= '<h3><a href="' . get_the_permalink() . '">';
						$content .= get_the_title();
						$content .= '</a></h3>' . PHP_EOL;
						$dates = get_post_meta( get_the_ID(), self::PREFIX . 'showtime' );
						if( $dates ) {
							sort( $dates );
							$content .= '<p>';
							$content .= date( get_option( 'date_format' ), $dates[0] );
							if( $dates[0] !== end( $dates ) ) {
								$content .= '&ndash;';
								$content .= date( get_option( 'date_format' ), end( $dates ) );
							}
							$content .= '</p>' . PHP_EOL;
						}

						$content .= '</div><!-- .four columns -->' . PHP_EOL;
					}
					$content .= '</div><!-- .row-->' . PHP_EOL;

				}


				wp_reset_postdata();

			}

		}

		return $content;
	}

	/**
	 * Add the rewrite rule(s) that we want.
	 */
	public static function add_rewrites() {
		add_rewrite_rule( '^movies/?$', 'index.php?upcoming_movies=true', 'top' );
		flush_rewrite_rules(); // REMOVE BEFORE FLIGHT
	}

	/**
	 * Add the query var(s) that we want.
	 * @param array Query variables.
	 * @return array The filtered query variables.
	 */
	public static function add_query_vars( $vars ) {
		$vars[] = 'upcoming_movies';
		return $vars;
	}

	/**
	 * Select the appropriate template.
	 */
	function template_selector( $template ) {
		if ( get_query_var( 'upcoming_movies' ) ) {
			$template = plugin_dir_path( __FILE__ ) . '/templates/page-upcoming.php';
		}
		return $template;
	}

	/**
	 * Get the next movie that will show
	 * @param mixed $time A numeric time since epoch, or null value
	 * @return WP_Query object
	 */
	public static function get_next_movie( $time = null ) {

		if( ! is_numeric( $time ) ) {
			$time = time();
		}

		// get the next upcoming movie
		$args = array(
			'posts_per_page' => 1,
			'post_type' => Evans_Movie::POST_TYPE,

			// sort by the showtime meta value
			'orderby' => 'meta_value_num',
			'order' => 'ASC',
			'meta_key' => Evans_Movie::PREFIX . 'showtime',

			// make sure we're getting only future showtimes
			'meta_query' => array(
				'key' => Evans_Movie::PREFIX . 'showtime',
				'type' => 'NUMERIC',
				'value' => $time,
				'compare' => '>=',
			),
		);
		$movie = new WP_Query( $args );

		return $movie;

	}
	
	/**
	 * Get the upcoming movies
	 * @param mixed time A numeric time since epoch, or null value
	 * @return WP_Query object
	 */
	public static function get_future_movies( $number_of_movies = 3, $time = null ) {

		if( ! is_numeric( $time ) ) {
			$time = time();
		}

		// get the next upcoming movie
		$args = array(
			'posts_per_page' => $number_of_movies,
			'post_type' => Evans_Movie::POST_TYPE,

			// sort by the showtime meta value
			'orderby' => 'meta_value_num',
			'order' => 'ASC',
			'meta_key' => Evans_Movie::PREFIX . 'showtime',

			// make sure we're getting only future showtimes
			'meta_query' => array(
				'key' => Evans_Movie::PREFIX . 'showtime',
				'type' => 'NUMERIC',
				'value' => $time,
				'compare' => '>',
			),
		);
		$movies = new WP_Query( $args );

		return $movies;

	}

}