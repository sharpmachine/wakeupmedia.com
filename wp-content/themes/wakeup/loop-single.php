<?php
/**
 * The loop that displays a single post.
 *
 * The loop displays the posts and the post content.  See
 * http://codex.wordpress.org/The_Loop to understand it and
 * http://codex.wordpress.org/Template_Tags to understand
 * the tags used in it.
 *
 * This can be overridden in child themes with loop-single.php.
 *
 */
?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="author-avatar">
						<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentyten_author_bio_avatar_size', 85 ) ); ?>
					</div>
					<div class="blog-content">
					<h1 class="entry-title"><?php the_title(); ?></h1>

					<div class="entry-meta">
						Posted on <?php the_date(); ?> by  <?php the_author_posts_link(); ?>
					</div><!-- .entry-meta -->
					
					

					<div class="entry-content">
						
						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>
					</div><!-- .entry-content -->



					<div class="entry-utility">
						<?php if ( 'article' == get_post_type() ): ?>
							This entry was posted in <?php echo get_the_term_list( $post->ID, 'cateogories', ' ', ', ', '' ); ?> and <?php echo get_the_term_list( $post->ID, 'article_tags', 'tagged ', ', ', '' ); ?>.  
							<?php endif; ?>
						<?php twentyten_posted_in(); ?>
						<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="edit-link">', '</span>' ); ?>
					</div><!-- .entry-utility -->
					
					<?php if ( get_the_author_meta( 'description' ) ) : // If a user has filled out their description, show a bio on their entries  ?>
					<div id="entry-author-info">
						<div id="author-description">
							<h2><?php printf( esc_attr__( 'About %s', 'twentyten' ), get_the_author() ); ?></h2>
							<?php the_author_meta( 'description' ); ?>
							<div id="author-link">
								<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" rel="author">
									<?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'twentyten' ), get_the_author() ); ?>
								</a>
							</div><!-- #author-link	-->
						</div><!-- #author-description -->
					</div><!-- #entry-author-info -->
				<?php endif; ?>
				
				</div><!-- #post-## -->
				<div class="clear">&nbsp;</div>
				</div><!-- .blog-content-->

				<?php comments_template( '', true ); ?>
				
				<footer class="bottom-links">
					<div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'twentyten' ) . '</span> %title' ); ?></div>
					<div class="nav-next tar"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'twentyten' ) . '</span>' ); ?></div>
				</footer>

<?php endwhile; // end of the loop. ?>