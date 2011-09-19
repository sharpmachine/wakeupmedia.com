<?php
/**
 * More Link Customizer Module
 * 
 * @since 1.3
 */

if (class_exists('SU_Module')) {

class SU_MoreLinks extends SU_Module {
	
	function get_module_title() { return __('More Link Customizer', 'seo-ultimate'); }
	
	function get_parent_module() { return 'misc'; }
	function get_settings_key() { return 'more-links'; }
	
	function get_default_settings() {
		return array(
			  'default' => 'Continue reading &#8220;{post}&#8221; &raquo;'
		);
	}
	
	function init() {
		add_filter('the_content_more_link', array(&$this, 'more_link_filter'), 10, 2);
		add_filter('su_get_postmeta-morelinktext', array(&$this, 'get_morelinktext_postmeta'), 10, 3);
	}
	
	function admin_page_contents() {
		$this->child_admin_form_start();
		$this->textbox('default', __('Default More Link Text', 'seo-ultimate'), $this->get_default_setting('default'));
		$this->child_admin_form_end();
	}
	
	function more_link_filter($link, $text=false) {
		
		if ($text === false) return $link; //Can't do it without $text parameter
		
		$default = $this->get_setting('default');
		
		if (strlen($newtext = trim($this->get_postmeta('morelinktext'))) || strlen(trim($newtext = $default))) {
			$newtext = str_replace('{post}', su_esc_html(get_the_title()), $newtext);
			$link = str_replace("$text</a>", "$newtext</a>", $link);
		}
		
		return $link;
	}
	
	function postmeta_fields($fields, $screen) {
		
		if (strcmp($screen, 'post') == 0)
			$fields['40|morelinktext'] = $this->get_postmeta_textbox('morelinktext', __('More Link Text:', 'seo-ultimate'));
		
		return $fields;
	}
	
	function get_morelinktext_postmeta($value, $key, $post) {
		
		if (!strlen($value)) {
			
			//Import any custom anchors from the post itself
			$content = $post->post_content;
			$matches = array();
			if ( preg_match('/<!--more(.*?)?-->/', $content, $matches) ) {
				$content = explode($matches[0], $content, 2);
				if ( !empty($matches[1]) )
					return strip_tags(wp_kses_no_null(trim($matches[1])));
			}
		}
		
		return $value;
	}
}

}
?>