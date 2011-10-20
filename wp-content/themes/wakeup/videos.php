<?php
/*
	*	Template Name: Videos Page
*/
 get_header(); ?>

		<div id="content-container">
			<section id="content" role="main">	
					<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
					
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php if ( is_front_page() ) { ?>
						<h2 class="entry-title"><?php the_title(); ?></h2>
					<?php } else { ?>
						<h1 class="entry-title"><?php the_title(); ?></h1>
					<?php } ?>
					
					<div class="entry-content">
						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>
					</div><!-- .entry-content -->
				</div><!-- #post-## -->
					
	<?php endwhile; endif; ?>
	
				<div class="yt_holder">
    				<div id="ytvideo"></div>
					<ul class="video-playlist">
						<?php query_posts("post_type=videos&posts_per_page=100"); ?>
						<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
						<li>
							<a class="video-thumb" href="http://www.youtube.com/watch?v=<?php the_field('youtube_video_id'); ?>"><br \><?php the_title(); ?></a>
							
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