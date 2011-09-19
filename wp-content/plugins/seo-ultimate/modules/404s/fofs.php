<?php
/**
 * 404 Monitor Module
 * 
 * @since 0.4
 */

if (class_exists('SU_Module')) {

class SU_Fofs extends SU_Module {
	function get_module_title() { return __('404 Monitor', 'seo-ultimate'); }
	function has_menu_count() { return true; }
	function admin_page_contents() { $this->children_admin_page_tabs(); }
}

}
?>