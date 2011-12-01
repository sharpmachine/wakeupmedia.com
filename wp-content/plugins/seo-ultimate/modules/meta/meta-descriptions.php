<?php
/**
 * Meta Description Editor Module
 * 
 * @since 4.0
 */

if (class_exists('SU_Module')) {

class SU_MetaDescriptions extends SU_Module {
	
	function get_module_title() { return __('Meta Description Editor', 'seo-ultimate'); }
	function get_menu_title()   { return __('Meta Descriptions', 'seo-ultimate'); }
	function get_settings_key() { return 'meta'; }
	
	function init() {
		add_action('su_head', array(&$this, 'head_tag_output'));
		add_filter('su_postmeta_help', array(&$this, 'postmeta_help'), 20);
	}
	
	function get_admin_page_tabs() {
		return array_merge(
			  array(
				  array('title' => __('Default Formats', 'seo-ultimate'), 'id' => 'su-default-formats', 'callback' => 'formats_tab')
				, array('title' => __('Blog Homepage', 'seo-ultimate'), 'id' => 'su-blog-homepage', 'callback' => 'home_tab')
				)
			, $this->get_meta_edit_tabs(array(
				  'type' => 'textarea'
				, 'name' => 'description'
				, 'term_settings_key' => 'taxonomy_descriptions'
				, 'label' => __('Meta Description', 'seo-ultimate')
			))
		);
	}
	
	function get_default_settings() {
		return array(
			  'home_description_tagline_default' => true
			, 'description_posttype_post' => '{excerpt}'
			, 'description_taxonomy_category' => '{description}'
			, 'description_taxonomy_post_tag' => '{description}'
		);
	}
	
	function formats_tab() {
		$this->admin_form_table_start();
		$this->textboxes(array(
			  'description_posttype_post' => __('Post Description Format', 'seo-ultimate')
			, 'description_taxonomy_category' => __('Category Description Format', 'seo-ultimate')
			, 'description_taxonomy_post_tag' => __('Post Tag Description Format', 'seo-ultimate')
		), $this->get_default_settings());
		$this->admin_form_table_end();
	}
	
	function home_tab() {
		$this->admin_form_table_start();
		$this->textarea('home_description', __('Blog Homepage Meta Description', 'seo-ultimate'), 3);
		$this->checkboxes(array(
				  'home_description_tagline_default' => __('Use this blog&#8217s tagline as the default homepage description.', 'seo-ultimate')
			), __('Default Value', 'seo-ultimate'));
		$this->admin_form_table_end();
	}
	
	function head_tag_output() {
		
		global $post;
		
		$desc = false;
		
		//If we're viewing the homepage, look for homepage meta data.
		if (is_home()) {
			$desc = $this->get_setting('home_description');
			if (!$desc && $this->get_setting('home_description_tagline_default')) $desc = get_bloginfo('description');
		
		//If we're viewing a post or page, look for its meta data.
		} elseif (is_singular()) {
			$desc = $this->get_postmeta('description');
			
			if (!trim($desc) && !post_password_required() && $format = $this->get_setting('description_posttype_'.get_post_type())) {
				
				$auto_excerpt = $post->post_content;
				$auto_excerpt = strip_shortcodes($auto_excerpt);
				$auto_excerpt = str_replace(']]>', ']]&gt;', $auto_excerpt);
				$auto_excerpt = strip_tags($auto_excerpt);
				$auto_excerpt = sustr::truncate($auto_excerpt, 150, '', true);
				
				$desc = str_replace(
					  array('{excerpt::autogen}', '{excerpt}')
					, array($auto_excerpt, strip_tags($post->post_excerpt))
					, $format);
			}
			
		//If we're viewing a term, look for its meta data.
		} elseif (suwp::is_tax()) {
			global $wp_query;
			$tax_descriptions = $this->get_setting('taxonomy_descriptions');
			$term_id  = $wp_query->get_queried_object_id();
			$term_obj = $wp_query->get_queried_object();
			$desc = isset($tax_descriptions[$term_id]) ? $tax_descriptions[$term_id] : '';
			
			if (!trim($desc) && $format = $this->get_setting('description_taxonomy_'.$term_obj->taxonomy)) {
				
				$desc = str_replace(
					  array('{description}')
					, array($term_obj->description)
					, $format);
			}
		}
		
		//Do we have a description? If so, output it.
		if ($desc) {
			$desc = su_esc_attr($desc);
			echo "\t<meta name=\"description\" content=\"$desc\" />\n";
		}
	}
	
	function postmeta_fields($fields) {
		$id = "_su_description";
		$value = su_esc_attr($this->get_postmeta('description'));
		
		$fields['20|description'] =
			  "<tr class='textarea' valign='top'>\n<th scope='row'><label for='$id'>".__('Meta Description:', 'seo-ultimate')."</label></th>\n"
			. "<td><textarea name='$id' id='$id' class='regular-text' cols='60' rows='3' tabindex='2'"
			. " onkeyup=\"javascript:document.getElementById('su_meta_description_charcount').innerHTML = document.getElementById('_su_description').value.length\">$value</textarea>"
			. "<br />".sprintf(__('You&#8217;ve entered %s characters. Most search engines use up to 140.', 'seo-ultimate'), "<strong id='su_meta_description_charcount'>".strlen($value)."</strong>")
			. "</td>\n</tr>\n"
		;
		
		return $fields;
	}
	
	function postmeta_help($help) {
		$help[] = __('<strong>Description</strong> &mdash; The value of the meta description tag. The description will often appear underneath the title in search engine results. Writing an accurate, attention-grabbing description for every post is important to ensuring a good search results clickthrough rate.', 'seo-ultimate');
		return $help;
	}
	
}

}
?>