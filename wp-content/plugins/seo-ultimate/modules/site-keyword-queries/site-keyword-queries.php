<?php
/**
 * Internal Relevance Researcher Module
 * 
 * @since 1.4
 */

if (class_exists('SU_Module')) {

class SU_SiteKeywordQueries extends SU_Module {
	
	function get_module_title() { return __('Internal Relevance Researcher', 'seo-ultimate'); }
	function get_menu_title() { return __('Int. Rel. Researcher', 'seo-ultimate'); }

	function admin_page_contents() {
?>
<form method="get" action="http://www.seodesignsolutions.com/blog/ultimate-linkbuilding-toolkit/result.php" target="_blank">
<input type="hidden" id="showback" name="showback" value="0" />
<input type="hidden" id="queries" name="queries" value="<?php echo su_esc_attr(trailingslashit(get_bloginfo('url'))); ?>" />

<h3><?php _e('Step 1: Enter Keywords', 'seo-ultimate'); ?></h3>
<div><textarea id="queries2" name="queries2" rows="10" cols="60"></textarea></div>
<div><em><?php _e('(Type one keyword per line)', 'seo-ultimate'); ?></em></div>

<h3><?php _e('Step 2: Set Options and Submit', 'seo-ultimate'); ?></h3>
<div>
	<label><input type="checkbox" name="quotes" value="1" /> <?php _e('Put keywords in quotes', 'seo-ultimate'); ?></label><br />
	<label><input type="checkbox" name="r100" value="1" /> <?php _e('Show 100 results per page', 'seo-ultimate'); ?></label><br />
	<label id="minimal-checkbox"><input type="checkbox" name="minimal" value="1" /> <?php
		_e('Use Google&#8217;s minimal mode', 'seo-ultimate'); ?></label><br /><br />
</div>

<div id="submit"><input type="submit" value="<?php _e('Submit', 'seo-ultimate'); ?>" class="button-primary" /></div>
</form>
<?php
	}
	
}

}
?>