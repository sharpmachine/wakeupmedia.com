<?php 
/* 
	* Template Name: 1 on 1
		*/
get_header(); ?>

		<div id="content-container">
			<section id="content" role="main">

			<?php get_template_part( 'loop', 'page' ); ?>
			
			<footer class="bottom-links tar">
				<?php if (is_page('1-on-1-with-brigitte')): ?>
					<a href="<?php bloginfo('url'); ?>/1-on-1-with-debra">Ask Debra a Question...</a>
				<?php endif; ?>
				
				<?php if (is_page('1-on-1-with-debra')): ?>
					<a href="<?php bloginfo('url'); ?>/1-on-1-with-brigitte">Ask Brigitte a Question...</a>
				<?php endif; ?>
			</footer>

			</section><!-- #content -->
		</div><!-- #content-container -->


<?php get_footer(); ?>
