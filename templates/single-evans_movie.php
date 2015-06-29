<?php

get_header();

if( have_posts() ) {
	while( have_posts() ) {
		the_post();
		if( has_post_thumbnail() ) {
			the_post_thumbnail();
		}
		the_title( '<h1>', '</h1>' );
		the_content();
	}
}
get_sidebar();
get_footer();