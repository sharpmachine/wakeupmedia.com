
	</section><!-- #page -->
</div><!-- .container - some layouts will require this to moved just above the footer tag -->
	<footer role="contentinfo">
		<div id="footer">
			<h2>Wake Up More!</h2>
			<div class="col-4">
				<h3><img src="<?php bloginfo('template_directory'); ?>/images/justsayin.png" width="60" height="46" alt="Justsayin" class="alignleft">Just Sayin’</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris pharetra ante id</p>
			</div>
			<div class="col-4">
				<h3><img src="<?php bloginfo('template_directory'); ?>/images/onthego.png" width="47" height="53" alt="Onthego" class="alignleft">On-the-Go</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris pharetra ante id</p>
			</div>
			<div class="col-4">
				<h3><img src="<?php bloginfo('template_directory'); ?>/images/blog.png" width="46" height="46" alt="Blog" class="alignleft">Life Boosters</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris pharetra ante id</p>
			</div>
			<div class="col-4 last">
				<h3><img src="<?php bloginfo('template_directory'); ?>/images/happenings.png" width="44" height="52" alt="Happenings" class="alignleft">Happenings</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris pharetra ante id</p>
			</div>
			
			<div class="col-2 clear">
				<h2>Contact Wake Up!</h2>
				<div class="push-74">
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris pharetra ante id orci ullamcorper nec laoreet mi porttitor. Nulla fringilla, mi ut sollicitudin consequat, risus lorem sollicitudin orci, vitae varius arcu libero ut nibh. Donec elementum, metus at malesuada molestie, lectus urna aliquet est, vel dictum nunc tellus eget lorem.</p>
					<a href="mailto:info@wakeupmedia.com" class="poosh-20">info@wakeupmedia.com</a>
					<a href="#"><img src="<?php bloginfo('template_directory'); ?>/images/facebook.png" width="17" height="29" alt="Facebook" class="sm"></a>
					<a href="#"><img src="<?php bloginfo('template_directory'); ?>/images/twitter.png" width="43" height="31" alt="Twitter" class="sm"></a>
					<a href="#"><img src="<?php bloginfo('template_directory'); ?>/images/youtube.png" width="74" height="32" alt="Youtube" class="sm"></a>
				</div>
			</div>
			
			<div class="col-2 last">
				<h2>A Little More Personal:</h2>
				<div class="col-4 pad-60">
					<h3>Debra:</h3>
					<a href="http://www.debradebra.com">www.debradebra.com</a><br \>
					<a href="mailto:debra@wakeupmedia.com">debra@wakeupmedia.com</a><br \>
					<a href="#"><img src="<?php bloginfo('template_directory'); ?>/images/facebook.png" width="17" height="29" alt="Facebook"></a>
					<a href="#"><img src="<?php bloginfo('template_directory'); ?>/images/twitter.png" width="43" height="31" alt="Twitter"></a>
					<a href="#"><img src="<?php bloginfo('template_directory'); ?>/images/youtube.png" width="74" height="32" alt="Youtube"></a>
				</div>
				<div class="col-4 pad-60 last">
					<h3>Bridgitte:</h3>
					<a href="http://www.brigittestraub.com">www.brigittestraub.com</a><br \>
					<a href="mailto:debra@wakeupmedia.com">brigitte@wakeupmedia.com</a><br \>
					<a href="#"><img src="<?php bloginfo('template_directory'); ?>/images/facebook.png" width="17" height="29" alt="Facebook"></a>
					<a href="#"><img src="<?php bloginfo('template_directory'); ?>/images/twitter.png" width="43" height="31" alt="Twitter"></a>
					<a href="#"><img src="<?php bloginfo('template_directory'); ?>/images/youtube.png" width="74" height="32" alt="Youtube"></a>
				</div>
			</div>
		

<?php get_sidebar( 'footer' ); ?>

			<div id="site-info">
				&copy;<?php echo date ('Y'); ?><a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
					<?php bloginfo( 'name' ); ?>
				</a>
			</div><!-- #site-info -->
		</div>
	</footer>


  
<?php wp_footer(); ?>

  <!-- scripts concatenated and minified via ant build script-->
  <script src="<?php bloginfo ('template_directory'); ?>/js/plugins.js"></script>
  <script src="<?php bloginfo ('template_directory'); ?>/js/script.js"></script>

	<!-- Remove these before deploying to production -->
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script src="<?php bloginfo ('template_directory'); ?>/js/hashgrid.js" type="text/javascript"></script>
</body>
</html>
