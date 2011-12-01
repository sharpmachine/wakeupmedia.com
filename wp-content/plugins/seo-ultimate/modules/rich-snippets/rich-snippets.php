<?php
/**
 * Rich Snippet Creator Module
 * 
 * @since 3.0
 */

if (class_exists('SU_Module')) {

class SU_RichSnippets extends SU_Module {
	
	function get_module_title() { return __('Rich Snippet Creator', 'seo-ultimate'); }
	
	function get_default_settings() {
		return array(
			  'format' => 'mf'
			, 'review_terms' => __("Reviews\nReview", 'seo-ultimate')
		);
	}
	
	function init() {
		add_filter('the_content', array(&$this, 'apply_markup'));
		add_filter('su_get_postmeta-rich_snippet_type', array(&$this, 'autodetect_type'));
	}
	
	function admin_page_contents() {
		$this->admin_form_start();
		$this->radiobuttons('format', suarr::flatten_values($this->get_supported_snippet_formats(), 'label'), __('Data Format', 'seo-ultimate'));
		$this->textarea('review_terms', __('Categories/Tags That Indicate Reviews', 'seo-ultimate'));
		$this->admin_form_end();
	}
	
	function get_supported_snippet_formats() {
		
		return array(
			  'mf' => array(
				  'label' => __('Microformats (recommended)', 'seo-ultimate')
				, 'item_tags_template' => '<div class="%1$s">%2$s</div>'
				, 'property_tags_template' => '<span class="%1$s">%2$s</span>'
				, 'hidden_property_tags_template' => '<span class="%1$s"><span class="value-title" title="%2$s"></span></span>'
				)
			, 'md' => array(
				  'label' => __('HTML5 Microdata', 'seo-ultimate')
				, 'item_tags_template' => '<div itemscope itemtype="http://data-vocabulary.org/%1$s">%2$s</div>'
				, 'property_tags_template' => '<span itemprop="%1$s">%2$s</span>'
				, 'hidden_property_tags_template' => '<span itemprop="%1$s" content="%2$s"></span>'
				)
			, 'rdfa' => array(
				  'label' => __('RDFa', 'seo-ultimate')
				, 'item_tags_template' => '<div xmlns:v="http://rdf.data-vocabulary.org/#" typeof="v:%1$s">%2$s</div>'				
				, 'property_tags_template' => '<span property="v:%1$s">%2$s</span>'
				, 'hidden_property_tags_template' => '<span property="v:%1$s" content="%2$s"></span>'
				)
		);
	}
	
	function get_supported_snippet_types() {

		return array(
			//REVIEW
			  'review' => array(
				  'label' => __('Review', 'seo-ultimate')
				, 'tags' => array(
					  'mf' => 'hreview'
					, 'md' => 'Review'
					, 'rdfa' => 'Review'
				)
				, 'properties' => array(
					  'item' => array(
						  'label' => __('Item Reviewed', 'seo-ultimate')
						, 'tags' => array(
							  'mf' => array('item', 'fn')
							, 'md' => 'itemreviewed'
							, 'rdfa' => 'itemreviewed'
						)
					)
					, 'rating' => array(
						  'label' => __('Star Rating', 'seo-ultimate')
						, 'value_format' => array('%s star', '%s stars', '%s-star', '%s-stars')
						, 'tags' => 'rating'
					)
					, 'reviewer' => array(
						  'label' => __('Review Author', 'seo-ultimate')
						, 'editable' => false
						, 'value_function' => 'get_the_author'
						, 'tags' => 'reviewer'
					)
					, 'date_reviewed' => array(
						  'label' => __('Date Reviewed', 'seo-ultimate')
						, 'editable' => false
						, 'value_function' => array('get_the_time', 'Y-m-d')
						, 'tags' => 'dtreviewed'
						, 'hidden_tags' => array(
							  'mf' => 'dtreviewed'
							, 'md' => '<time itemprop="dtreviewed" datetime="%s"></time>'
							, 'rdfa' => 'dtreviewed'
						)
					)
				)
			)
		);
	}
	
	function autodetect_type($current_type) {
		if (!strlen($current_type) && $terms = suwp::get_all_the_terms()) {
			
			$types = $this->get_supported_snippet_types();
			foreach ($types as $type => $type_data) {
				
				if (suarr::any_in_array(
						  suarr::explode_lines($this->get_setting("{$type}_terms", ''))
						, suarr::flatten_values($terms, 'name')
						, false
					))
					return $type;
			}
		}
		
		return $current_type;
	}
	
	function add_tags($content, $tags, $template, $escape=true) {
		if ($escape) $content = su_esc_attr($content);
		$tags = array_reverse((array)$tags);
		foreach ($tags as $tag) {
			if (sustr::startswith($tag, '<'))
				$content = sprintf($tag, $content);
			else
				$content = sprintf($template, $tag, $content);
		}
		return $content;
	}
	
	function apply_markup($content) {
		
		//Single items only
		if (!is_singular() || !in_the_loop()) return $content;
		
		//Get the current type
		$type = $this->get_postmeta('rich_snippet_type');
		if (!strlen($type) || $type == 'none') return $content;
		
		//Get the current format
		$format = $this->get_setting('format', 'mf');
		
		//Get tag templates for the current format
		$formats = $this->get_supported_snippet_formats();
		
		//Get data for the current type
		$types = $this->get_supported_snippet_types();
		$type_data = $types[$type];
		
		//Cycle through the current type's properties
		$append = '';
		$num_properties = 0;
		foreach ($type_data['properties'] as $property => $property_data) {
			
			//Get the current value for this property
			$value = strval($this->get_postmeta("rich_snippet_{$type}_{$property}"));
			
			//If a value is not set, look for a value-generating function
			if (!strlen($value)) {
				if (isset($property_data['value_function'])) {
					$valfunc = (array)$property_data['value_function'];
					if (is_callable($valfunc[0])) {
						$valfunc_args = isset($valfunc[1]) ? (array)$valfunc[1] : array();
						$value = call_user_func_array($valfunc[0], $valfunc_args);
					}
				}
			}
			
			//If still no value, skip this property
			if (!strlen($value)) continue;
			
			//Get the property tags
			$tag = is_array($property_data['tags']) ?
						$property_data['tags'][$format] :
						$property_data['tags'];
			
			if (isset($property_data['hidden_tags'])) {
				$hidden_tag = is_array($property_data['hidden_tags']) ?
							$property_data['hidden_tags'][$format] :
							$property_data['hidden_tags'];
			} else
				$hidden_tag = $tag;
			
			//Add property tags to the value
			$markedup_value = $this->add_tags($value, $tag, $formats[$format]['property_tags_template']);
			$hidden_markedup_value = $this->add_tags($value, $hidden_tag, $formats[$format]['hidden_property_tags_template']);
			
			//Apply a value format to visible values if provided
			if (isset($property_data['value_format'])) {
				$values = array_values(sustr::batch_replace('%s', $value, $property_data['value_format']));
				$markedup_values = array_values(sustr::batch_replace('%s', $markedup_value, $property_data['value_format']));
			} else {
				$values = array($value);
				$markedup_values = array($markedup_value);
			}
			
			//Is the value in the content, and are we allowed to search/replace the content for this value?
			$count = 0;
			if (empty($property_data['always_hidden'])) {
				for ($i=0; $i<count($values); $i++) {
					$content = sustr::htmlsafe_str_replace($values[$i], $markedup_values[$i], $content, 1, $count);
					if ($count > 0) break;
				}
			}
			
			if ($count == 0)
				$append .= $hidden_markedup_value;
			
			$num_properties++;
		}
		
		if ($num_properties)
			$content = $this->add_tags("$content<div>$append</div>", $type_data['tags'][$format], $formats[$format]['item_tags_template'], false);
		
		//Return filtered content
		return $content;
	}
	
	function postmeta_fields($fields) {
		$fields['40|rich_snippet_type'] = $this->get_postmeta_dropdown('rich_snippet_type', array(
			  'none' => __('None', 'seo-ultimate')
			, 'review' => __('Review', 'seo-ultimate')
		), __('Rich Snippet Type:', 'seo-ultimate'));
		
		$fields['45|rich_snippet_review_item|rich_snippet_review_rating'] = $this->get_postmeta_subsection('rich_snippet_type', 'review',
			
			  $this->get_postmeta_textbox('rich_snippet_review_item', __('Name of Reviewed Item:', 'seo-ultimate'))
			
			. $this->get_postmeta_dropdown('rich_snippet_review_rating', array(
				  '0'   => __('None', 'seo-ultimate')
				, '0.5' => __('0.5 stars', 'seo-ultimate')
				, '1'   => __('1 star', 'seo-ultimate')
				, '1.5' => __('1.5 stars', 'seo-ultimate')
				, '2'   => __('2 stars', 'seo-ultimate')
				, '2.5' => __('2.5 stars', 'seo-ultimate')
				, '3'   => __('3 stars', 'seo-ultimate')
				, '3.5' => __('3.5 stars', 'seo-ultimate')
				, '4'   => __('4 stars', 'seo-ultimate')
				, '4.5' => __('4.5 stars', 'seo-ultimate')
				, '5'   => __('5 stars', 'seo-ultimate')
			), __('Star Rating for Reviewed Item:', 'seo-ultimate'))
		);
		
		return $fields;
	}
}

}
?>