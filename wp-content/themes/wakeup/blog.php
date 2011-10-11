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
					<h2 class="split">Bridgitte's</h2>
				</div>
				
			<div class="posts-col-2">
			<?php rewind_posts(); ?>
				
			<?php
				// $temp = $wp_query;
				// 				$wp_query= null;
				// 				$wp_query = new WP_Query();
				// 				$wp_query->query('author=3&showposts=4&paged='.$paged);
				// 				while ($wp_query->have_posts()) : $wp_query->the_post();
				// 				$do_not_duplicate[] = $post->ID
			?>
			
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
			
			<?php query_posts('author=2&showposts=4'); ?>
			<?php while (have_posts()) : the_post();
			if (in_array ($post->ID, $do_not_duplicate)) continue;
			update_post_caches($post);
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
							<h4>Posted on <?php the_date(); ?> by <?php the_author(); ?></h4>
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
							<h4>Posted on <?php the_date(); ?> by <?php the_author(); ?></h4>
							<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title();  ?></a>
						</h3>
					</div>
		
						<?php endwhile; ?>
				</div><!-- .posts-col-2 -->


				
			
				<?php // $wp_query = null; $wp_query = $temp;?>
			</section><!-- #content -->
		</div><!-- #content-container -->
<?php get_footer(); ?>
