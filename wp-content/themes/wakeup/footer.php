
	</section><!-- #page -->
</div><!-- .container - some layouts will require this to moved just above the footer tag -->
	<footer role="contentinfo">
		<div id="footer">
			<h2>Wake Up More!</h2>
			<div class="col-4">
				<h3><a href="#"><img src="<?php bloginfo('template_directory'); ?>/images/justsayin.png" width="60" height="46" alt="Justsayin" class="alignleft"></a>Just Sayin'</h3>
				<p>Check out our Just Sayin’ videos were you’ll find a more candid side of us!</p>
			</div>
			<div class="col-4">
				<h3><a href="#"><img src="<?php bloginfo('template_directory'); ?>/images/onthego.png" width="47" height="53" alt="Onthego" class="alignleft"></a>On-the-Go</h3>
				<p>Our purses are big enough to fit a camera in so check out our videos from the streets!</p>
			</div>
			<div class="col-4">
				<h3><a href="#"><img src="<?php bloginfo('template_directory'); ?>/images/blog.png" width="46" height="46" alt="Blog" class="alignleft"></a>Life Boosters</h3>
				<p>Be encouraged by reading articles to lift you up and give you a new perspective!</p>
			</div>
			<div class="col-4 last">
				<h3><a href="#"><img src="<?php bloginfo('template_directory'); ?>/images/happenings.png" width="44" height="52" alt="Happenings" class="alignleft"></a>Happenings</h3>
				<p>Coming soon!</p>
			</div>
			
			<div class="col-2 clear">
				<h2>Contact Wake Up!</h2>
				<div class="push-74">
					<p>Got some questions or comments? Feel free to contact wake up and share your heart. We love hearing from you so send us an email, post on our Facebook or find us on Twitter. Check out our YouTube channel for more videos!</p>
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
