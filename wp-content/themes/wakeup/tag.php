<?php get_header(); ?>

		<div id="content-container" class="span-20">
			<section id="content" role="main">

				<h1 class="page-title"><?php
					printf( __( 'Tag Archives: %s', 'twentyten' ), '<span>' . single_tag_title( '', false ) . '</span>' );
				?></h1>

<?php get_template_part( 'loop', 'tag' ); ?>
			</section><!-- #content -->
		</div><!-- #content-container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
