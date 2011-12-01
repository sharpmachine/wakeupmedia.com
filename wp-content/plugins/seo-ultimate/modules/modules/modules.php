<?php
/**
 * Module Manager Module
 * 
 * @since 0.7
 */

if (class_exists('SU_Module')) {

class SU_Modules extends SU_Module {
	
	function get_module_title() { return __('Module Manager', 'seo-ultimate'); }
	function get_menu_title() { return __('Modules', 'seo-ultimate'); }
	function get_menu_pos()   { return 0; }
	function is_menu_default(){ return true; }
	
	function init() {
		
		if ($this->is_action('update')) {
			
			$psdata = (array)get_option('seo_ultimate', array());
			
			foreach ($_POST as $key => $value) {
				if (substr($key, 0, 3) == 'su-') {
					$key = str_replace(array('su-', '-module-status'), '', $key);
					$value = intval($value);
					
					$psdata['modules'][$key] = $value;
				}
			}
			
			update_option('seo_ultimate', $psdata);
			
			wp_redirect(add_query_arg('su-modules-updated', '1', suurl::current()), 301);
			exit;
		}
	}
	
	function admin_page_contents() {
		echo "<p>";
		_e('SEO Ultimate&#8217;s features are located in groups called &#8220;modules.&#8221; By default, most of these modules are listed in the &#8220;SEO&#8221; menu on the left. Whenever you&#8217;re working with a module, you can view documentation by clicking the tabs in the upper-right-hand corner of your administration screen.', 'seo-ultimate');
		echo "</p><p>";
		_e('The Module Manager lets you  disable or hide modules you don&#8217;t use. You can also silence modules from displaying bubble alerts on the menu.', 'seo-ultimate');
		echo "</p>";
		
		if (!empty($_GET['su-modules-updated']))
			$this->print_message('success', __('Modules updated.', 'seo-ultimate'));
		
		$this->admin_form_start(false, false);
		
		$headers = array(
			  __('Status', 'seo-ultimate')
			, __('Module', 'seo-ultimate')
		);
		echo <<<STR
<table class="widefat" cellspacing="0">
	<thead><tr>
		<th scope="col" class="module-status">{$headers[0]}</th>
		<th scope="col" class="module-name">{$headers[1]}</th>
	</tr></thead>
	<tbody>

STR;
		
		$statuses = array(
			  SU_MODULE_ENABLED => __('Enabled', 'seo-ultimate')
			, SU_MODULE_SILENCED => __('Silenced', 'seo-ultimate')
			, SU_MODULE_HIDDEN => __('Hidden', 'seo-ultimate')
			, SU_MODULE_DISABLED => __('Disabled', 'seo-ultimate')
		);
		
		$modules = array();
		
		foreach ($this->plugin->modules as $key => $x_module) {
			$module =& $this->plugin->modules[$key];
			
			//On some setups, get_parent_class() returns the class name in lowercase
			if (strcasecmp(get_parent_class($module), 'SU_Module') == 0 && !in_array($key, array('modules')) && $module->is_independent_module())
				$modules[$key] = $module->get_module_title();
		}
		
		foreach ($this->plugin->disabled_modules as $key => $class) {
			
			if (call_user_func(array($class, 'is_independent_module')))
				$modules[$key] = call_user_func(array($class, 'get_module_title'));
		}
		
		asort($modules);
		
		//Do we have any modules requiring the "Silenced" column? Store that boolean in $any_hmc
		$any_hmc = false;
		foreach ($modules as $key => $name) {
			if ($this->plugin->call_module_func($key, 'has_menu_count', $hmc) && $hmc) {
				$any_hmc = true;
				break;
			}
		}
		
		$psdata = (array)get_option('seo_ultimate', array());
		
		foreach ($modules as $key => $name) {
			
			$currentstatus = $psdata['modules'][$key];
			
			echo "\t\t<tr>\n\t\t\t<td class='module-status' id='module-status-$key'>\n";
			echo "\t\t\t\t<input type='hidden' name='su-$key-module-status' id='su-$key-module-status' value='$currentstatus' />\n";
			
			foreach ($statuses as $statuscode => $statuslabel) {
				
				$hmc = ($this->plugin->call_module_func($key, 'has_menu_count', $_hmc) && $_hmc);
				
				$is_current = false;
				$style = '';
				switch ($statuscode) {
					case SU_MODULE_ENABLED:
						if ($currentstatus == SU_MODULE_SILENCED && !$hmc) $is_current = true;
						break;
					case SU_MODULE_SILENCED:
						if (!$any_hmc) continue 2; //break out of switch and foreach
						if (!$hmc) $style = " style='visibility: hidden;'";
						break;
					case SU_MODULE_HIDDEN:
						if ($this->plugin->call_module_func($key, 'get_menu_title', $module_menu_title) && $module_menu_title === false)
							$style = " style='visibility: hidden;'";
						break;
				}
				
				if ($is_current || $currentstatus == $statuscode) $current = ' current'; else $current = '';
				$codeclass = str_replace('-', 'n', strval($statuscode));
				echo "\t\t\t\t\t<span class='status-$codeclass'$style>";
				echo "<a href='javascript:void(0)' onclick=\"javascript:set_module_status('$key', $statuscode, this)\" class='$current'>$statuslabel</a></span>\n";
			}
			
			if (!$this->plugin->module_exists($key) || !$this->plugin->call_module_func($key, 'get_admin_url', $admin_url))
				$admin_url = false;
			
			if ($currentstatus > SU_MODULE_DISABLED && $admin_url) {
				$cellcontent = "<a href='{$admin_url}'>$name</a>";
			} else
				$cellcontent = $name;
			
			echo <<<STR
				</td>
				<td class='module-name'>
					$cellcontent
				</td>
			</tr>

STR;
		}
		
		echo "\t</tbody>\n</table>\n";
		
		$this->admin_form_end(null, false);
	}
}

}
?>