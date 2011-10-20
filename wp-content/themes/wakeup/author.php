<?php get_header(); ?>

		<div id="content-container">
			<section id="content" role="main" class="mass-archive-pages">

<?php
	if ( have_posts() )
		the_post();
?>

				<h1 class="page-title author">What <?php the_author(); ?> is saying</h1>
				
				

<?php
	rewind_posts();
	 get_template_part( 'loop', 'author' );
?>

			</section><!-- #content -->
		</div><!-- #content-container -->

<?php get_footer(); ?>
