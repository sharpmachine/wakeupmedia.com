<?php get_header(); ?>

		<div id="content-container" class="span-20">
			<section id="content" role="main">

			<?php get_template_part( 'loop', 'index' ); ?>
			
			</section><!-- #content -->
		</div><!-- #content-container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
