<?php
/*
	*	Template Name: Videos Page
*/
 get_header(); ?>

		<div id="content-container">
			<section id="content" role="main">	
			<?php get_template_part( 'loop', 'page' ); ?>
			<?php rewind_posts(); ?>
				<div class="yt_holder">
    				<div id="ytvideo"></div>
					<ul class="video-playlist">
						<?php query_posts("post_type=videos&posts_per_page=100"); ?>
						<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
						<li>
							<a class="video-thumb" href="http://www.youtube.com/watch?v=<?php the_field('youtube_video_id'); ?>"></a>
							
						</li>
						<?php endwhile; ?>
			
						<?php else : ?>
			
							<p>Sorry, no videos right now.  Check back soon though!</p>
			
						<?php endif; ?>
						
					</ul><!-- .demo2 -->
				</div><!-- .yt_holder -->
				
			</section><!-- #content -->
		</div><!-- #content-container -->

<?php get_footer(); ?>