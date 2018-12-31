<?php

get_header();

/**
 * Get the list of upcoming movies and display them
 */

echo( '<h1>Upcoming Movies</h1>' . PHP_EOL );

$movies = Evans_Movie::get_future_movies( time(), -1 );
// Get next year's movies if there aren't any in the current year.
if ( ! $movies->have_posts() ) {
	$next_year = intval( date( 'Y' ) ) + 1;
	$time = strtotime( "January 1, $next_year" );
	$movies = Evans_Movie::get_future_movies( $time, -1 );
}
if( $movies->have_posts() ) {
	$thumbnail_attrs = array(
		'align' => 'left',
	);
	while ( $movies->have_posts() ) {
		$movies->the_post();
		echo( '<div class="row">' );
		if( has_post_thumbnail() ) {
			the_post_thumbnail( 'thumbnail', $thumbnail_attrs );
		}
		the_title( '<h2><a href="' . get_permalink() . '">', '</a></h2>' . PHP_EOL );
		echo( '</div> <!-- .row -->' );
	}
	wp_reset_postdata();
}
get_sidebar();
get_footer();