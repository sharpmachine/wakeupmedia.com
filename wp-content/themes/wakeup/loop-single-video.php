<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h1 class="entry-title">Videos</h1>
					
					<div class="entry-content">
						<div class="yt_holder">
    						<div id="ytvideo">
								<iframe width="551" height="310" src="http://www.youtube.com/embed/<?php the_field('youtube_video_id'); ?>" frameborder="0" allowfullscreen></iframe>
							</div>
					</div>
					
				</div><!-- .entry-content -->


				<?php comments_template( '', true ); ?>

<?php endwhile; // end of the loop. ?>

				