<?php include('screen_extra_activate.php'); ?>
<?php include('screen_extra_export.php'); ?>

<?php
// get current page
$currentFile = $_SERVER["SCRIPT_NAME"];
$parts = Explode('/', $currentFile);
$currentFile = $parts[count($parts) - 1];

if($currentFile == 'edit.php'):
?>


<div class="acf_col_right hidden metabox-holder" id="poststuff" >

	<div class="postbox">
		<div class="handlediv"><br></div>
		<h3 class="hndle"><span><?php _e("Advanced Custom Fields v",'acf'); ?><?php echo $this->version; ?></span></h3>
		<div class="inside">
			<div class="field">
				<h4><?php _e("Changelog",'acf'); ?></h4>
				<p><?php _e("See what's new in",'acf'); ?> <a class="thickbox" href="<?php bloginfo('url'); ?>/wp-admin/plugin-install.php?tab=plugin-information&plugin=advanced-custom-fields&section=changelog&TB_iframe=true&width=640&height=559">v<?php echo $this->version; ?></a>
			</div>
			<div class="field">
				<h4><?php _e("Resources",'acf'); ?></h4>
				<p><?php _e("Watch tutorials, read documentation, learn the API code and find some tips &amp; tricks for your next web project.",'acf'); ?><br />
				<a href="http://plugins.elliotcondon.com/advanced-custom-fields/"><?php _e("View the plugins website",'acf'); ?></a></p>
			</div>
			<!-- <div class="field">
				<h4><?php _e("Support",'acf'); ?></h4>
				<p><?php _e("Join the growing community over at the support forum to share ideas, report bugs and keep up to date with ACF",'acf'); ?><br />
				<a href="http://support.plugins.elliotcondon.com/categories/advanced-custom-fields/"><?php _e("View the Support Forum",'acf'); ?></a></p>
			</div> -->
			<div class="field">
				<h4><?php _e("Developed by",'acf'); ?> Elliot Condon</h4>
				<p><a href="http://wordpress.org/extend/plugins/advanced-custom-fields/"><?php _e("Vote for ACF",'acf'); ?></a> | <a href="http://twitter.com/elliotcondon"><?php _e("Twitter",'acf'); ?></a> | <a href="http://blog.elliotcondon.com"><?php _e("Blog",'acf'); ?></a></p>
			</div>
			
		
		</div>
	</div>
</div>

<?php endif; ?>