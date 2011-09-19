<?php
/**
 * Sharing Facilitator Module
 * 
 * @since 3.5
 */

if (class_exists('SU_Module')) {

class SU_SharingButtons extends SU_Module {
	
	function get_module_title() { return __('Sharing Facilitator', 'seo-ultimate'); }
	
	function get_parent_module() { return 'misc'; }
	function get_settings_key() { return 'sharing-buttons'; }
	
	function init() {
		add_filter('the_content', array(&$this, 'add_sharing_buttons'));
	}
	
	function get_default_settings() {
		return array(
			  'provider' => 'none'
			, 'sharethis_code' => '<script type="text/javascript" charset="utf-8" src="http://w.sharethis.com/widget/?wp={wpver}"></script>'
			, 'addthis_code' => '<a class="addthis_button" href="http://addthis.com/bookmark.php?v=250"><img src="http://s7.addthis.com/static/btn/v2/lg-share-en.gif" width="125" height="16" alt="' . __('Bookmark and Share', 'seo-ultimate') . '" style="border:0"/></a><script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js"></script>'
		);
	}
	
	/*
	function get_admin_page_tabs() {
		return array(
			  __('Providers', 'seo-ultimate') => 'providers_tab'
		);
	}
	*/
	
	function admin_page_contents() {
		$this->child_admin_form_start();
		$this->admin_form_subheader(__('Which provider would you like to use for your sharing buttons?', 'seo-ultimate'));
		$this->radiobuttons('provider', array(
			  'none' => __('None; disable sharing buttons', 'seo-ultimate')
			, 'sharethis' => __('Use the ShareThis button', 'seo-ultimate') //: %s{sharethis_code}
			, 'addthis' => __('Use the AddThis button', 'seo-ultimate') //: %s{addthis_code}
		));
		$this->child_admin_form_end();
	}
	
	function add_sharing_buttons($content) {
		if (!is_feed()) {
			switch ($this->get_setting('provider', 'none')) {
				case 'sharethis': $code = $this->get_setting('sharethis_code', ''); break;
				case 'addthis': $code = $this->get_setting('addthis_code', ''); break;
				default: return $content; break;
			}
			
			if ($code) {
				$code = str_replace(array(
						  '{wpver}'
					), array (
						  get_bloginfo('version')
					), $code);
				return $content . $code;
			}
		}
		return $content;
	}
}

}
?>