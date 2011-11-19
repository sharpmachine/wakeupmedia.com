<?php 
/*
	Template Name: Blog
*/
get_header(); ?>

		<div id="content-container">
			<section id="content" role="main">

			<?php get_template_part( 'loop', 'blog' ); ?>
			<div class="black-bar">
					<h2 class="split">Debra's</h2>
					<h2 class="split">Brigitte's</h2>
				</div>
				
			<div class="posts-col-2">
			
			<?php $featured_query = new WP_Query('author=3&showposts=4');
			while ($featured_query->have_posts()) : $featured_query->the_post();
			$do_not_duplicate[] = $post->ID 
			 ?>
			<div class="posts-col-4">
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h3 class="entry-title">
					<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php get_short_title();  ?></a>
				</h3>
				<div class="posted-on"><?php twentyten_posted_on(); ?></div>
			<?php echo get_short_excerpt(); ?>
			</div>
				</div><!-- .posts-col-4 -->
			<?php endwhile; ?>
			</div><!-- .posts-col-2 -->
			
			<div class="posts-col-2 last">

			<?php $featured_query = new WP_Query('author=2&showposts=4');
			while ($featured_query->have_posts()) : $featured_query->the_post();
			$do_not_duplicate[] = $post->ID 
			 ?>
				 <div class="posts-col-4">
					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<h3 class="entry-title">
							<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php get_short_title();  ?></a>
						</h3>
						<div class="posted-on"><?php twentyten_posted_on(); ?></div>
					<?php echo get_short_excerpt(); ?>
					
					</div>
				</div><!-- .posts-col-4 -->
			<?php endwhile; ?>
			</div><!-- .posts-col-2 -->
				
				<div class="black-bar">
					<h2>Older Posts</h2>
				</div>
	
				<div class="posts-col-2 tar old-posts">
					
					<?php query_posts('author=3&showposts=10'); ?>
					<?php while (have_posts()) : the_post();
					if (in_array ($post->ID, $do_not_duplicate)) continue;
					update_post_caches($post);
					 ?>
			
					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<h3 class="entry-title">
							<span>Posted on <?php the_date(); ?> by <?php the_author(); ?></span><br>
							<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title();  ?></a>
						</h3>
					</div>
		
						<?php endwhile; ?>
				</div><!-- .posts-col-2 -->

				<div class="posts-col-2 last old-posts">
					
					<?php query_posts('author=2&showposts=10'); ?>
					<?php while (have_posts()) : the_post();
					if (in_array ($post->ID, $do_not_duplicate)) continue;
					update_post_caches($post);
					 ?>
			
					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<h3 class="entry-title">
							<span>Posted on <?php the_date(); ?> by <?php the_author(); ?></span><br>
							<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title();  ?></a>
						</h3>
					</div>
		
						<?php endwhile; ?>
				</div><!-- .posts-col-2 -->
				
				<footer class="bottom-links">
					<div class="nav-previous"><a href="<?php bloginfo('url'); ?>/author/debra/">See all posts by Debra</a></div>
					<div class="nav-next tar"><a href="<?php bloginfo('url'); ?>/author/bridgitte/">See all posts by Brigitte</a></div>
				</footer>
			</section><!-- #content -->
		</div><!-- #content-container -->
<?php get_footer(); ?>
