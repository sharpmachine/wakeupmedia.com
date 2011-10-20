<?php get_header(); ?>

		<div id="content-container">
			<section id="content" role="main" class="mass-archive-pages">

				<h1 class="page-title"><?php
					printf( __( 'Tag: %s', 'twentyten' ), '<span>' . single_tag_title( '', false ) . '</span>' );
				?></h1>

<?php get_template_part( 'loop', 'tag' ); ?>
			</section><!-- #content -->
		</div><!-- #content-container -->

<?php get_footer(); ?>
