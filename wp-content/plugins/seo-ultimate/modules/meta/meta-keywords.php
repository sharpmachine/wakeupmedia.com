<?php
/**
 * Meta Keywords Editor Module
 * 
 * @since 4.0
 */

if (class_exists('SU_Module')) {

class SU_MetaKeywords extends SU_Module {
	
	function get_module_title() { return __('Meta Keywords Editor', 'seo-ultimate'); }
	function get_menu_title()   { return __('Meta Keywords', 'seo-ultimate'); }
	function get_settings_key() { return 'meta'; }
	function get_default_status() { return SU_MODULE_DISABLED; }
	
	function init() {
		add_action('su_head', array(&$this, 'head_tag_output'));
		add_filter('su_postmeta_help', array(&$this, 'postmeta_help'), 20);
	}
	
	function get_default_settings() {
		return array(
			  'auto_keywords_posttype_post_words_value' => 3
			, 'auto_keywords_posttype_page_words_value' => 3
			, 'auto_keywords_posttype_attachment_words_value' => 3
		);
	}
	
	function get_admin_page_tabs() {
		return array_merge(
			  array(
				  array('title' => __('Default Values', 'seo-ultimate'), 'id' => 'su-default-values', 'callback' => 'defaults_tab')
				, array('title' => __('Blog Homepage', 'seo-ultimate'), 'id' => 'su-blog-homepage', 'callback' => 'home_tab')
				)
			, $this->get_meta_edit_tabs(array(
				  'type' => 'textbox'
				, 'name' => 'keywords'
				, 'term_settings_key' => 'taxonomy_keywords'
				, 'label' => __('Meta Keywords', 'seo-ultimate')
			))
		);
	}
	
	function defaults_tab() {
		$this->admin_form_table_start();
		
		$posttypenames = suwp::get_post_type_names();
		foreach ($posttypenames as $posttypename) {
			$posttype = get_post_type_object($posttypename);
			$posttypelabel = $posttype->labels->name;
			
			$checkboxes = array();
			
			if (post_type_supports($posttypename, 'editor'))
				$checkboxes["auto_keywords_posttype_{$posttypename}_words"] = __('The %d most commonly-used words', 'seo-ultimate');
			
			$taxnames = get_object_taxonomies($posttypename);
			
			foreach ($taxnames as $taxname) {
				$taxonomy = get_taxonomy($taxname);
				$checkboxes["auto_keywords_posttype_{$posttypename}_tax_{$taxname}"] = $taxonomy->labels->name;
			}
			
			if ($checkboxes)
				$this->checkboxes($checkboxes, $posttypelabel);
		}
		
		$this->textarea('global_keywords', __('Sitewide Keywords', 'seo-ultimate') . '<br /><small><em>' . __('(Separate with commas)', 'seo-ultimate') . '</em></small>');
		
		$this->admin_form_table_end();
	}
	
	function home_tab() {
		$this->admin_form_table_start();
		$this->textarea('home_keywords', __('Blog Homepage Meta Keywords', 'seo-ultimate'), 3);
		$this->admin_form_table_end();
	}
	
	function head_tag_output() {
		global $post;
		
		$kw = false;
		
		//If we're viewing the homepage, look for homepage meta data.
		if (is_home()) {
			$kw = $this->get_setting('home_keywords');
		
		//If we're viewing a post or page...
		} elseif (is_singular()) {
			
			//...look for its meta data
			$kw = $this->get_postmeta('keywords');	
			
			//...and add default values
			if ($posttypename = get_post_type()) {
				$taxnames = get_object_taxonomies($posttypename);
				
				foreach ($taxnames as $taxname) {
					if ($this->get_setting("auto_keywords_posttype_{$posttypename}_tax_{$taxname}", false)) {
						$terms = get_the_terms(0, $taxname);
						$terms = suarr::flatten_values($terms, 'name');
						$terms = implode(',', $terms);
						$kw .= ',' . $terms;
					}
				}
				
				if ($this->get_setting("auto_keywords_posttype_{$posttypename}_words", false)) {
					$words = preg_split("/[\W+]/", strip_tags($post->post_content), null, PREG_SPLIT_NO_EMPTY);
					$words = array_count_values($words);
					arsort($words);
					$words = array_filter($words, array(&$this, 'filter_word_counts'));
					$words = array_keys($words);
					$stopwords = suarr::explode_lines($this->get_setting('words_to_remove', array(), 'slugs'));
					$words = array_diff($words, $stopwords);
					$words = array_slice($words, 0, $this->get_setting("auto_keywords_posttype_{$posttypename}_words_value"));
					$words = implode(',', $words);
					$kw .= ',' . $words;
				}
			}
			
		//If we're viewing a term, look for its meta data.
		} elseif (suwp::is_tax()) {
			global $wp_query;
			$tax_keywords = $this->get_setting('taxonomy_keywords');
			$kw = $tax_keywords[$wp_query->get_queried_object_id()];
		}
		
		if ($globals = $this->get_setting('global_keywords')) {
			if (strlen($kw)) $kw .= ',';
			$kw .= $globals;
		}
		
		$kw = str_replace(array("\r\n", "\n"), ',', $kw);
		$kw = explode(',', $kw);
		$kw = array_map('trim', $kw); //Remove extra spaces from beginning/end of keywords
		$kw = array_filter($kw); //Remove blank keywords
		$kw = suarr::array_unique_i($kw); //Remove duplicate keywords
		$kw = implode(',', $kw);
		
		//Do we have keywords? If so, output them.
		if ($kw) {
			$kw = su_esc_attr($kw);
			echo "\t<meta name=\"keywords\" content=\"$kw\" />\n";
		}
	}
	
	function filter_word_counts($count) {
		return $count > 1;
	}
	
	function postmeta_fields($fields) {	
		$fields['25|keywords'] = $this->get_postmeta_textbox('keywords', __('Meta Keywords:<br /><em>(separate with commas)</em>', 'seo-ultimate'));
		return $fields;
	}
	
	function postmeta_help($help) {
		$help[] = __('<strong>Keywords</strong> &mdash; The value of the meta keywords tag. The keywords list gives search engines a hint as to what this post/page is about. Be sure to separate keywords with commas, like so: <samp>one,two,three</samp>.', 'seo-ultimate');
		return $help;
	}
	
}

}
?>