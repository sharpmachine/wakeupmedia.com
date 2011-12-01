<?php

class SU_InternalLinkAliases extends SU_Module {
	
	function get_default_settings() {
		return array(
			  'alias_dir' => 'go'
		);
	}
	
	function init() {
		add_filter('su_custom_update_postmeta-aliases', array(&$this, 'save_post_aliases'), 10, 4);
		add_filter('the_content', array(&$this, 'apply_aliases'), 9); //Run before wp_texturize etc.
		add_action('template_redirect', array(&$this, 'redirect_aliases'), 0);
		add_action('do_robotstxt', array(&$this, 'block_aliases_dir'));
		add_action('su_do_robotstxt', array(&$this, 'block_aliases_dir'));
		add_filter('su_get_setting-internal-link-aliases-alias_dir', array(&$this, 'filter_alias_dir'));
	}
	
	function get_module_title() { return __('Link Mask Generator', 'seo-ultimate'); }
	
	function get_parent_module() { return 'misc'; }
	function get_settings_key() { return 'internal-link-aliases'; }
	
	function admin_page_contents() {
		$this->child_admin_form_start();
		$this->textbox('alias_dir', __('Alias Directory', 'seo-ultimate'), $this->get_default_setting('alias_dir'));
		if ($this->plugin->module_exists('link-nofollow'))
			$this->checkbox('nofollow_aliased_links', __('Nofollow aliased links', 'seo-ultimate'), __('Link Attributes', 'seo-ultimate'));
		$this->child_admin_form_end();
	}
	
	function filter_alias_dir($alias_dir) {
		return trim(sustr::preg_filter('a-zA-Z0-9_/', $alias_dir), '/');
	}
	
	function postmeta_fields($fields) {
		
		$post_id = suwp::get_post_id();
		$post = get_post($post_id);
		if (!$post) return;
		$content = $post->post_content;
		
		$alias_dir = $this->get_setting('alias_dir', 'go');
		
		if ($content && preg_match_all('@ href=["\']([^"\']+)["\']@', $content, $matches)) {
			$urls = $matches[1];
			
			$html = "<tr valign='top'>\n<th scope='row' class='su'>".__('Link Masks:', 'seo-ultimate')."</th>\n<td>\n";
			
			$html .= "<table class='widefat'><thead>\n";
			$headers = array(__('URL', 'seo-ultimate'), '', __('Mask URL', 'seo-ultimate'));
			foreach ($headers as $header) $html .= "<th>$header</th>\n";
			$html .= "</thead>\n<tbody>";
			
			$aliases = $this->get_setting('aliases', array());
			$post_aliases = array();
			foreach ($aliases as $alias) {
				if (in_array($post->ID, $alias['posts']))
					$post_aliases[$alias['from']] = $alias['to'];
			}
			
			foreach ($urls as $url) {
				$a_url = su_esc_attr($url);
				$un_h_url = htmlspecialchars_decode($url);
				$ht_url = esc_html(sustr::truncate($url, 30));
				
				if (isset($post_aliases[$url]))
					$alias_to = $post_aliases[$url];
				elseif (isset($post_aliases[$un_h_url]))
					$alias_to = $post_aliases[$un_h_url];
				else
					$alias_to = '';
				
				$a_alias_to = esc_attr($alias_to);
				
				$alias_id = md5($url . serialize(array($post_id)));
				$html .= "<tr><td><a href='$a_url' title='$a_url' target='_blank'>$ht_url</a><input type='hidden' name='_su_aliases[$alias_id][from]' value='$a_url' /></td>\n<td>&rArr;</td><td>/$alias_dir/<input type='text' name='_su_aliases[$alias_id][to]' value='$a_alias_to' /></td></tr>\n";
			}
			
			$html .= "</tbody>\n</table>\n";
			
			$html .= '<p><small>' . __('You can stop search engines from following a link by typing in a mask for its URL.', 'seo-ultimate') . "</small></p>\n";
			
			$html .= "</td>\n</tr>\n";
			
			$fields['70|aliases'] = $html;
		}
		
		return $fields;
	}
	
	function save_post_aliases($false, $saved_aliases, $metakey, $post) {
		if ($post->post_type == 'revision' || !is_array($saved_aliases)) return true;
		
		$aliases = $this->get_setting('aliases', array());
		
		foreach ($saved_aliases as $id => $data) {
			$aliases[$id]['from']  = $data['from'];
			$aliases[$id]['to']    = $data['to'];
			$aliases[$id]['posts'] = array($post->ID);
		}
		
		$this->update_setting('aliases', $aliases);
		
		return true;
	}
	
	function apply_aliases($content) {
		return preg_replace_callback('@<a ([^>]*)href=(["\'])([^"\']+)(["\'])([^>]*)>@', array(&$this, 'apply_aliases_callback'), $content);
	}
	
	function apply_aliases_callback($matches) {
		$id = suwp::get_post_id();
		
		static $aliases = false;
		//Just in case we have duplicate aliases, make sure the most recent ones are applied first
		if ($aliases === false) $aliases = array_reverse($this->get_setting('aliases', array()), true);
		
		static $alias_dir = false;
		if ($alias_dir === false) $alias_dir = $this->get_setting('alias_dir', 'go');
		
		$new_url = $old_url = $matches[3];
		
		foreach ($aliases as $alias) {
			$to = $alias['to'];
			
			if (in_array($id, $alias['posts']) && $to) {
				$from = $alias['from'];
				$h_from = esc_html($from);
				$to = get_bloginfo('url') . "/$alias_dir/$to/";
				
				if ($from == $old_url || $h_from == $old_url) {
					$new_url = $to;
					break;
				}
			}
		}
		
		$attrs = "{$matches[1]}href={$matches[2]}{$new_url}{$matches[4]}{$matches[5]}";
		
		if ($this->get_setting('nofollow_aliased_links', false) && $this->plugin->module_exists('link-nofollow'))
			$this->plugin->call_module_func('link-nofollow', 'nofollow_attributes_string', $attrs, $attrs);
		
		return "<a $attrs>";
	}
	
	function redirect_aliases() {
		$aliases = $this->get_setting('aliases', array());
		$alias_dir = $this->get_setting('alias_dir', 'go');
		
		foreach ($aliases as $alias)
			if ($to = $alias['to'])
				if (suurl::equal(suurl::current(), get_bloginfo('url') . "/$alias_dir/$to/"))
					wp_redirect($alias['from']);
	}
	
	function block_aliases_dir() {
		echo '# ';
		_e('Added by Link Alias Generator (LAG) module', 'seo-ultimate');
		echo "\n";
		
		$urlinfo = parse_url(get_bloginfo('url'));
		$path = $urlinfo['path'];
		echo "User-agent: *\n";
		
		$alias_dir = $this->get_setting('alias_dir', 'go');
		echo "Disallow: $path/$alias_dir/\n";
		
		echo '# ';
		_e('End LAG', 'seo-ultimate');
		echo "\n\n";
	}

}