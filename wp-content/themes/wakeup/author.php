<?php get_header(); ?>

		<div id="content-container">
			<section id="content" role="main">

<?php
	if ( have_posts() )
		the_post();
?>

				<h1 class="page-title author"><?php printf( __( 'What %s is saying', 'twentyten' ), "<span class='vcard'><a class='url fn n' href='" . get_author_posts_url( get_the_author_meta( 'ID' ) ) . "' title='" . esc_attr( get_the_author() ) . "' rel='me'>" . get_the_author() . "</a></span>" ); ?></h1>



<?php
	rewind_posts();
	 get_template_part( 'loop', 'author' );
?>
			</section><!-- #content -->
		</div><!-- #content-container -->

<?php get_footer(); ?>
