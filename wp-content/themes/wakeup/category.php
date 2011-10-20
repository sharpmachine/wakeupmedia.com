<?php get_header(); ?>

		<div id="content-container">
			<section id="content" role="main" class="mass-archive-pages">

				<h1 class="page-title"><?php
					printf( __( 'Category: %s', 'twentyten' ), '<span>' . single_cat_title( '', false ) . '</span>' );
				?></h1>
				<?php
					$category_description = category_description();
					if ( ! empty( $category_description ) )
						echo '<div class="archive-meta">' . $category_description . '</div>';

				get_template_part( 'loop', 'category' );
				?>

			</section><!-- #content -->
		</div><!-- #content-container -->
<?php get_footer(); ?>
