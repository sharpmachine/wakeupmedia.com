<?php get_header(); ?>

	<div id="content-container" class="span-20">
		<section id="content" role="main">

			<div id="post-0" class="post error404 not-found">
				<h1 class="entry-title"><?php _e( 'Not Found', 'twentyten' ); ?></h1>
				<div class="entry-content">
					<p><?php _e( 'That\'s a bummer!  The page you\'re looking for is nowhere to be found : ( - Try searching for it or choosing something from the list below.', 'twentyten' ); ?></p>
					<?php get_search_form(); ?>
					<?php wp_list_pages(); ?>
					<?php wp_list_categories(); ?>
				</div><!-- .entry-content -->
			</div><!-- #post-0 -->

		</section><!-- #content -->
	</div><!-- #content-container -->
	<script type="text/javascript">
		// focus on search field after it has loaded
		document.getElementById('s') && document.getElementById('s').focus();
	</script>

<?php get_footer(); ?>