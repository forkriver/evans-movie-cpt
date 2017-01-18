<?php

get_header();

/**
 * Get the list of upcoming movies and display them
 */

global $wp_query;
$movies = $wp_query->posts;
usort( $movies, array( 'Evans_Movie', '_sort_by_date_asc' ) );
echo( '<div class="movie-year-list">' . PHP_EOL );
echo( '<h1>Movies in ' . get_query_var( 'movie_year' ) . '</h1>' . PHP_EOL );
$prev_month = 0;
$month_links = '';
$content = '';
foreach( $movies as $m ) {
	$dates = get_post_meta( $m->ID, Evans_Movie::PREFIX . 'showtime' );
	$month = date( 'm', $dates[0] );
	if( absint( $month) !== $prev_month ) {
		$content .= '<h2><a name="' . strtolower( date( 'M', $dates[0] ) ) . '"></a>' . date( 'F', $dates[0] ) . '</h2>' . PHP_EOL;
		$month_links .= '<a href="#' . strtolower( date( 'M', $dates[0] ) ) . '">' . date( 'F', $dates[0] ) . '</a> | ';
		$prev_month = absint( date( 'm', $dates[0] ) );
	}
	$content .= '<p>' . PHP_EOL .
				'<div class="movie-title movie-' . $m->ID . '">' .
				'<a href="' . get_permalink( $m->ID ) . '"><h3>' . $m->post_title . '</h3></a>' . 
				'</div> <!-- .movie-title -->' . PHP_EOL .
				'<div class="movie-dates">' . PHP_EOL .
				'<ul class="movie-dates">' . PHP_EOL;
	foreach( $dates as $d ) {
		$content .= '<li class="movie-date">' . date( 'M jS \a\t g:iA', $d ) . '</li> <!-- .movie-date -->' . PHP_EOL;
	}
	$content .= '</ul> <!-- .movie-dates -->' . PHP_EOL .
				'</div> <!-- .movie-dates -->' . PHP_EOL .
				'</p>' . PHP_EOL;
}
echo( substr( $month_links, 0, strrpos( $month_links, ' |' ) ) . PHP_EOL );
echo( $content );
echo( '</div> <!-- .movie-year-list -->' . PHP_EOL );

get_sidebar();
get_footer();