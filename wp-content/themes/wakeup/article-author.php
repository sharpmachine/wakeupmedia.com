<?php 
/*
	* Template Name: Debra Articles Archive
*/
get_header(); ?>

		<div id="content-container">
			<section id="content" role="main" class="mass-archive-pages">

<?php
	if ( have_posts() )
		the_post();
?>

				<h1 class="page-title author"><?php the_title(); ?></h1>

<?php
	rewind_posts();
	
$paged = 1;
if ( get_query_var('paged') ) $paged = get_query_var('paged');
if ( get_query_var('page') ) $paged = get_query_var('page');

query_posts( '&post_type=article&author=3&paged=' . $paged );

	 get_template_part( 'loop', 'author' );
?>

			</section><!-- #content -->
		</div><!-- #content-container -->

<?php get_footer(); ?>
