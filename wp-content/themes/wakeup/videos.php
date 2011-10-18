<?php
/*
	*	Template Name: Videos Page
*/
 get_header(); ?>

		<div id="content-container">
			<section id="content" role="main">
				
			<?php get_template_part( 'loop', 'page' ); ?>
			<?php rewind_posts(); ?>

			<?php query_posts("post_type=videos&posts_per_page=100"); ?>
			
			<?php if (have_posts()) : ?>
			
	<?php while (have_posts()) : the_post(); ?>
		
		<div class="videos-container">
			<a href="http://www.youtube.com/watch?v=<?php the_field('youtube_video_id'); ?>&width=640&height=390"><img src="http://img.youtube.com/vi/<?php the_field('youtube_video_id'); ?>/0.jpg" alt="Hello" width="260" height="195"></a>
			<span><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></span>
		</div>
	<?php endwhile; ?>
			
		<?php // Navigation ?>
			
	<?php else : ?>
			
		<?php // No Posts Found ?>
			
<?php endif; ?>
			

			</section><!-- #content -->
		</div><!-- #content-container -->

<?php get_footer(); ?>
