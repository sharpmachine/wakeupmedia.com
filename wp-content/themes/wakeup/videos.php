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
					
	<?php endwhile;  endif; ?>
	
	<div class="black-bar">
					<h2 class="split">Debra's Videos</h2>
					<h2 class="split">Brigitte's Videos</h2>
				</div>
				
				<div class="posts-col-2">
			
						<?php $featured_query = new WP_Query('showposts=4&post_type=videos&video_categories=debra');
						while ($featured_query->have_posts()) : $featured_query->the_post();
						$do_not_duplicate[] = $post->ID 
						 ?>
							<div class="videos-container">
								<a href="http://www.youtube.com/watch?v=<?php the_field('youtube_video_id'); ?>&width=640&height=390" rel="lightbox[video]"><img src="http://img.youtube.com/vi/<?php the_field('youtube_video_id'); ?>/0.jpg" alt="Hello" width="216" height="139"></a>
								<span>
									<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								</span>
								<br \>
								<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% Comments', 'twentyten' ) ); ?></span>
							</div><!-- .videos-container -->
							<?php endwhile; ?>
						</div><!-- .posts-col-2 -->
						
			<div class="posts-col-2 last">

	
					<?php $featured_query = new WP_Query('showposts=4&post_type=videos&video_categories=brigitte');
						while ($featured_query->have_posts()) : $featured_query->the_post();
						$do_not_duplicate[] = $post->ID 
						 ?>
							<div class="videos-container">
								<a href="http://www.youtube.com/watch?v=<?php the_field('youtube_video_id'); ?>&width=640&height=390" rel="lightbox[video]"><img src="http://img.youtube.com/vi/<?php the_field('youtube_video_id'); ?>/0.jpg" alt="Hello" width="216" height="139"></a>
								<span>
									<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								</span>
								<br \>
								<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% Comments', 'twentyten' ) ); ?></span>
							</div><!-- .videos-container -->
						<?php endwhile; ?>
			</div><!-- .posts-col-2 -->
				
				<div class="black-bar" id="on-the-go">
					<h2>On-The-Go!</h2>
				</div>
				
				<div class="videos">
				
				<?php query_posts('showposts=12&post_type=videos&video_categories=on-the-go'); ?>
					<?php while (have_posts()) : the_post();
					if (in_array ($post->ID, $do_not_duplicate)) continue;
					update_post_caches($post);
					 ?>
					 <div class="videos-container2">
								<a href="http://www.youtube.com/watch?v=<?php the_field('youtube_video_id'); ?>&width=640&height=390" rel="lightbox[video]"><img src="http://img.youtube.com/vi/<?php the_field('youtube_video_id'); ?>/0.jpg" alt="Hello" width="223" height="143"></a>
								<span>
									<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								</span>
								<br \>
								<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% Comments', 'twentyten' ) ); ?></span>
							</div><!-- .videos-container -->
							<?php endwhile; ?>
				</div><!-- .videos -->
				<div class="black-bar" id="just-sayin">
					<h2>Just Sayin'</h2>
				</div>
				<div class="videos">
				<?php query_posts('showposts=12&post_type=videos&video_categories=just-sayin'); ?>
					<?php while (have_posts()) : the_post();
					if (in_array ($post->ID, $do_not_duplicate)) continue;
					update_post_caches($post);
					 ?>
					 <div class="videos-container2">
								<a href="http://www.youtube.com/watch?v=<?php the_field('youtube_video_id'); ?>&width=640&height=390" rel="lightbox[video]"><img src="http://img.youtube.com/vi/<?php the_field('youtube_video_id'); ?>/0.jpg" alt="Hello" width="223" height="143"></a>
								<span>
									<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								</span>
								<br \>
								<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% Comments', 'twentyten' ) ); ?></span>
							</div><!-- .videos-container -->
							<?php endwhile; ?>
				</div><!-- .videos -->
	
				<footer class="bottom-links tar">
					<a href="http://www.youtube.com/user/wakeupmediatv">Watch more videos on our Youtube Channel</a>
				</footer>
				
			</section><!-- #content -->
		</div><!-- #content-container -->

<?php get_footer(); ?>