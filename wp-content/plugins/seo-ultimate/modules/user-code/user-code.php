<?php
/**
 * Code Inserter Module
 * 
 * @since 2.7
 */

if (class_exists('SU_Module')) {

class SU_UserCode extends SU_Module {
	
	function get_module_title() { return __('Code Inserter', 'seo-ultimate'); }
	
	function get_default_settings() {
		return array(
			  'global_wp_head' => $this->flush_setting('custom_html', '', 'meta')
		);
	}
	
	function init() {
		$hooks = array('su_head', 'the_content', 'wp_footer');
		foreach ($hooks as $hook) add_filter($hook, array(&$this, "{$hook}_code"));
	}
	
	function get_admin_page_tabs() {
		return array(
			  array('title' => __('Everywhere', 'seo-ultimate'), 'id' => 'su-everywhere', 'callback' => array('usercode_admin_tab', 'global'))
		);
	}
	
	function usercode_admin_tab($section) {
		
		$textareas = array(
			  'wp_head' => __('&lt;head&gt; Tag', 'seo-ultimate')
			, 'the_content_before' => __('Before Item Content', 'seo-ultimate')
			, 'the_content_after' => __('After Item Content', 'seo-ultimate')
			, 'wp_footer' => __('Footer', 'seo-ultimate')
		);
		$textareas = suarr::aprintf("{$section}_%s", false, $textareas);
		
		$this->admin_form_table_start();
		$this->textareas($textareas);
		$this->admin_form_table_end();
	}
	
	function get_usercode($field) {
		
		$code = $this->get_setting("global_$field", '');
		if (is_front_page()) $code .= $this->get_setting("frontpage_$field", '');
		
		return $this->plugin->mark_code($code, __('Code Inserter module', 'seo-ultimate'), $field == 'wp_head');
	}
	
	function su_head_code() {
		echo $this->get_usercode('wp_head');
	}
	
	function wp_footer_code() {
		echo $this->get_usercode('wp_footer');
	}
	
	function the_content_code($content) {
		return $this->get_usercode('the_content_before') . $content . $this->get_usercode('the_content_after');
	}
	
}

}

?>