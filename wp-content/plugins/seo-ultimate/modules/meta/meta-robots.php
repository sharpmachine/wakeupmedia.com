<?php
/**
 * Meta Robot Tags Editor Module
 * 
 * @since 4.0
 */

if (class_exists('SU_Module')) {

class SU_MetaRobots extends SU_Module {
	
	function get_module_title() { return __('Meta Robot Tags Editor', 'seo-ultimate'); }
	function get_menu_title()   { return __('Meta Robot Tags', 'seo-ultimate'); }
	function get_settings_key() { return 'meta'; }
	
	function init() {
		add_filter('su_meta_robots', array(&$this, 'meta_robots'));
	}
	
	function get_admin_page_tabs() {
		return array(
			array('title' => __('Global', 'seo-ultimate'), 'id' => 'su-global', 'callback' => 'global_tab')
		);
	}
	
	function global_tab() {
		$this->admin_form_table_start();
		$this->admin_form_subheader(__('Spider Instructions', 'seo-ultimate'));
		$this->checkboxes(array(
				  'noodp' => __('Don&#8217t use this site&#8217s Open Directory description in search results.', 'seo-ultimate')
				, 'noydir' => __('Don&#8217t use this site&#8217s Yahoo! Directory description in search results.', 'seo-ultimate')
				, 'noarchive' => __('Don&#8217t cache or archive this site.', 'seo-ultimate')
			));
		$this->admin_form_table_end();
	}
	
	//Add the appropriate commands to the meta robots array
	function meta_robots($commands) {
		
		$tags = array('noodp', 'noydir', 'noarchive');
		
		foreach ($tags as $tag) {
			if ($this->get_setting($tag)) $commands[] = $tag;
		}
		
		return $commands;
	}
}

}
?>