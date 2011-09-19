<?php
/**
 * Template Name: One column, no sidebar
 *
 * A custom page template without sidebar.
 *
 */

get_header(); ?>

		<div id="content-container" class="span-24">
			<section id="content" role="main">

			<?php get_template_part( 'loop', 'page' ); ?>

			</section><!-- #content -->
		</div><!-- #content-container -->

<?php get_footer(); ?>
