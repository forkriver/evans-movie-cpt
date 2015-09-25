<?php

get_header();

/**
 * Get the list of upcoming movies and display them
 */

echo( '<h1>Upcoming Movies</h1>' . PHP_EOL );

$movies = Evans_Movie::get_future_movies( -1 );
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