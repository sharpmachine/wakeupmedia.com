
	</section><!-- #page -->
</div><!-- .container - some layouts will require this to moved just above the footer tag -->
	<footer role="contentinfo">
		<div id="footer">
			<h2>Wake Up More!</h2>
			<div class="col-4">
				<h3><a href="<?php bloginfo('url'); ?>/videos#just-sayin"><img src="<?php bloginfo('template_directory'); ?>/images/justsayin.png" width="60" height="46" alt="Justsayin" class="alignleft"></a>Just Sayin'</h3>
				<p>Check out our Just Sayin’ videos were you’ll find a more candid side of us!</p>
			</div>
			<div class="col-4">
				<h3><a href="<?php bloginfo('url'); ?>/videos#on-the-go"><img src="<?php bloginfo('template_directory'); ?>/images/onthego.png" width="47" height="53" alt="Onthego" class="alignleft"></a>On-the-Go</h3>
				<p>Our purses are big enough to fit a camera in so check out our videos from the streets!</p>
			</div>
			<div class="col-4">
				<h3><a href="<?php bloginfo('url'); ?>/articles"><img src="<?php bloginfo('template_directory'); ?>/images/blog.png" width="46" height="46" alt="Blog" class="alignleft"></a>Life Boosters</h3>
				<p>Be encouraged by reading articles to lift you up and give you a new perspective!</p>
			</div>
			<div class="col-4 last">
				<h3><a href="#"><img src="<?php bloginfo('template_directory'); ?>/images/happenings.png" width="44" height="52" alt="Happenings" class="alignleft"></a>Happenings</h3>
				<p>Coming soon!</p>
			</div>
			
			<div class="col-2 clear">
				<h2>Contact Wake Up!</h2>
				<div class="push-74">
					<p>Have a question or comment? Feel free to contact Wake Up and share your heart. We love hearing from you so send us an email, post on our Facebook, follow us on Twitter, or check out our YouTube channel!</p>
					<a href="mailto:info@wakeupmedia.com" class="poosh-20">info@wakeupmedia.com</a>
					<a href="http://www.facebook.com/pages/Wake-Up-Media/57722878269"><img src="<?php bloginfo('template_directory'); ?>/images/facebook.png" width="17" height="29" alt="Facebook" class="sm"></a>
					<a href="https://twitter.com/#!/wakeupmedia"><img src="<?php bloginfo('template_directory'); ?>/images/twitter.png" width="43" height="31" alt="Twitter" class="sm"></a>
					<a href="http://www.youtube.com/user/wakeupmediatv"><img src="<?php bloginfo('template_directory'); ?>/images/youtube.png" width="74" height="32" alt="Youtube" class="sm"></a>
				</div>
			</div>
			
			<div class="col-2 last">
				<h2>A Little More Personal:</h2>
				<div class="col-4 pad-60">
					<h3>Debra:</h3>
					<a href="http://www.debradebra.com">www.debradebra.com</a><br>
					<a href="mailto:debra@wakeupmedia.com">debra@wakeupmedia.com</a><br>
					<a href="http://www.facebook.com/profile.php?id=1042331062"><img src="<?php bloginfo('template_directory'); ?>/images/facebook.png" width="17" height="29" alt="Facebook"></a>
					<a href="https://twitter.com/#!/wakeupmedia"><img src="<?php bloginfo('template_directory'); ?>/images/twitter.png" width="43" height="31" alt="Twitter"></a>
					<a href="http://www.youtube.com/user/wakeupmediatv"><img src="<?php bloginfo('template_directory'); ?>/images/youtube.png" width="74" height="32" alt="Youtube"></a>
				</div>
				<div class="col-4 pad-60 last">
					<h3>Brigitte:</h3>
					<a href="http://www.brigittestraub.com">www.brigittestraub.com</a><br>
					<a href="mailto:debra@wakeupmedia.com">brigitte@wakeupmedia.com</a><br>
					<a href="http://www.facebook.com/profile.php?id=100001846006975"><img src="<?php bloginfo('template_directory'); ?>/images/facebook.png" width="17" height="29" alt="Facebook"></a>
					<a href="https://twitter.com/#!/wakeupmedia"><img src="<?php bloginfo('template_directory'); ?>/images/twitter.png" width="43" height="31" alt="Twitter"></a>
					<a href="http://www.youtube.com/user/wakeupmediatv"><img src="<?php bloginfo('template_directory'); ?>/images/youtube.png" width="74" height="32" alt="Youtube"></a>
				</div>
			</div>
		

<?php get_sidebar( 'footer' ); ?>

			<div id="site-info">
				&copy;<?php echo date ('Y'); ?><a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
					<?php bloginfo( 'name' ); ?>
				</a> | <a href="<?php bloginfo('url'); ?>/privacy-policy">Privacy Policy</a> | <a href="<?php bloginfo('url'); ?>/terms-of-service-agreement">Terms of Service</a>
			</div><!-- #site-info -->
		</div>
	</footer>


  
<?php wp_footer(); ?>
</html>
