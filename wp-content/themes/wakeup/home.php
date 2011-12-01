<?php get_header(); ?>

		<div id="content-container">
			<section id="content" role="main">

			<?php // get_template_part( 'loop', 'index' ); ?>
			
			<article class="col-2">
				<?php query_posts('video_categories=debra&post_type=videos&showposts=1'); ?>
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

					<iframe width="450" height="253" src="http://www.youtube.com/embed/<?php the_field('youtube_video_id'); ?>" frameborder="0" allowfullscreen></iframe>
				
				<?php endwhile; endif; ?>
	
				<h5><a href="<?php bloginfo('url'); ?>/videos">Debra’s</a> latest video.</h5>
				<h2 class="no-mb"><a href="<?php bloginfo('url'); ?>/author/debra/">Debra's Blog</a></h2>
				<div class="feature-box">
					<?php $featured_query = new WP_Query('author=3&showposts=1');
					while ($featured_query->have_posts()) : $featured_query->the_post();
					$do_not_duplicate[] = $post->ID 
					 ?>
					<h3><?php the_title(); ?></h3>
					<p><?php echo get_avatar( get_the_author_email(), '85', '', 'headshots' ); ?><?php the_excerpt(); ?></p>
					<?php endwhile; ?>
				</div>
				<div class="older-entries">
					<h3>Recent Entries...</h3>
					<?php query_posts('author=3&showposts=4'); ?>
					<?php while (have_posts()) : the_post();
					if (in_array ($post->ID, $do_not_duplicate)) continue;
					update_post_caches($post);
					 ?>
					<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
					<?php endwhile; ?>
				</div>
				
				<div class="question-button">
					<a href="<?php bloginfo('url'); ?>/1-on-1-with-debra" class="question">Have a question for Debra?</a>
				</div>
			</article>
			
			<article class="col-2 last">
				<?php query_posts('video_categories=brigitte&post_type=videos&showposts=1'); ?>
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

					<iframe width="450" height="253" src="http://www.youtube.com/embed/<?php the_field('youtube_video_id'); ?>" frameborder="0" allowfullscreen></iframe>
				
				<?php endwhile; endif; ?>
				<h5><a href="<?php bloginfo('url'); ?>/videos">Brigitte’s</a> latest video.</h5>
				<h2 class="no-mb"><a href="<?php bloginfo('url'); ?>/author/bridgitte/">Brigitte's  Blog</a></h2>
				<div class="feature-box">
				<?php $featured_query = new WP_Query('author=5&showposts=1');
					while ($featured_query->have_posts()) : $featured_query->the_post();
					$do_not_duplicate[] = $post->ID 
					 ?>
					<h3><?php the_title(); ?></h3>
					<p><?php echo get_avatar( get_the_author_email(), '85', '', 'headshots' ); ?><?php the_excerpt(); ?></p>
					<?php endwhile; ?>
				</div>
				<div class="older-entries">
					<h3>Recent Entries...</h3>
					<?php query_posts('author=5&showposts=4'); ?>
					<?php while (have_posts()) : the_post();
					if (in_array ($post->ID, $do_not_duplicate)) continue;
					update_post_caches($post);
					 ?>
					<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
					<?php endwhile; ?>
				</div>
				<div class="question-button">
					<a href="<?php bloginfo('url'); ?>/1-on-1-with-brigitte" class="question">Have a question for Brigitte?</a>
				</div>
			</article>
			
			</section><!-- #content -->
		</div><!-- #content-container -->

<?php get_footer(); ?>
