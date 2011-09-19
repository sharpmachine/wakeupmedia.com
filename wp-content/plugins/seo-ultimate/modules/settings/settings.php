<?php
/**
 * SEO Ultimate Plugin Settings Module
 * 
 * @since 0.2
 */

if (class_exists('SU_Module')) {

class SU_Settings extends SU_Module {
	
	function get_module_title() { return __('Plugin Settings', 'seo-ultimate'); }
	function get_page_title() { return __('SEO Ultimate Plugin Settings', 'seo-ultimate'); }
	function get_menu_title() { return __('SEO Ultimate', 'seo-ultimate'); }
	function get_menu_parent(){ return 'options-general.php'; }	
	function admin_page_contents() { $this->children_admin_page_tabs(); }
}

}
?>