<?php 
/*
	Template Name: Blog
*/
get_header(); ?>

		<div id="content-container">
			<section id="content" role="main">

			<?php get_template_part( 'loop', 'blog' ); ?>
			<div class="black-bar">
					<h2 class="split">Debra</h2>
					<h2 class="split">Bridgitte</h2>
				</div>
				
			<div class="posts-col-2">
			<?php rewind_posts(); ?>
				
			<?php
				$temp = $wp_query;
				$wp_query= null;
				$wp_query = new WP_Query();
				$wp_query->query('author=1&showposts=4&paged='.$paged);
				while ($wp_query->have_posts()) : $wp_query->the_post();
				$do_not_duplicate[] = $post->ID
			?>
			
			<div class="posts-col-4">
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h3 class="entry-title">
					<a href="<?php the_permalink(); ?>"><?php the_title();  ?></a>
				</h3>
				<div><?php twentyten_posted_on(); ?></div>
				<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( array (100, 100) ); ?></a>
			<?php the_excerpt(); ?>
			
				<div class="entry-utility">
				<?php if ( count( get_the_category() ) ) : ?>
					<span class="cat-links">
						<?php printf( __( '<span class="%1$s">Posted in</span> %2$s', 'twentyten' ), 'entry-utility-prep entry-utility-prep-cat-links', get_the_category_list( ', ' ) ); ?>
					</span>
					<span class="meta-sep">|</span>
				<?php endif; ?>
				<?php
					$tags_list = get_the_tag_list( '', ', ' );
					if ( $tags_list ):
				?>
					<span class="tag-links">
						<?php printf( __( '<span class="%1$s">Tagged</span> %2$s', 'twentyten' ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list ); ?>
					</span>
					<span class="meta-sep">|</span>
				<?php endif; ?>
				<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% Comments', 'twentyten' ) ); ?></span>
				<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?>
			</div><!-- .entry-utility -->
			</div>
				</div><!-- .posts-col-4 -->
			<?php endwhile; ?>
			</div><!-- .posts-col-2 -->
			
			<div class="black-bar">
					<h2>Older Posts</h2>
				</div>
			
			<?php query_posts('author=1&showposts=10'); ?>
			<?php while (have_posts()) : the_post();
			if (in_array ($post->ID, $do_not_duplicate)) continue;
			update_post_caches($post);
			 ?>
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h3 class="entry-title">
					<a href="<?php the_permalink(); ?>"><?php the_title();  ?></a>
				</h3>
				<div><?php twentyten_posted_on(); ?></div>
				<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( array (100, 100) ); ?></a>
			<?php the_excerpt(); ?>
			
				<div class="entry-utility">
				<?php if ( count( get_the_category() ) ) : ?>
					<span class="cat-links">
						<?php printf( __( '<span class="%1$s">Posted in</span> %2$s', 'twentyten' ), 'entry-utility-prep entry-utility-prep-cat-links', get_the_category_list( ', ' ) ); ?>
					</span>
					<span class="meta-sep">|</span>
				<?php endif; ?>
				<?php
					$tags_list = get_the_tag_list( '', ', ' );
					if ( $tags_list ):
				?>
					<span class="tag-links">
						<?php printf( __( '<span class="%1$s">Tagged</span> %2$s', 'twentyten' ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list ); ?>
					</span>
					<span class="meta-sep">|</span>
				<?php endif; ?>
				<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% Comments', 'twentyten' ) ); ?></span>
				<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?>
			</div><!-- .entry-utility -->
			</div>
<?php endwhile; ?>
			<?php $wp_query = null; $wp_query = $temp;?>
					
					
					
				
				
			
				
			</section><!-- #content -->
		</div><!-- #content-container -->
<?php get_footer(); ?>
