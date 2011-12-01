<?php
/**
 * Title Tag Rewriter Module
 * 
 * @since 0.1
 */

if (class_exists('SU_Module')) {

class SU_Titles extends SU_Module {
	
	function get_module_title() { return __('Title Tag Rewriter', 'seo-ultimate'); }
	
	function init() {
		
		switch ($this->get_setting('rewrite_method', 'ob')) {
			case 'filter':
				add_filter('wp_title', array(&$this, 'get_title'));
				break;
			case 'ob':
			default:
				add_action('template_redirect', array(&$this, 'before_header'), 0);
				add_action('wp_head', array(&$this, 'after_header'), 1000);
				break;
		}
		
		add_filter('su_postmeta_help', array(&$this, 'postmeta_help'), 10);
	}
	
	function get_admin_page_tabs() {
		return array_merge(
			  array(
				  array('title' => __('Default Formats', 'seo-ultimate'), 'id' => 'su-default-formats', 'callback' => 'formats_tab')
				, array('title' => __('Settings', 'seo-ultimate'), 'id' => 'su-settings', 'callback' => 'settings_tab')
				)
			, $this->get_meta_edit_tabs(array(
				  'type' => 'textbox'
				, 'name' => 'title'
				, 'term_settings_key' => 'taxonomy_titles'
				, 'label' => __('Title Tag', 'seo-ultimate')
			))
		);
	}
	
	function formats_tab() {
		echo "<table class='form-table'>\n";
		$this->textboxes($this->get_supported_settings(), $this->get_default_settings());
		echo "</table>";
	}
	
	function settings_tab() {
		$this->admin_form_table_start();
		$this->checkbox('terms_ucwords', __('Convert lowercase category/tag names to title case when used in title tags.', 'seo-ultimate'), __('Title Tag Variables', 'seo-ultimate'));
		$this->radiobuttons('rewrite_method', array(
			  'ob' => __('Use output buffering &mdash; no configuration required, but slower (default)', 'seo-ultimate')
			, 'filter' => __('Use filtering &mdash; faster, but configuration required (see the &#8220;Settings Help&#8221; dropdown for details)', 'seo-ultimate')
		), __('Rewrite Method', 'seo-ultimate'));
		$this->admin_form_table_end();
	}
	
	function get_default_settings() {
		
		//We internationalize even non-text formats (like "{post} | {blog}") to allow RTL languages to switch the order of the variables
		return array(
			  'title_home' => __('{blog}', 'seo-ultimate')
			, 'title_single' => __('{post} | {blog}', 'seo-ultimate')
			, 'title_page' => __('{page} | {blog}', 'seo-ultimate')
			, 'title_category' => __('{category} | {blog}', 'seo-ultimate')
			, 'title_tag' => __('{tag} | {blog}', 'seo-ultimate')
			, 'title_day' => __('Archives for {month} {day}, {year} | {blog}', 'seo-ultimate')
			, 'title_month' => __('Archives for {month} {year} | {blog}', 'seo-ultimate')
			, 'title_year' => __('Archives for {year} | {blog}', 'seo-ultimate')
			, 'title_author' => __('Posts by {author} | {blog}', 'seo-ultimate')
			, 'title_search' => __('Search Results for {query} | {blog}', 'seo-ultimate')
			, 'title_404' => __('404 Not Found | {blog}', 'seo-ultimate')
			, 'title_paged' => __('{title} - Page {num}', 'seo-ultimate')
			
			, 'terms_ucwords' => true
			, 'rewrite_method' => 'ob'
		);
	}
	
	function get_supported_settings() {
		return array(
			  'title_home' => __('Blog Homepage Title', 'seo-ultimate')
			, 'title_single' => __('Post Title Format', 'seo-ultimate')
			, 'title_page' => __('Page Title Format', 'seo-ultimate')
			, 'title_category' => __('Category Title Format', 'seo-ultimate')
			, 'title_tag' => __('Tag Title Format', 'seo-ultimate')
			, 'title_day' => __('Day Archive Title Format', 'seo-ultimate')
			, 'title_month' => __('Month Archive Title Format', 'seo-ultimate')
			, 'title_year' => __('Year Archive Title Format', 'seo-ultimate')
			, 'title_author' => __('Author Archive Title Format', 'seo-ultimate')
			, 'title_search' => __('Search Title Format', 'seo-ultimate')
			, 'title_404' => __('404 Title Format', 'seo-ultimate')
			, 'title_paged' => __('Pagination Title Format', 'seo-ultimate')
		);
	}
	
	function get_title_format() {
		if ($key = $this->get_current_page_type())
			return $this->get_setting("title_$key");
		
		return false;
	}
	
	function get_current_page_type() {
		$pagetypes = $this->get_supported_settings();
		unset($pagetypes['title_paged']);
		
		foreach ($pagetypes as $key => $title) {
			$key = str_replace('title_', '', $key);
			if (call_user_func("is_$key")) return $key;
		}
		
		return false;
	}
	
	function should_rewrite_title() {
		return (!is_admin() && !is_feed());
	}
	
	function before_header() {
		if ($this->should_rewrite_title()) ob_start(array(&$this, 'change_title_tag'));
	}

	function after_header() {
		if ($this->should_rewrite_title()) {
			
			$handlers = ob_list_handlers();
			if (count($handlers) > 0 && strcasecmp($handlers[count($handlers)-1], 'SU_Titles::change_title_tag') == 0)
				ob_end_flush();
			else
				su_debug_log(__FILE__, __CLASS__, __FUNCTION__, __LINE__, "Other ob_list_handlers found:\n".print_r($handlers, true));
		}
	}
	
	function change_title_tag($head) {
		
		$title = $this->get_title();
		if (!$title) return $head;
		
		//Replace the old title with the new and return
		return eregi_replace('<title>[^<]*</title>', '<title>'.$title.'</title>', $head);
	}
	
	function get_title() {
		
		global $wp_query, $wp_locale;
		
		//Custom post/page title?
		if ($post_title = $this->get_postmeta('title'))
			return htmlspecialchars($this->get_title_paged($post_title));
		
		//Custom taxonomy title?
		if (is_category() || is_tag() || is_tax()) {
			$tax_titles = $this->get_setting('taxonomy_titles');
			if ($tax_title = $tax_titles[$wp_query->get_queried_object_id()])
				return htmlspecialchars($this->get_title_paged($tax_title));
		}
		
		//Get format
		if (!$this->should_rewrite_title()) return '';
		if (!($format = $this->get_title_format())) return '';
		
		//Load post/page titles
		$post_id = 0;
		$post_title = '';
		$parent_title = '';
		if (is_singular()) {
			$post = $wp_query->get_queried_object();
			$post_title = strip_tags( apply_filters( 'single_post_title', $post->post_title ) );
			$post_id = $post->ID;
			
			if ($parent = $post->post_parent) {
				$parent = &get_post($parent);
				$parent_title = strip_tags( apply_filters( 'single_post_title', $parent->post_title ) );
			}
		}
		
		//Load date-based archive titles
		if ($m = get_query_var('m')) {
			$year = substr($m, 0, 4);
			$monthnum = intval(substr($m, 4, 2));
			$daynum = intval(substr($m, 6, 2));
		} else {
			$year = get_query_var('year');
			$monthnum = get_query_var('monthnum');
			$daynum = get_query_var('day');
		}
		$month = $wp_locale->get_month($monthnum);
		$monthnum = zeroise($monthnum, 2);
		$day = date('jS', mktime(12,0,0,$monthnum,$daynum,$year));
		$daynum = zeroise($daynum, 2);
		
		//Load category titles
		$cat_title = $cat_titles = $cat_desc = '';
		if (is_category()) {
			$cat_title = single_cat_title('', false);
			$cat_desc = category_description();
		} elseif (count($categories = get_the_category())) {
			$cat_titles = su_lang_implode($categories, 'name');
			usort($categories, '_usort_terms_by_ID');
			$cat_title = $categories[0]->name;
			$cat_desc = category_description($categories[0]->term_id);
		}
		if (strlen($cat_title) && $this->get_setting('terms_ucwords', true))
			$cat_title = sustr::tclcwords($cat_title);
		
		//Load tag titles
		$tag_title = $tag_desc = '';
		if (is_tag()) {
			$tag_title = single_tag_title('', false);
			$tag_desc = tag_description();
			
			if ($this->get_setting('terms_ucwords', true))
				$tag_title = sustr::tclcwords($tag_title);
		}
		
		//Load author titles
		if (is_author()) {
			$author_obj = $wp_query->get_queried_object();
		} elseif (is_singular()) {
			global $authordata;
			$author_obj = $authordata;
		} else {
			$author_obj = null;
		}
		if ($author_obj)
			$author = array(
				  'username' => $author_obj->user_login
				, 'name' => $author_obj->display_name
				, 'firstname' => get_the_author_meta('first_name', $author_obj->ID)
				, 'lastname' => get_the_author_meta('last_name',  $author_obj->ID)
				, 'nickname' => get_the_author_meta('nickname',   $author_obj->ID)
			);
		else
			$author = array(
				  'username' => ''
				, 'name' => ''
				, 'firstname' => ''
				, 'lastname' => ''
				, 'nickname' => ''
			);
		
		$variables = array(
			  '{blog}' => get_bloginfo('name')
			, '{tagline}' => get_bloginfo('description')
			, '{post}' => $post_title
			, '{page}' => $post_title
			, '{page_parent}' => $parent_title
			, '{category}' => $cat_title
			, '{categories}' => $cat_titles
			, '{category_description}' => $cat_desc
			, '{tag}' => $tag_title
			, '{tag_description}' => $tag_desc
			, '{tags}' => su_lang_implode(get_the_tags($post_id), 'name', true)
			, '{daynum}' => $daynum
			, '{day}' => $day
			, '{monthnum}' => $monthnum
			, '{month}' => $month
			, '{year}' => $year
			, '{author}' => $author['name']
			, '{author_name}' => $author['name']
			, '{author_username}' => $author['username']
			, '{author_firstname}' => $author['firstname']
			, '{author_lastname}' => $author['lastname']
			, '{author_nickname}' => $author['nickname']
			, '{query}' => su_esc_attr(get_search_query())
			, '{ucquery}' => su_esc_attr(ucwords(get_search_query()))
			, '{url_words}' => $this->get_url_words($_SERVER['REQUEST_URI'])
		);
		
		$title = str_replace(array_keys($variables), array_values($variables), htmlspecialchars($format));
		
		return $this->get_title_paged($title);
	}
	
	function get_title_paged($title) {
		
		global $wp_query, $numpages;
		
		if (is_paged() || get_query_var('page')) {
			
			if (is_paged()) {
				$num = absint(get_query_var('paged'));
				$max = absint($wp_query->max_num_pages);
			} else {
				$num = absint(get_query_var('page'));
				
				if (is_singular()) {
					$post = $wp_query->get_queried_object();
					$max = count(explode('<!--nextpage-->', $post->post_content));
				} else
					$max = '';
			}
			
			return str_replace(
				array('{title}', '{num}', '{max}'),
				array( $title, $num, $max ),
				$this->get_setting('title_paged'));
		} else
			return $title;
	}
	
	function get_url_words($url) {
		
		//Remove any extensions (.html, .php, etc)
		$url = preg_replace('|\\.[a-zA-Z]{1,4}$|', ' ', $url);
		
		//Turn slashes to >>
		$url = str_replace('/', ' &raquo; ', $url);
		
		//Remove word separators
		$url = str_replace(array('.', '/', '-'), ' ', $url);
		
		//Capitalize the first letter of every word
		$url = explode(' ', $url);
		$url = array_map('trim', $url);
		$url = array_map('ucwords', $url);
		$url = implode(' ', $url);
		$url = trim($url);
		
		return $url;
	}
	
	function postmeta_fields($fields) {
		$fields['10|title'] = $this->get_postmeta_textbox('title', __('Title Tag:', 'seo-ultimate'));
		return $fields;
	}
	
	function postmeta_help($help) {
		$help[] = __('<strong>Title Tag</strong> &mdash; The exact contents of the &lt;title&gt; tag. The title appears in visitors&#8217; title bars and in search engine result titles. If this box is left blank, then the <a href="admin.php?page=su-titles" target="_blank">default post/page titles</a> are used.', 'seo-ultimate');
		return $help;
	}
}

}
?>