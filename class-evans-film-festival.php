<?php
/**
 * Film Festival functions and hooks.
 *
 * @package evans-movie
 * @subpackage film-festival
 */

/**
 * Film Festival class.
 *
 * @since 1.1.0
 */
class Evans_Film_Festival {

	/**
	 * Class constructor.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	function __construct() {
		add_shortcode( 'film_festival', array( $this, 'ff_shortcode' ) );
	}

	/**
	 * Shortcode callback for the [film_festival] shortcode.
	 *
	 * @param array $_atts The shortcode attributes.
	 * @return string The shortcode content.
	 * @since 1.1.0
	 */
	function ff_shortcode( $_atts ) {
		global $post;
		$defaults = array(
			'year' => date( 'Y', strtotime( $post->post_date ) ),
		);
		$atts = shortcode_atts( $defaults, $_atts );
		// Get the movies.
		$time = strtotime( '01-Jan-' . $atts['year'] );
		$args = array(
			'post_type' => Evans_Movie::POST_TYPE,
			// Sort by the showtime meta value.
			'orderby' => 'meta_value_num',
			'order' => 'ASC',
			'meta_key' => Evans_Movie::PREFIX . 'showtime',

			'meta_query' => array(
				'key' => Evans_Movie::PREFIX . 'showtime',
				'type' => 'NUMERIC',
				'value' => $time,
				'compare' => '>',
			),
			'tag__in' => array( Evans_Movie::get_ff_tag_id() ),
			'posts_per_page' => 100,

		);
		$ff = new WP_Query( $args );
		$content = '';
		if ( $ff->have_posts() ) {
			while ( $ff->have_posts() ) {
				$ff->the_post();
				$content .= date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), get_post_meta( get_the_ID(), Evans_Movie::PREFIX . 'showtime', true ) );
				$content .= '&mdash;';
				$content .= '<a href="' . get_permalink() . '">';
				$content .= get_the_title();
				$content .= '</a><br />' . PHP_EOL;
			}
		}
		return $content;
	}

}

new Evans_Film_Festival;
