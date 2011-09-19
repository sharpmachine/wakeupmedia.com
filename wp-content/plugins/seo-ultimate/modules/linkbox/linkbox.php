<?php
/**
 * Linkbox Inserter Module
 * 
 * @since 0.6
 */

if (class_exists('SU_Module')) {

class SU_Linkbox extends SU_Module {

	function get_module_title() { return __('Linkbox Inserter', 'seo-ultimate'); }
	
	function get_default_settings() {
		//The default linkbox HTML
		return array(
			  'html' => '<div class="su-linkbox" id="post-{id}-linkbox"><div class="su-linkbox-label">' .
						__('Link to this post!', 'seo-ultimate') .
						'</div><div class="su-linkbox-field">' .
						'<input type="text" value="&lt;a href=&quot;{url}&quot;&gt;{title}&lt;/a&gt;" '.
						'onclick="javascript:this.select()" readonly="readonly" style="width: 100%;" />' .
						'</div></div>'
		);
	}
	
	function init() {
		//We only want to filter post content when we're in the front-end, so we hook into template_redirect
		add_action('template_redirect', array(&$this, 'template_init'));
	}
	
	function template_init() {
		$enabled = false;
		
		if ($this->should_linkbox())
			//Add the linkbox to post/page content
			add_filter('the_content', array(&$this, 'linkbox_filter'));
		
		if ($this->get_setting('action_hook'))
			//Enable the action hook
			add_action('su_linkbox', array(&$this, 'linkbox_action'));
	}
	
	function admin_page_contents() {
		$this->admin_form_start();
		$this->checkboxes(array('filter_posts'	=> __('At the end of posts', 'seo-ultimate')
							,	'filter_pages'	=> __('At the end of pages', 'seo-ultimate')
							,	'action_hook'	=> __('When called by the su_linkbox hook', 'seo-ultimate')
		), __('Display linkboxes...', 'seo-ultimate'));
		$this->textarea('html', __('Linkbox HTML', 'seo-ultimate'), 10);
		$this->admin_form_end();
	}
	
	function should_linkbox() {
		return (!is_page() && $this->get_setting('filter_posts'))
			|| ( is_page() && $this->get_setting('filter_pages'));
	}
	
	function linkbox_filter($content, $id = false) {
		
		//If no ID is provided, get the ID of the current post
		if (!$id) $id = suwp::get_post_id();
		
		if ($id) {
			//Don't add a linkbox if a "more" link is present (since a linkbox should go at the very bottom of a post)
			$morelink = '<a href="'.get_permalink($id).'#more-'.$id.'" class="more-link">';
			if (strpos($content, $morelink) !== false) return $content;
			
			//Load the HTML and replace the variables with the proper values
			$linkbox = $this->get_setting('html');
			$linkbox = str_replace(
				array('{id}', '{url}', '{title}'),
				array(intval($id), su_esc_attr(get_permalink($id)), su_esc_attr(get_the_title($id))),
				$linkbox
			);
			
			//Return the content with the linkbox added to the bottom
			return $content.$linkbox;
		}
		
		return $content;
	}
	
	function linkbox_action($id = false) {
		echo $this->linkbox_filter('', $id);
	}
}

}
?>