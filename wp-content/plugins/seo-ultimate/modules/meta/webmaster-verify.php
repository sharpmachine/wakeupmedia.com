<?php
/**
 * Webmaster Verification Assistant Module
 * 
 * @since 4.0
 */

if (class_exists('SU_Module')) {

class SU_WebmasterVerify extends SU_Module {
	
	function get_module_title() { return __('Webmaster Verification Assistant', 'seo-ultimate'); }
	function get_menu_title() { return __('W.M. Verification', 'seo-ultimate'); }
	
	function get_parent_module() { return 'misc'; }
	function get_settings_key() { return 'meta'; }
	
	function init() {
		add_action('su_head', array(&$this, 'head_tag_output'));
	}
	
	function head_tag_output() {
		
		//Supported meta tags and their names
		$verify = array(
			  'google' => 'google-site-verification'
			, 'yahoo' => 'y_key'
			, 'microsoft' => 'msvalidate.01'
		);
		
		//Do we have verification tags? If so, output them.
		foreach ($verify as $site => $name) {
			if ($value = $this->get_setting($site.'_verify')) {
				if (sustr::startswith(trim($value), '<meta ') && sustr::endswith(trim($value), '/>'))
					echo "\t".trim($value)."\n";
				else {
					$value = su_esc_attr($value);
					echo "\t<meta name=\"$name\" content=\"$value\" />\n";
				}
			}
		}
	}
	
	function admin_page_contents() {
		$this->child_admin_form_start();
		$this->textboxes(array(
				  'google_verify' => __('Google Webmaster Tools', 'seo-ultimate')
				, 'yahoo_verify' => __('Yahoo! Site Explorer', 'seo-ultimate')
				, 'microsoft_verify' => __('Bing Webmaster Center', 'seo-ultimate')
			));
		$this->child_admin_form_end();
	}
}

}
?>