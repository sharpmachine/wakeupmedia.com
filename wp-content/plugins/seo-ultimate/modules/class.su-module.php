<?php
/**
 * The pseudo-abstract class upon which all modules are based.
 * 
 * @abstract
 * @since 0.1
 */
class SU_Module {
	
	/********** VARIABLES **********/
	
	/**
	 * @since 0.1
	 * @var string
	 */
	var $module_key;
	
	/**
	 * Stores the parent module if applicable.
	 * 
	 * @since 1.5
	 * @var SU_Module
	 */
	var $parent_module = null;
	
	/**
	 * Stores any child modules.
	 * 
	 * @since 1.5
	 * @var array
	 */
	var $modules = array();
	
	/**
	 * Stores the module file's URL.
	 * 
	 * @since 0.1
	 * @var string
	 */
	var $module_url;
	
	/**
	 * Stores the URL to the directory containing the module file. Has trailing slash.
	 * 
	 * @since 1.5
	 * @var string
	 */
	var $module_dir_url;
	
	/**
	 * Stores the module file's URL relative to the plugin directory.
	 * 
	 * @since 2.1
	 * @var string
	 */
	var $module_rel_url;
	
	/**
	 * Stores the URL to the directory containing the module file, relative to the plugin directory. Has trailing slash.
	 * 
	 * @since 2.1
	 * @var string
	 */
	var $module_dir_rel_url;
	
	/**
	 * Stores the module's plugin page hook (the full hook with seo_page_ prefix).
	 * A reconstructed value of the get_plugin_page_hook() function, which is only available after admin init.
	 * 
	 * @since 0.1
	 * @var string
	 */
	var $plugin_page_hook;
	
	/**
	 * Contains messages that are waiting to be displayed to the user.
	 * 
	 * @since 0.1
	 * @var array
	 */
	var $messages = array();
	
	/**
	 * Stores the plugin object by reference.
	 * 
	 * @since 1.5
	 */
	var $plugin = null;
	
	
	/********** CONSTRUCTOR FUNCTION **********/
	
	/**
	 * PHP4 constructor that points to the likely-overloaded PHP5 constructor.
	 * 
	 * @since 0.1
	 * @uses __construct()
	 */
	function SU_Module() {
		$this->__construct();
	}
	
	
	/********** PSEUDO-ABSTRACT FUNCTIONS **********/
	
	/**
	 * PHP5 constructor.
	 * 
	 * @since 0.1
	 */
	function __construct() { }
	
	/**
	 * The module's official title.
	 * 
	 * @since 1.5
	 * 
	 * @return string
	 */
	function get_module_title() { return ''; }
	
	/**
	 * The title to be used by parent modules.
	 * 
	 * @since 1.5
	 * 
	 * @return string
	 */
	function get_module_subtitle() { return isset($this) ? $this->get_module_title() : ''; }
	
	/**
	 * The title of the admin page, which is displayed in the <title> and <h2> tags.
	 * Is the same as the menu title by default.
	 * 
	 * @since 0.1
	 * 
	 * @return string The title shown on this module's admin page.
	 */
	function get_page_title() { return isset($this) ? $this->get_module_title() : ''; }
	
	/**
	 * The title that appears on the administration navigation menu.
	 * 
	 * @since 0.1
	 * 
	 * @return string The title shown on the admin menu.
	 */
	function get_menu_title() { return isset($this) ? $this->get_module_title() : ''; }
	
	/**
	 * Determines where this module's admin page should appear relative to those of other modules.
	 * If two modules have the same menu position index, they are sorted alphabetically.
	 * 
	 * @since 0.1
	 * 
	 * @return int The menu position index.
	 */
	function get_menu_pos()   { return 10; }
	
	/**
	 * Determines where this module's admin contents should appear on the parent page relative to those of other sibling modules.
	 * If two modules have the same order index, they are sorted alphabetically.
	 * 
	 * @since 1.5
	 * 
	 * @return int The child order index.
	 */
	function get_child_order() { return 999; }
	
	/**
	 * The number that should be displayed in a bubble next to the module's menu title.
	 * A return value of zero means no bubble is shown.
	 * 
	 * @since 0.1
	 * 
	 * @return int The number that should be displayed.
	 */
	function get_menu_count() {
		$count = 0;
		foreach ($this->modules as $key => $module) {
			$count += $this->modules[$key]->get_menu_count();
		}
		return $count;
	}
	
	/**
	 * Whether or not the module will ever return a non-zero menu count.
	 * 
	 * @since 1.5
	 * 
	 * @return boolean
	 */
	function has_menu_count() { return false; }
	
	/**
	 * A descriptive label of the menu count.
	 * 
	 * @since 0.3
	 * 
	 * @return string The label.
	 */
	function get_menu_count_label() { return ''; }
	
	/**
	 * Indicates under which top-level menu this module's admin page should go.
	 * Examples: seo (This plugin's SEO menu), options-general.php (The Settings menu)
	 * 
	 * @since 0.1
	 * 
	 * @return string The value to pass to WordPress's add_submenu_page() function.
	 */
	function get_menu_parent(){ return 'seo'; }
	
	/**
	 * Returns the hook of this module's menu parent.
	 * Examples: seo (This plugin's SEO menu), settings (The Settings menu), toplevel (The toplevel)
	 * 
	 * @since 0.1
	 * 
	 * @return string The hook of the module's menu parent.
	 */
	function get_menu_parent_hook() { return $this->get_menu_parent(); }
	
	/**
	 * The status (enabled/silenced/hidden) of the module when the module is newly added to the plugin.
	 * 
	 * @since 1.5
	 * 
	 * @return int Either SU_MODULE_ENABLED, SU_MODULE_SILENCED, or SU_MODULE_HIDDEN.
	 */
	function get_default_status() { return SU_MODULE_ENABLED; }
	
	/**
	 * The module key of this module's parent. Defaults to false (no parent).
	 * 
	 * @since 0.3
	 * 
	 * @return string|bool
	 */
	function get_parent_module() { return false; }
	
	/**
	 * Returns an array of admin page tabs; the label is the key and the callback is the value.
	 * 
	 * @since 1.5
	 * 
	 * @return array
	 */
	function get_admin_page_tabs() { return array(); }
	
	/**
	 * Whether or not the module can "exist on its own."
	 * Determines whether or not the module appears in the Module Manager.
	 * 
	 * @since 1.5
	 * 
	 * @return bool
	 */
	function is_independent_module() {
		return true;
	}
	
	/**
	 * The array key of the plugin's settings array in which this module's settings are stored.
	 * 
	 * @since 1.5
	 * 
	 * @return string
	 */
	function get_settings_key() {
		if (strlen($parent = $this->get_parent_module()) && !$this->is_independent_module())
			return $this->plugin->modules[$parent]->get_settings_key();
		else
			return $this->get_module_key();
	}
	
	/**
	 * Whether or not this module should be the default screen for the "SEO" menu.
	 * 
	 * @since 1.5
	 * @return bool
	 */
	function is_menu_default() { return false; }
	
	/**
	 * Called after the module has been constructed and its variables have been filled.
	 * 
	 * @since 3.9
	 */
	function load() {}
	
	/**
	 * Called at WordPress's init hook.
	 * 
	 * @since 0.1
	 */
	function init() {}
	
	/**
	 * Called upon module activation,
	 * i.e. when a module is uploaded or when the plugin is activated for the first time.
	 * 
	 * @since 0.1
	 */
	function activate() { }
	
	/**
	 * Called when SEO Ultimate has just been upgraded to a new version.
	 * 
	 * @since 2.1
	 */
	function upgrade() { }
	
	/**
	 * Returns an array of default settings. The defaults will be saved in the database if the settings don't exist.
	 * 
	 * @since 0.1
	 * 
	 * @return array The default settings. (The setting name is the key, and the default value is the array value.)
	 */
	function get_default_settings() { return array(); }
	
	/**
	 * Is called at WordPress' admin_init hook when this module's admin page is showing.
	 * 
	 * @since 6.0
	 */
	function admin_page_init() { }
	
	/**
	 * The contents of the administration page.
	 * 
	 * @since 0.1
	 */
	function admin_page_contents() {
		$this->children_admin_page_tabs_form();
	}
	
	/**
	 * Returns a list of possible admin table columns that should be registered in "Screen Options"
	 * 
	 * @since 2.1
	 * 
	 * @return array
	 */
	function get_admin_table_columns() {
		return array();
	}
	
	/**
	 * Returns an array of custom contextual help dropdowns; internationalized titles are the array keys and contents are the array values.
	 * 
	 * @since 1.5
	 * @uses sumd::get_sections()
	 * @uses sumd::get_section()
	 * @uses SEO_Ultimate::get_translated_mdoc_path()
	 * @uses SEO_Ultimate::get_mdoc_path()
	 * 
	 * @return array
	 */
	function get_admin_dropdowns() {
		
		$paths = array($this->plugin->get_translated_mdoc_path(), $this->plugin->get_mdoc_path());
		
		foreach ($paths as $path) {
			if (is_readable($path)) {
				$readme = file_get_contents($path);
				$sections = sumd::get_sections(sumd::get_section($readme, $this->get_module_title()));
				if (count($sections)) {
					
					if (sustr::has($path, '/translations/') && preg_match("|\nStable tag: ([a-zA-Z0-9. ]+)|i", $readme, $matches)) {
						$version = $matches[1];
						if (version_compare($version, SU_VERSION, '<'))
							$sections = suarr::aprintf(false, '%s<p><em>'
								. __('(Note: This translated documentation was designed for an older version of SEO Ultimate and may be outdated.)', 'seo-ultimate')
								. '</em></p>'
							, $sections);
					}
					
					return $sections;
					
				} elseif (count($this->modules)) {
					$sections = array();
					foreach ($this->modules as $key => $x_module) {
						$module_title = $this->modules[$key]->get_module_title();
						$section = trim(sumd::get_section($readme, $module_title));
						
						if ($section) {
							$section_html = '';
							$subsections = sumd::get_sections($section);
							foreach ($subsections as $subsection_header => $subsection) {
								$section_html .= '<h6>' . trim($subsection_header, "\r\n= ") . "</h6>\n" . $subsection . "\n\n";
							}
							
							$sections[$module_title] = $section_html;
						}
					}
					
					if (count($sections))
						return $sections;
				}
			}
		}
		
		return array();
	}
	
	/**
	 * Adds the module's post meta box field HTML to the array.
	 * 
	 * @since 0.1
	 * 
	 * @param array $fields The fields array.
	 * @return array The updated fields array.
	 */
	function postmeta_fields($fields) { return $fields;	}
	
	/********** INITIALIZATION FUNCTIONALITY **********/
	
	/**
	 * If settings are unset, apply the defaults if available.
	 * 
	 * @since 0.5
	 * @uses get_default_settings()
	 * @uses get_setting()
	 * @uses update_setting()
	 */
	function load_default_settings() {
		
		$defaults = $this->get_default_settings();
		foreach ($defaults as $setting => $default) {
			if ($this->get_setting($setting, "{reset}") === "{reset}") {
				$this->update_setting($setting, $default, null, null);
			}
		}
	}
	
	
	/********** MODULE FUNCTIONS **********/
	
	/**
	 * Returns the array key of the module.
	 * 
	 * @since 0.1
	 * @uses $module_key
	 * 
	 * @return string The module key.
	 */
	function get_module_key() {
		if ($this->module_key)
			return $this->module_key;
		else
			die(str_rot13('Zbqhyr ybnqrq sebz na rkgreany fbhepr!'));
	}
	
	/**
	 * Returns the key of the parent module if there is one; if not, the key of the current module.
	 * 
	 * @since 2.1
	 * 
	 * @return string
	 */
	function get_module_or_parent_key() {
		return (strlen($p = $this->get_parent_module()) && $this->plugin->module_exists($p)) ? $p : $this->get_module_key();
	}
	
	/**
	 * Returns the absolute URL of the module's admin page.
	 * 
	 * @since 0.7
	 * @uses get_parent_module()
	 * @uses get_module_key()
	 * @uses SEO_Ultimate::key_to_hook()
	 * 
	 * @param string|false $key The key of the module for which to generate the admin URL. Optional.
	 * @return string The absolute URL to the admin page.
	 */
	function get_admin_url($key = false) {
		
		$anchor = '';
		if ($key === false) {
			if (($key = $this->get_parent_module()) && $this->plugin->module_exists($key)) {
				
				if (count($tabs = $this->get_admin_page_tabs())) {
					$first_tab = reset($tabs);
					$anchor = '#' . $first_tab['id'];
				} else {
					$anchor = '#' . $this->plugin->key_to_hook($this->get_module_key());
				}
			} else
				$key = $this->get_module_key();
		}
		
		if (!$this->plugin->call_module_func($key, 'get_menu_title', $menu_title) || !$menu_title)
			return false;
		
		$basepage = 'admin.php';
		if ($this->plugin->call_module_func($key, 'get_menu_parent', $custom_basepage) && sustr::endswith($custom_basepage, '.php'))
			$basepage = $custom_basepage;
		
		return admin_url($basepage.'?page='.$this->plugin->key_to_hook($key).$anchor);
	}
	
	/**
	 * Returns an <a> link to the module's admin page, if the module is enabled.
	 * 
	 * @since 1.0
	 * @uses get_admin_url()
	 * 
	 * @param string|false $key The key of the module for which to generate the admin URL.
	 * @param string $label The text to go inside the <a> element.
	 * @return string The <a> element, if the module exists; otherwise, the label by itself.
	 */
	function get_admin_link($key, $label) {
	
		if ($key == false || $this->plugin->module_exists($key))
			return sprintf('<a href="%s">%s</a>', $this->get_admin_url($key), $label);
		else
			return $label;
	}
	
	/**
	 * Returns a boolean indicating whether the user is currently viewing this module's admin page.
	 * 
	 * @since 1.1.1
	 * 
	 * @return bool Whether the user is currently viewing this module's admin page.
	 */
	function is_module_admin_page() {
		if (is_admin()) {
			global $plugin_page;
			if (strcmp($plugin_page, $this->plugin->key_to_hook($this->get_module_or_parent_key())) == 0) return true;
		}
		
		return false;
	}
	
	/**
	 * Returns the filename of the module's icon URL.
	 * 
	 * @since 1.5
	 * 
	 * @return string
	 */
	function get_menu_icon_filename() {
		$filenames = array(
			  $this->get_settings_key()
			, $this->get_module_key()
			, $this->get_parent_module()
		);
		
		foreach ($filenames as $filename) {
			$image = $this->module_dir_url.$filename.'.png';
			if (is_readable($image)) return $image;
		}
		
		return '';
	}
	
	
	/********** CHILD MODULE FUNCTIONS **********/
	
	/**
	 * Finds child modules of this module and fills $this->modules accordingly.
	 * 
	 * @since 1.5
	 */
	function load_child_modules() {
		foreach ($this->plugin->modules as $key => $x_module) {
			if ($key != $this->get_module_key()) {
				$module =& $this->plugin->modules[$key];
				if ($module->get_parent_module() == $this->get_module_key()) {
					$module->parent_module =& $this;
					$this->modules[$key] =& $module;
				}
			}
		}
		
		if (count($this->modules) > 0)
			@uasort($this->modules, array(&$this, 'module_sort_callback'));
	}
	
	/**
	 * Returns an array of this module's admin tabs plus those of its children.
	 * 
	 * @since 1.5
	 * @return array
	 */
	function get_children_admin_page_tabs() {
		$tabs = $this->get_admin_page_tabs();
		
		foreach ($this->modules as $key => $x_module) {
			$module =& $this->modules[$key];
			$child_tabs = $module->get_admin_page_tabs();
			
			if (empty($child_tabs))
				$child_tabs[] = array(
					  'title' => $module->get_module_subtitle()
					, 'id' => $this->plugin->key_to_hook($key)
					, 'callback' => array(&$module, 'admin_page_contents')
				);
			
			foreach ($child_tabs as $child_tab) {
				if (is_array($child_tab) && !is_array($child_tab['callback'])) $child_tab['callback'] = array(&$module, $child_tab['callback']);
				$tabs[] = $child_tab;
			}
		}
		
		return $tabs;
	}
	
	/**
	 * Outputs this module's admin tabs plus those of its children.
	 * 
	 * @since 1.5
	 */
	function children_admin_page_tabs() {
		if (count($tabs = $this->get_children_admin_page_tabs()))
			$this->admin_page_tabs($tabs);
	}
	
	/**
	 * Outputs a form containing this module's admin tabs plus those of its children.
	 * 
	 * @since 1.5
	 */
	function children_admin_page_tabs_form() {
		if (count($tabs = $this->get_children_admin_page_tabs())) {
			$this->admin_form_start(false, false);
			$this->admin_page_tabs($tabs);
			$this->admin_form_end(null, false);
		}
	}
	
	/**
	 * Outputs the admin pages of this module's children, one after the other.
	 * 
	 * @since 1.5
	 */
	function children_admin_pages() {
		foreach ($this->modules as $key => $x_module) {
			$this->modules[$key]->admin_subheader($this->modules[$key]->get_module_subtitle(), $this->plugin->key_to_hook($key));
			$this->modules[$key]->admin_page_contents();
		}
	}
	
	/**
	 * Outputs a form containing the admin pages of this module's children, outputted one after the other.
	 * 
	 * @since 1.5
	 */
	function children_admin_pages_form() {
		if (count($this->modules)) {
			$this->admin_form_start();
			$this->children_admin_pages();
			$this->admin_form_end();
		} else
			$this->print_message('warning', sprintf(__('All the modules on this page have been disabled. You can re-enable them using the <a href="%s">Module Manager</a>.', 'seo-ultimate'), $this->get_admin_url('modules')));
	}
	
	/**
	 * Compares two modules to determine which of the two should be displayed first on the parent page.
	 * Sorts by child order first, and title second.
	 * Works as a uasort() callback.
	 * 
	 * @since 1.5
	 * @uses SU_Module::get_child_order()
	 * @uses SU_Module::get_module_subtitle()
	 * 
	 * @param SU_Module $a The first module to compare.
	 * @param SU_Module $b The second module to compare.
	 * @return int This will be -1 if $a comes first, or 1 if $b comes first.
	 */
	function module_sort_callback($a, $b) {
		
		if ($a->get_child_order() == $b->get_child_order()) {
			return strcmp($a->get_module_subtitle(), $b->get_module_subtitle());
		}
		
		return ($a->get_child_order() < $b->get_child_order()) ? -1 : 1;
	}
	
	/********** SETTINGS FUNCTIONS **********/
	
	/**
	 * Retrieves the given setting from a module's settings array.
	 * 
	 * @since 0.1
	 * @uses get_settings_key()
	 * 
	 * @param string $key The name of the setting to retrieve.
	 * @param mixed $default What should be returned if the setting does not exist. Optional.
	 * @param string|null $module The module to which the setting belongs. Defaults to the current module's settings key. Optional.
	 * @return mixed The value of the setting, or the $default variable.
	 */
	function get_setting($key, $default=null, $module=null, $sneakpeak=false) {
		if (!$module) $module = $this->get_settings_key();
		
		$msdata = (array)get_option("seo_ultimate_module_$module", array());
		
		if ($sneakpeak && $this->is_action('update'))
			$setting = stripslashes(isset($_POST[$key]) ? $_POST[$key] : null);
		elseif (isset($msdata[$key]))
			$setting = $msdata[$key];
		else
			$setting = $default;
		
		$setting = apply_filters("su_get_setting-$module", $setting, $key);
		$setting = apply_filters("su_get_setting-$module-$key", $setting, $key);
		
		return $setting;
	}
	
	/**
	 * Sets a value in the module's settings array.
	 * 
	 * @since 0.1
	 * @uses get_settings_key()
	 * 
	 * @param string $key The key of the setting to be changed.
	 * @param string $value The new value to assign to the setting.
	 * @param string|null $module The module to which the setting belongs. Defaults to the current module's settings key. Optional.
	 */
	function update_setting($key, $value, $module=null, $array_key=null) {
		if (!$module) $module = $this->get_settings_key();
		
		$msdata = (array)get_option("seo_ultimate_module_$module", array());
		
		$use_custom  = 	apply_filters("su_custom_update_setting-$module-$key", false, $value, $key) ||
						apply_filters("su_custom_update_setting-$module", false, $value, $key);
		
		if (!$use_custom) {
			if ($array_key)
				$msdata[$key][$array_key] = $value;
			else
				$msdata[$key] = $value;
		}
		
		update_option("seo_ultimate_module_$module", $msdata);
	}
	
	/**
	 * Adds 1 to the value of an integer setting in the module's settings array.
	 * 
	 * @since 0.1
	 * @uses get_setting()
	 * @uses update_setting()
	 * 
	 * @param string $key The key of the setting to be incremented.
	 */
	function increment_setting($key) {
		$value = $this->get_setting($key);
		$this->update_setting($key, $value+1);
	}
	
	/**
	 * Assigns a value of zero to a setting in the module's settings array.
	 * 
	 * @since 0.1
	 * @uses update_setting()
	 * 
	 * @param string $key The key of the setting to be reset.
	 */
	function reset_setting($key) {
		$this->update_setting($key, 0);
	}
	
	/**
	 * Gets a setting's value, deletes the setting, and returns the value.
	 * 
	 * @since 2.1
	 * @uses get_settings_key()
	 * 
	 * @param string $key The name of the setting to retrieve/delete.
	 * @param mixed $default What should be returned if the setting does not exist. Optional.
	 * @param string|null $module The module to which the setting belongs. Defaults to the current module's settings key. Optional.
	 * @return mixed The value of the setting, or the $default variable.
	 */
	function flush_setting($key, $default=null, $module=null) {
		$setting = $this->get_setting($key, $default, $module); //We need to retrieve the setting before deleting it
		$this->delete_setting($key, $module);
		return $setting;
	}
	
	/**
	 * Deletes a module setting.
	 * 
	 * @since 2.1
	 * @uses get_settings_key()
	 * 
	 * @param string $key The name of the setting to delete.
	 * @param string|null $module The module to which the setting belongs. Defaults to the current module's settings key. Optional.
	 */
	function delete_setting($key, $module=null, $array_key = null) {
		if (!$module) $module = $this->get_settings_key();
		
		$msdata = (array)get_option("seo_ultimate_module_$module", array());
		
		if (isset($msdata[$key])) {
			if ($array_key)
				unset($msdata[$key][$array_key]);
			else
				unset($msdata[$key]);
		}
	}
	
	/**
	 * Updates the value of more than one setting at a time.
	 * 
	 * @since 0.1
	 * @uses update_setting()
	 * 
	 * @param array $settings The names (keys) and values of settings to be updated.
	 */
	function update_settings($settings) {
		foreach ($settings as $key => $value)
			update_setting($key, $value, null, null);
	}
	
	/**
	 * Returns a default setting. Only use this function if a default is indeed provided!
	 * 
	 * @since 1.3
	 * @uses get_default_settings()
	 * 
	 * @param string $key The name of the setting whose default to retrieve.
	 * @return mixed The default value for the setting.
	 */
	function get_default_setting($key) {
		$defaults = $this->get_default_settings();
		return $defaults[$key];
	}
	
	
	/********** ADMIN PAGE FUNCTIONS **********/
	
	/**
	 * Displays the beginning, contents, and end of the module's administration page.
	 * 
	 * @since 0.1
	 * @uses admin_page_start()
	 * @uses admin_page_contents()
	 * @uses admin_page_end()
	 */
	function admin_page() {
		if (!apply_filters('su_custom_admin_page-'.$this->get_module_key(), false)) {
			$this->admin_page_start();
			$this->admin_page_contents();
			$this->admin_page_end();
		}
	}
	
	/**
	 * Outputs the starting code for an administration page: 
	 * wrapper, ID'd <div>, icon, and title
	 * 
	 * @since 0.1
	 * @uses admin_footer() Hooked into WordPress's in_admin_footer action.
	 * @uses screen_meta_filter() Hooked into our screen_meta filter
	 * @uses get_module_key()
	 * @uses get_page_title()
	 * 
	 * @param string $icon The ID that should be applied to the icon element. The icon is loaded via CSS based on the ID. Optional.
	 */
	function admin_page_start($icon = 'options-general') {
		
		//Add our custom footer attribution
		add_action('in_admin_footer', array(&$this, 'admin_footer'));
		
		//Add our custom contextual help
		add_filter('screen_meta', array(&$this, 'screen_meta_filter'));
		
		//Output the beginning of the admin screen
		echo "<div class=\"wrap\">\n";
		
		if (strcmp($pclass = strtolower(get_parent_class($this)), 'su_module') != 0)
			$class = ' '.str_replace('_', '-', $pclass);
		else
			$class = '';
		
		echo "<div id=\"su-".su_esc_attr($this->get_module_key())."\" class=\"su-module$class\">\n";
		screen_icon($icon);
		echo "\n<h2>".$this->get_page_title()."</h2>\n";
	}
	
	/**
	 * Outputs an administration page subheader (an <h4> tag).
	 * 
	 * @since 0.1
	 * 
	 * @param string $title The text to output.
	 */
	function admin_subheader($title, $id=false) {
		if ($id) $id = ' id="' . su_esc_attr($id) . '"';
		echo "<h4 class='su-subheader'$id>$title</h4>\n";
	}
	
	/**
	 * Outputs an administration form table subheader.
	 * 
	 * @since 0.1
	 * 
	 * @param string $title The text to output.
	 */
	function admin_form_subheader($title) {
		echo "<tr><th colspan='2'><strong>$title</strong></th></tr>\n";
	}
	
	/**
	 * Outputs the ending code for an administration page.
	 * 
	 * @since 0.1
	 */
	function admin_page_end() {
		echo "\n</div>\n</div>\n";
	}
	
	/**
	 * Outputs a tab control and loads the current tab.
	 * 
	 * @since 0.7
	 * @uses get_admin_url()
	 * @uses SEO_Ultimate::plugin_dir_url
	 * 
	 * @param array $tabs The internationalized tab titles are the array keys, and the references to the functions that display the tab contents are the array values.
	 * @param bool $table Whether or not the tab contents should be wrapped in a form table.
	 */
	function admin_page_tabs($tabs = array(), $table=false) {
		
		if ($c = count($tabs)) {
			
			if ($c > 1)
				echo "\n\n<div id='su-tabset' class='su-tabs'>\n";
			
			foreach ($tabs as $tab) {
				
				if (isset($tab['title']))	$title	  = $tab['title'];	  else return;
				if (isset($tab['id']))		$id		  = $tab['id'];		  else return;
				if (isset($tab['callback']))$function = $tab['callback']; else return;
				
				if ($c > 1) {
					//$id = 'su-' . sustr::preg_filter('a-z0-9', strtolower($title));
					echo "<fieldset id='$id'>\n<h3>$title</h3>\n<div class='su-tab-contents'>\n";
				}
				
				if ($table) echo "<table class='form-table'>\n";
				
				$call = $args = array();
				
				if (is_array($function)) {
					
					if (is_array($function[0])) {
						$call = array_shift($function);
						$args = $function;
					} elseif (is_string($function[0])) {
						$call = array_shift($function);
						$call = array(&$this, $call);
						$args = $function;
					} else {
						$call = $function;
					}
				} else {
					$call = array(&$this, $function);
				}
				if (is_callable($call)) call_user_func_array($call, $args);
				
				if ($table) echo "</table>";
				
				if ($c > 1)
					echo "</div>\n</fieldset>\n";
			}
			
			if ($c > 1) {
				echo "</div>\n";
				
				echo '<script type="text/javascript" src="'.$this->plugin->plugin_dir_url.'includes/tabs.js?v='.SU_VERSION.'"></script>';
			}
		}
	}
	
	/**
	 * Adds the hook necessary to initialize the admin page tabs.
	 * 
	 * @since 0.8
	 */
	function admin_page_tabs_init() {
		add_action('admin_enqueue_scripts', array(&$this, 'admin_page_tabs_js'));
	}
	
	/**
	 * Enqueues the JavaScript needed for the admin page tabs.
	 * 
	 * @since 0.8
	 * @uses is_module_admin_page()
	 */
	function admin_page_tabs_js() {
		if ($this->is_module_admin_page())
			wp_enqueue_script('jquery-ui-tabs');
	}
	
	/**
	 * Adds the module's custom screen meta, if present.
	 * 
	 * @since 0.9
	 * @uses get_admin_dropdowns()
	 */
	function screen_meta_filter($screen_meta) {
		
		$sections = array_reverse($this->get_admin_dropdowns());
		
		if (is_array($sections) && count($sections)) {
			foreach ($sections as $label => $text) {
				$key = preg_replace('|[^a-z]|', '', strtolower($label));
				$label = htmlspecialchars($label);
				$content  = "<div class='su-help'>\n";
				
				$header = sprintf(_x('%s &mdash; %s', 'Dropdown Title', 'seo-ultimate'), $this->get_module_title(), $label);
				$header = sustr::remove_double_words($header);
				
				$text = wptexturize(Markdown($text));
				$text = str_replace('<a ', '<a target="_blank" ', $text);
				
				$content .= "<h5>$header</h5>\n\n";
				$content .= $text;
				$content .= "\n</div>\n";
				$screen_meta[] = compact('key', 'label', 'content');
			}
			
			echo "<script type='text/javascript'>jQuery(function($) { $('#contextual-help-link').css('display', 'none'); });</script>";
		}
		
		return $screen_meta;
	}
	
	/**
	 * Adds plugin/module information to the admin footer.
	 * 
	 * @since 0.1
	 * @uses SU_PLUGIN_URI
	 * @uses SU_PLUGIN_NAME
	 * @uses SU_AUTHOR_URI
	 * @uses SU_AUTHOR
	 */
	function admin_footer() {
		printf(__('%1$s | %2$s %3$s by %4$s', 'seo-ultimate'),
			$this->get_module_title(),
			'<a href="'.SU_PLUGIN_URI.'" target="_blank">'.__(SU_PLUGIN_NAME, 'seo-ultimate').'</a>',
			SU_VERSION,
			'<a href="'.SU_AUTHOR_URI.'" target="_blank">'.__(SU_AUTHOR, 'seo-ultimate').'</a>'
		);
		
		echo "<br />";
	}
	
	/**
	 * Returns tabs for post/taxonomy meta editing tables.
	 * 
	 * @since 2.9
	 * @uses get_postmeta_edit_tabs()
	 * @uses get_taxmeta_edit_tabs()
	 * 
	 * @param array $fields The array of meta fields that the user can edit with the tables.
	 */
	function get_meta_edit_tabs($fields) {
		return array_merge(
			$this->get_postmeta_edit_tabs($fields)
			,$this->get_taxmeta_edit_tabs($fields)
		);
	}
	
	/**
	 * Returns tabs for post meta editing tables.
	 * 
	 * @since 2.9
	 * 
	 * @param array $fields The array of meta fields that the user can edit with the tables.
	 */
	function get_postmeta_edit_tabs($fields) {
		
		$types = suwp::get_post_type_objects();
		
		//Turn the types array into a tabs array
		$tabs = array();
		foreach ($types as $type)
			$tabs[] = array(
				  'title' => $type->labels->name
				, 'id' => 'su-' . $type->name
				, 'callback' => array('meta_edit_tab', 'post', 'su-' . $type->name, $type->name, $type->labels->singular_name, $fields)
			);
		return $tabs;
	}
	
	/**
	 * Returns tabs for taxonomy meta editing tables.
	 * 
	 * @since 2.9
	 * 
	 * @param array $fields The array of meta fields that the user can edit with the tables.
	 */
	function get_taxmeta_edit_tabs($fields) {
		$types = suwp::get_taxonomies();
		
		//Turn the types array into a tabs array
		$tabs = array();
		foreach ($types as $name => $type) {
			if ($type->labels->name) {
				$tabs[] = array(
					  'title' => $type->labels->name
					, 'id' => 'su-' . $name
					, 'callback' => array('meta_edit_tab', 'term', 'su-' . $name, $name, $type->labels->singular_name, $fields)
				);
			}
		}
		return $tabs;
	}
	
	/**
	 * Outputs the contents of a meta editing tab.
	 * 
	 * @since 2.9
	 */
	function meta_edit_tab($genus, $tab, $type, $type_label, $fields) {
		if (!$this->meta_edit_table($genus, $tab, $type, $type_label, $fields))
			$this->print_message('info', __('Your site currently doesn&#8217;t have any public items of this type.', 'seo-ultimate'));
	}
	
	/**
	 * Outputs the contents of a meta editing table.
	 * 
	 * @since 2.9
	 * 
	 * @param string $genus The type of object being handled (either 'post' or 'term')
	 * @param string $tab The ID of the current tab; used to generate a URL hash (e.g. #$tab)
	 * @param string $type The type of post/taxonomy type being edited (examples: post, page, attachment, category, post_tag)
	 * @param string $type_label The singular label for the post/taxonomy type (examples: Post, Page, Attachment, Category, Post Tag)
	 * @param array $fields The array of meta fields that the user can edit with the tables. The data for each meta field are stored in an array with these elements: "type" (can be textbox, textarea, or checkbox), "name" (the meta field, e.g. title or description), "term_settings_key" (the key of the setting for cases when term meta data are stored in the settings array), and "label" (the internationalized label of the field, e.g. "Meta Description" or "Title Tag")
	 */
	function meta_edit_table($genus, $tab, $type, $type_label, $fields) {
		
		//Pseudo-constant
		$per_page = 100;
		
		//Sanitize parameters
		if (!is_array($fields) || !count($fields)) return false;
		if (!isset($fields[0]) || !is_array($fields[0])) $fields = array($fields);
		
		//Get search query
		$type_s = $type . '_s';
		$search = isset($_REQUEST[$type_s]) ? $_REQUEST[$type_s] : '';
		
		//Save meta if applicable
		if ($is_update = ($this->is_action('update') && !strlen(trim($search)))) {
			foreach ($_POST as $key => $value)
				if (sustr::startswith($key, $genus.'_'))
					foreach ($fields as $field)
						if (preg_match("/{$genus}_([0-9]+)_{$field['name']}/", $key, $matches)) {
							$id = (int)$matches[1];
							switch ($genus) {
								case 'post': update_post_meta($id, "_su_{$field['name']}", $_POST[$key]); break;
								case 'term': $this->update_setting($field['term_settings_key'], $_POST[$key], null, $id); break;
							}
							continue 2; //Go to next $_POST item
						}
		}
		
		$pagenum = isset( $_GET[$type . '_paged'] ) ? absint( $_GET[$type . '_paged'] ) : 0;
		if ( empty($pagenum) ) $pagenum = 1;
		
		//Load up the objects based on the genus
		switch ($genus) {
			case 'post':
				
				//Get the posts
				wp(array(
					  'post_type' => $type
					, 'posts_per_page' => $per_page
					, 'paged' => $pagenum
					, 'order' => 'ASC'
					, 'orderby' => 'title'
					, 's' => $search
				));
				global $wp_query;
				$objects = &$wp_query->posts;
				
				$num_pages = $wp_query->max_num_pages;
				$total_objects = $wp_query->found_posts;
				
				break;
				
			case 'term':
				$objects = get_terms($type, array('search' => $search));
				$total_objects = count($objects);
				$num_pages = ceil($total_objects / $per_page);
				$objects = array_slice($objects, $per_page * ($pagenum-1), $per_page);
				break;
			default:
				return false;
				break;
		}
		
		if ($total_objects < 1) return false;
		
		echo "\n<div class='su-meta-edit-table'>\n";
		
		$page_links = paginate_links( array(
			  'base' => add_query_arg( $type . '_paged', '%#%' ) . '#' . $tab
			, 'format' => ''
			, 'prev_text' => __('&laquo;')
			, 'next_text' => __('&raquo;')
			, 'total' => $num_pages
			, 'current' => $pagenum
		));
		
		if ( $page_links ) {
			$page_links_text = '<div class="tablenav"><div class="tablenav-pages">';
			$page_links_text .= sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
					number_format_i18n( ( $pagenum - 1 ) * $per_page + 1 ),
					number_format_i18n( min( $pagenum * $per_page, $total_objects ) ),
					number_format_i18n( $total_objects ),
					$page_links
					);
			$page_links_text .= "</div></div>\n";
			
			echo $page_links_text;
		} else $page_links_text = '';
		
		//Get object identification headers
		$headers = array(
			  'actions' => __('Actions', 'seo-ultimate')
			, 'id' => __('ID', 'seo-ultimate')
			, 'name' => $type_label
		);
		
		//Get meta field headers
		foreach ($fields as $field) {
			$headers[$field['name']] = $field['label'];
		}
		
		//Output all headers
		$this->admin_wftable_start($headers);
		
		//Output rows
		foreach ($objects as $object) {
			
			switch ($genus) {
				case 'post':
					$id = intval($object->ID);
					$name = $object->post_title;
					$view_url = get_permalink($id);
					$edit_url = get_edit_post_link($id);
					break;
				case 'term':
					$id = intval($object->term_id);
					$name = $object->name;
					$view_url = get_term_link($id, $type);
					$edit_url = suwp::get_edit_term_link($id, $type);
					break;
				default: return false; break;
			}
			
			$view_url = su_esc_attr($view_url);
			$edit_url = su_esc_attr($edit_url);
			
			$actions = array(sprintf('<a href="%s">%s</a>', $view_url, __('View', 'seo-ultimate')));
			if ($edit_url)
				$actions[] = sprintf('<a href="%s">%s</a>', $edit_url, __('Edit', 'seo-ultimate'));
			$actions = implode(' | ', $actions);
			
			$cells = compact('actions', 'id', 'name');
			
			//Get meta field cells
			foreach ($fields as $field) {
				$inputid = "{$genus}_{$id}_{$field['name']}";
				
				switch ($genus) {
					case 'post':
						$value = $this->get_postmeta($field['name'], $id);
						break;
					case 'term':
						$value = $this->get_setting($field['term_settings_key'], array());
						$value = isset($value[$id]) ? $value[$id] : null;
						break;
				}
				
				if ($is_update && $field['type'] == 'checkbox' && $value == '1' && !isset($_POST[$inputid]))
					switch ($genus) {
						case 'post': delete_post_meta($id, "_su_{$field['name']}"); $value = 0; break;
						case 'term': $this->update_setting($field['term_settings_key'], false, null, $id); break;
					}
				
				$cells[$field['name']] = $this->get_input_element(
					  $field['type'] //Type
					, $inputid
					, $value
					, isset($field['options']) ? $field['options'] : false
				);
			}
			
			//Output all cells
			$this->table_row($cells, $id, $type);
		}
		
		//End table
		$this->admin_wftable_end();
		
		echo $page_links_text;
		
		echo "</div>\n";
		
		return true;
	}
	
	/**
	 * Returns the HTML for a given type of input element, without any surrounding <td> elements etc.
	 * 
	 * @since 2.9
	 * 
	 * @param string $type The type of input element (can be textbox, textarea, or checkbox)
	 * @param string $inputid The name/ID of the input element
	 * @param string $value The current value of the field
	 * @return string
	 */
	function get_input_element($type, $name, $value=null, $extra=false, $inputid=true) {
		if ($value === null) $value = $this->get_setting($name);
		
		if ($inputid === true) $inputid = $name;
		if (strlen($inputid)) $inputid = " id='".su_esc_attr($inputid)."'";
		
		//Get HTML element
		switch ($type) {
			case 'textbox':
				$value = su_esc_editable_html($value);
				$placeholder = $extra ? " placeholder='" . su_esc_attr($extra) . "'" : '';
				return "<input name='$name'$inputid value='$value'$placeholder type='text' class='textbox regular-text' />";
				break;
			case 'textarea':
				$value = su_esc_editable_html($value);
				return "<textarea name='$name'$inputid type='text' rows='3' cols='50' class='textarea regular-text'>$value</textarea>";
				break;
			case 'checkbox':
				$checked = $value ? " checked='checked'" : '';
				$html = "<input name='$name'$inputid value='1' type='checkbox' class='checkbox'$checked />";
				if (is_string($extra)) {
					$extra = su_esc_html($extra);
					$html = "<label>$html&nbsp;$extra</label>";
				}
				return $html;
				break;
			case 'dropdown':
				$html = "<select name='$name'$inputid onchange='javascript:su_toggle_select_children(this)' class='dropdown'>";
				if (is_array($extra)) $html .= suhtml::option_tags($extra, $value); else $html .= $extra;
				$html .= "</select>";
				return $html;
				break;
			case 'hidden':
				return "<input name='$name'$inputid value='$value' type='hidden' />";
				break;
		}
		
		return '';
	}
	
	/**
	 * Creates an admin form subsection.
	 * 
	 * @since 3.8
	 * @uses get_setting()
	 * @see get_input_element()
	 * 
	 * @param string $field
	 * @param string|null $current_value
	 * @param string $trigger_value
	 * @param string $html
	 * @return string
	 */
	function get_admin_form_subsection($field, $current_value, $trigger_value, $html) {
		if ($current_value === null) $current_value = $this->get_setting($field);
		$hidden = ($current_value == $trigger_value) ? '' : ' hidden';
		
		$field = su_esc_attr($field);
		$trigger_value = su_esc_attr($trigger_value);
		$html = "<div class='su_{$field}_{$trigger_value}_subsection$hidden'>$html</div>";
		return $html;
	}
	
	/**
	 * Creates multiple admin form subsections.
	 * 
	 * @since 3.8
	 * @uses get_admin_form_subsection()
	 * @see get_input_element()
	 * 
	 * @param string $field
	 * @param array $subsections Array of ($field => $trigger_value)
	 * @return string
	 */
	function get_admin_form_subsections($field, $current_value, $subsections) {
		$allhtml = '';
		if (!in_array($current_value, $subsection_keys = array_keys($subsections))) $current_value = $subsection_keys[0];
		foreach ($subsections as $trigger_value => $html)
			$allhtml .= $this->get_admin_form_subsection($field, $current_value, $trigger_value, $html);
		return $allhtml;
	}
	
	
	/********** ADMIN FORM FUNCTIONS **********/
	
	/**
	 * Begins an administration form.
	 * Outputs a subheader if provided, queues a success message upon settings update, outputs queued messages,
	 * opens a form tag, outputs a nonce field and other WordPress fields, and begins a form table.
	 * 
	 * @since 0.1
	 * @uses SEO_Ultimate::key_to_hook()
	 * @uses get_module_key()
	 * @uses admin_subheader()
	 * @uses is_action()
	 * @uses print_message()
	 * @uses get_parent_module()
	 * 
	 * @param mixed $header The text of the subheader that should go right before the form. Optional.
	 * @param boolean $table Whether or not to start a form table.
	 */
	function admin_form_start($header = false, $table = true, $form = true) {
		
		if ($header) $this->admin_subheader($header);
		
		if ($form) {
			$hook = $this->plugin->key_to_hook($this->get_module_or_parent_key());
			if ($this->is_action('update')) $this->print_message('success', __('Settings updated.', 'seo-ultimate'));
			echo "<form id='su-admin-form' method='post' action='?page=$hook'>\n";
			settings_fields($hook);
		}
		
		echo "\n";
		if ($table) echo "<table class='form-table'>\n";
	}
	
	/**
	 * Ends an administration form.
	 * Closes the table tag, outputs a "Save Changes" button, and closes the form tag.
	 * 
	 * @since 0.1
	 * @uses get_parent_module()
	 * 
	 * @param string|false $button The label of the submit button.
	 * @param boolean $table Whether or not a form table should be ended.
	 */
	function admin_form_end($button = null, $table = true) {
		
		if ($button === null) $button = __('Save Changes'); //This string is used in normal WP, so we don't need a textdomain
		if ($table) echo "</table>\n";
		
		if ($button !== false) {
?>
<p class="submit">
	<input type="submit" class="button-primary" value="<?php echo $button ?>" />
</p>
</form>
<?php
		}
	}
	
	/**
	 * Begins an admin form table.
	 * 
	 * @since 1.5
	 */
	function admin_form_table_start() {
		echo "<table class='form-table'>\n";
	}
	
	/**
	 * Ends an admin form table
	 * 
	 * @since 1.5
	 */
	function admin_form_table_end() {
		echo "</table>\n";
	}
	
	/**
	 * @since 5.8
	 */
	function child_admin_form_start() {
		if ($this->get_parent_module() && $this->plugin->module_exists($this->get_parent_module()))
			$this->admin_form_table_start();
		else
			$this->admin_form_start();
	}
	
	/**
	 * @since 5.8
	 */
	function child_admin_form_end() {
		if ($this->get_parent_module() && $this->plugin->module_exists($this->get_parent_module()))
			$this->admin_form_table_end();
		else
			$this->admin_form_end();
	}
	
	/**
	 * Begins a "widefat" WordPress table.
	 * 
	 * @since 1.8
	 * 
	 * @param $headers Array of (CSS class => Internationalized column title)
	 */
	function admin_wftable_start($headers = false) {
		echo "\n<table class='widefat' cellspacing='0'>\n";
		if ($headers)
			$this->table_column_headers($headers);
		else {
			echo "\t<thead><tr>\n";
			print_column_headers($this->plugin_page_hook);
			echo "\t</tr></thead>\n";
			echo "\t<tfoot><tr>\n";
			print_column_headers($this->plugin_page_hook);
			echo "\t</tr></tfoot>\n";
		}
		echo "\t<tbody>\n";
	}
	
	/**
	 * Outputs a <tr> of <th scope="col"></th> tags based on an array of column headers.
	 * 
	 * @since 2.1
	 * 
	 * @param $headers Array of (CSS class => Internationalized column title)
	 */
	function table_column_headers($headers) {
		echo "\t<thead><tr>\n";
		$mk = $this->get_module_key();
		foreach ($headers as $class => $header) {
			$class = is_numeric($class) ? '' : " class='su-$mk-$class'";
			echo "\t\t<th scope='col'$class>$header</th>\n";
		}
		echo "\t</tr></thead>\n";
	}
	
	/**
	 * Outputs <td> tags based on an array of cell data.
	 * 
	 * @since 2.1
	 * 
	 * @param $headers Array of (CSS class => Cell data)
	 */
	function table_cells($cells) {
		
		if (count($this->get_admin_table_columns())) {
			$columns = get_column_headers($this->plugin_page_hook);
			$hidden = get_hidden_columns($this->plugin_page_hook);
			foreach ( $columns as $column_name => $column_display_name ) {
				$class = "class=\"$column_name column-$column_name\"";
				$style = in_array($column_name, $hidden) ? ' style="display:none;"' : '';
				echo "\t\t<td $class$style>".$cells[$column_name]."</td>\n";
			}
		} elseif (is_array($cells) && count($cells)) {
			foreach ($cells as $class => $content) {
				$class = is_numeric($class) ? '' : " class='su-$class'";
				echo "\t\t<td$class>$content</td>\n";
			}
		}
	}
	
	/**
	 * Outputs a <tr> tag with <td> children.
	 * 
	 * @since 2.9
	 */
	function table_row($cells, $id, $class) {
		$mk = $this->get_module_key();
		echo "\t<tr id='su-$mk-$id' class='su-$mk-$class'>\n";
		$this->table_cells($cells);
		echo "\t</tr>\n";
	}
	
	/**
	 * Outputs a <tr> tag with <td> children, and consolidates adjacent, identical <td> elements with the rowspan attribute.
	 * 
	 * @since 2.9
	 */
	function table_rows_consolidated($rows, $cols_to_consolidate = 999) {
		$mk = $this->get_module_key();
		
		$rowspans = array();
		
		//Cycle through each row
		foreach ($rows as $rowid => $row) {
			
			echo "<tr>";
			
			//Cycle through the row's cells
			$cellid = 0;
			foreach ($row as $class => $cell) {
				
				//If a rowspan is already in process for this cell...
				if ($rowspans[$cellid] > 1)
					$rowspans[$cellid]--;
				else {
					
					//Find out if we should start a rowspan
					$rowspanhtml = '';
					if ($cellid < $cols_to_consolidate) {
						$rowspan = 1;
						for ($larowid = $rowid+1; $larowid < count($rows); $larowid++) {
							$lacell = $rows[$larowid][$class];
							if (strlen($lacell) && $cell == $lacell) $rowspan++; else break;
						}
						
						if ($rowspan > 1) {
							$rowspans[$cellid] = $rowspan;
							$rowspanhtml = " rowspan='$rowspan'";
						}
					}
					
					echo "<td class='su-$mk-$class'$rowspanhtml>$cell</td>";
				}
				
				$cellid++;
			}
			
			echo "</tr>";
		}
	}
	
	/**
	 * Ends a "widefat" WordPress table.
	 * 
	 * @since 1.8
	 */
	function admin_wftable_end() {
		echo "\t</tbody>\n</table>\n";
	}
	
	/**
	 * Outputs the HTML that begins an admin form group.
	 * 
	 * @since 1.5
	 * 
	 * @param string $title The title of the group.
	 * @param bool $newtable Whether to open a new <table> element.
	 */
	function admin_form_group_start($title, $newtable=true) {
		echo "<tr valign='top'>\n<th scope='row'>$title</th>\n<td><fieldset><legend class='hidden'>$title</legend>\n";
		if ($newtable) echo "<table>\n";
	}
	
	/**
	 * Outputs the HTML that ends an admin form group.
	 * 
	 * @since 1.5
	 * 
	 * @param bool $newtable Whether to close a <table> element.
	 */
	function admin_form_group_end($newtable=true) {
		if ($newtable) echo "</table>\n";
		echo "</td>\n</tr>\n";
	}
	
	function admin_form_indent_start() {
		echo "<tr valign='top'><td colspan='2'><table class='su-indent'>";
	}
	
	function admin_form_indent_end() {
		echo "</table></td></tr>";
	}
	
	/**
	 * Applies the necessary HTML so that certain content is displayed only when the mouse hovers over the including table row.
	 * 
	 * @since 0.1
	 * 
	 * @param string $text The always-visible text.
	 * @param string $hovertext The text that only displays upon row hover.
	 * @return string The HTML to put in a hover-supporting table row.
	 */
	function hover_row($text, $hovertext, $inline = false) {
		if ($inline)
			return "<span>$text</span>\n<span class='row-actions'> &mdash; $hovertext</span>";
		else
			return "<div>$text</div>\n<div class='row-actions'>$hovertext</div>";
	}
	
	/**
	 * Outputs a text block into an admin form.
	 * 
	 * @since 1.5
	 * 
	 * @param string $text
	 */
	function textblock($text) {
		echo "<tr valign='top' class='su-admin-form-textblock'>\n<td colspan='2'>\n";
		echo $text;
		echo "\n</td>\n</tr>\n";
	}
	
	/**
	 * Outputs a group of checkboxes into an admin form, and saves the values into the database after form submission.
	 * 
	 * @since 0.1
	 * @uses is_action()
	 * @uses update_setting()
	 * @uses get_module_key()
	 * @uses get_setting()
	 * 
	 * @param array $checkboxes An array of checkboxes. (Field/setting IDs are the keys, and descriptions are the values.)
	 * @param mixed $grouptext The text to display in a table cell to the left of the one containing the checkboxes. Optional.
	 */
	function checkboxes($checkboxes, $grouptext=false) {
		
		//Save checkbox settings after form submission
		if ($this->is_action('update')) {
			foreach ($checkboxes as $name => $desc) {
				$new_value = isset($_POST[$name]) ? ($_POST[$name] == '1') : false;
				$this->update_setting($name, $new_value);
				
				if (is_array($desc)) $desc = isset($desc['description']) ? $desc['description'] : '';
				
				if (strpos($desc, '%d') !== false) {
					$name .= '_value';
					$this->update_setting($name, intval($_POST[$name]));
				}
			}
		}
		
		if ($grouptext)
			$this->admin_form_group_start($grouptext, false);
		else
			echo "<tr valign='top' class='su-admin-form-checkbox'>\n<td colspan='2'>\n";
		
		if (is_array($checkboxes)) {
			foreach ($checkboxes as $name => $desc) {
				
				if (is_array($desc)) {
					$indent = isset($desc['indent']) ? $desc['indent'] : false;
					$desc = $desc['description'];
				} else {
					$indent = false;
				}
				
				register_setting($this->get_module_key(), $name, 'intval');
				$name = su_esc_attr($name);
				
				if (strpos($desc, '%d') === false) {
					$onclick = '';
				} else {
					$int_var_name = $name.'_value';
					$int_var_value = intval($this->get_setting($int_var_name));
					if ($this->get_setting($name) === true) $disabled = ''; else $disabled = "readonly='readonly' ";
					$desc = str_replace('%d', "</label><input name='$int_var_name' id='$int_var_name' type='text' value='$int_var_value' size='2' maxlength='3' $disabled/><label for='$name'>", $desc);
					$desc = str_replace("<label for='$name'></label>", '', $desc);
					$onclick = " onclick=\"javascript:document.getElementById('$int_var_name').readOnly=!this.checked;\"";
				}
				
				if ($indent) $labelclass = " class='su-indent'"; else $labelclass = '';
				echo "<label for='$name'$labelclass><input name='$name' id='$name' type='checkbox' value='1'";
				if ($this->get_setting($name) === true) echo " checked='checked'";
				echo "$onclick /> $desc</label><br />\n";
			}
		}
		
		if ($grouptext) echo "</fieldset>";
		echo "</td>\n</tr>\n";
	}
	
	/**
	 * Outputs a single checkbox into an admin form and saves its value into the database after form submission.
	 * 
	 * @since 1.5
	 * @uses checkboxes()
	 * 
	 * @param string $id The field/setting ID.
	 * @param string $desc The checkbox's label.
	 * @param mixed $grouptext The text to display in a table cell to the left of the one containing the checkbox. Optional.
	 * @return string The HTML that would render the checkbox.
	 */
	function checkbox($id, $desc, $grouptext = false) {
		$this->checkboxes(array($id => $desc), $grouptext);
	}
	
	/**
	 * Outputs a set of radio buttons into an admin form and saves the set's value into the database after form submission.
	 * 
	 * @since 1.5
	 * @uses is_action()
	 * @uses update_setting()
	 * @uses admin_form_group_start()
	 * @uses admin_form_group_end()
	 * @uses su_esc_attr()
	 * @uses get_setting()
	 * 
	 * @param string $name The name of the set of radio buttons.
	 * @param array $values The keys of this array are the radio button values, and the array values are the label strings.
	 * @param string|false $grouptext The text to display in a table cell to the left of the one containing the radio buttons. Optional.
	 */
	function radiobuttons($name, $values, $grouptext=false) {
		
		//Save radio button setting after form submission
		if ($this->is_action('update') && isset($_POST[$name]))
			$this->update_setting($name, $_POST[$name]);
		
		if ($grouptext)
			$this->admin_form_group_start($grouptext, false);
		else
			echo "<tr valign='top' class='su-admin-form-radio'>\n<td colspan='2'>\n";
		
		if (is_array($values)) {
			
			register_setting($this->get_module_key(), $name);
			$name = su_esc_attr($name);
			
			$first = true;
			foreach ($values as $value => $desc) {
				
				$value = su_esc_attr($value);
				$id = "{$name}_{$value}";
				
				$current = (strcmp($this->get_setting($name), $value) == 0);
				$class = $first ? 'first' : ''; $first = false;
				if ($current) $class .= ' current-setting';
				$class = trim($class);
				if ($class) $class = " class='$class'";
				
				extract($this->insert_subfield_textboxes($name, $desc));
				
				echo "<div><label for='$id'$class><input name='$name' id='$id' type='radio' value='$value'";
				if ($current) echo " checked='checked'";
				echo " /> $label";
				
				if (!sustr::has($label, '</label>')) echo '</label>';
				//if (!sustr::has($desc,  '</label>')) echo '<br />';
				echo "</div>\n";
			}
		}
		
		if ($grouptext) echo "</fieldset>";
		echo "</td>\n</tr>\n";
	}
	
	/**
	 * Outputs a single radio button into an admin form and saves the set's value into the database after form submission.
	 * 
	 * @since 3.0
	 * @uses radiobuttons()
	 * 
	 * @param string $name The name of the set of radio buttons.
	 * @param string $value The value of this radio button.
	 * @param string $label The label for this radio button.
	 */
	function radiobutton($name, $value, $label) {
		$this->radiobuttons($name, array($value => $label));
	}
	
	/**
	 * Outputs a dropdown into an admin form and saves the set's value into the database after form submission.
	 * 
	 * @since 3.7
	 * @uses is_action()
	 * @uses update_setting()
	 * @uses admin_form_group_start()
	 * @uses admin_form_group_end()
	 * @uses su_esc_attr()
	 * @uses get_setting()
	 * 
	 * @param string $name The name of the set of radio buttons.
	 * @param array $values The keys of this array are the radio button values, and the array values are the label strings.
	 * @param string|false $grouptext The text to display in a table cell to the left of the one containing the radio buttons. Optional.
	 */
	function dropdown($name, $values, $grouptext=false) {
		
		//Save dropdown setting after form submission
		if ($this->is_action('update') && isset($_POST[$name]))
			$this->update_setting($name, $_POST[$name]);
		
		if ($grouptext)
			$this->admin_form_group_start($grouptext, false);
		else
			echo "<tr valign='top' class='su-admin-form-dropdown'>\n<td colspan='2'>\n";
		
		if (is_array($values)) {
			
			register_setting($this->get_module_key(), $name);
			
			$name = su_esc_attr($name);
			echo "<select name='$name' id='$name'>\n";
			echo suhtml::option_tags($values, $this->get_setting($name));
			echo "</select>";
		}
		
		if ($grouptext) echo "</fieldset>";
		echo "</td>\n</tr>\n";
	}
	
	/**
	 * @since 3.0
	 */
	function insert_subfield_textboxes($name, $label, $enabled = true) {
		
		$pattern = '/%(d|s)({([a-z0-9_-]+)})?/';
		
		if (preg_match($pattern, $label, $matches)) {
			$is_int_field = ($matches[1] == 'd');
			$sfname = $matches[3];
			if (!$sfname) $sfname = $name.'_value';
			
			if ($this->is_action('update')) {
				$sfvalue = stripslashes($_POST[$sfname]);
				if ($is_int_field) $sfvalue = intval($sfvalue);
				$this->update_setting($sfname, $sfvalue);
			} else {
				$sfvalue = $this->get_setting($sfname);
				if ($is_int_field) $sfvalue = intval($sfvalue);
			}
			
			if ($enabled) $disabled = ''; else $disabled = " readonly='readonly'";
			
			$esfvalue = su_esc_attr($sfvalue);
			$field_html = "</label><input class='regular-text textbox subfield' name='$sfname' id='$sfname' type='text' value='$esfvalue'$disabled";
			if ($is_int_field) $field_html .= " size='2' maxlength='3'";
			$field_html .= " /><label for='$name'>";
			
			$label = preg_replace($pattern, $field_html, $label);
			$label = preg_replace("@<label for='$name'>$@", '', $label);
			
			$onclick = " onclick=\"javascript:document.getElementById('$sfname').readOnly=!this.checked;\"";
		} else
			$onclick = '';
		
		return compact('label', 'onclick');
	}
	
	/**
	 * Outputs a group of textboxes into an admin form, and saves the values into the database after form submission.
	 * Can also display a "Reset" link next to each textbox that reverts its value to a specified default.
	 * 
	 * @since 0.1
	 * @uses is_action()
	 * @uses update_setting()
	 * @uses get_module_key()
	 * @uses get_setting()
	 * 
	 * @param array $textboxes An array of textboxes. (Field/setting IDs are the keys, and descriptions are the values.)
	 * @param array $defaults An array of default textbox values that trigger "Reset" links. (The field/setting ID is the key, and the default value is the value.) Optional.
	 * @param mixed $grouptext The text to display in a table cell to the left of the one containing the textboxes. Optional.
	 */
	function textboxes($textboxes, $defaults=array(), $grouptext=false) {
		
		if ($this->is_action('update')) {
			foreach ($textboxes as $id => $title) {
				if (isset($_POST[$id]))
					$this->update_setting($id, stripslashes($_POST[$id]));
			}
		}
		
		if ($grouptext) $this->admin_form_group_start($grouptext, false);
		
		foreach ($textboxes as $id => $title) {
			register_setting($this->get_module_key(), $id);
			$value = su_esc_editable_html($this->get_setting($id));
			$id = su_esc_attr($id);
			$resetmessage = su_esc_attr(__('Are you sure you want to replace the textbox contents with this default value?', 'seo-ultimate'));
			
			if ($grouptext)
				echo "<div class='field'><label for='$id'>$title</label><br />\n";
			elseif (strpos($title, '</a>') === false)
				echo "<tr valign='top'>\n<th scope='row'><label for='$id'>$title</label></th>\n<td>";
			else
				echo "<tr valign='top'>\n<td>$title</td>\n<td>";
			
			echo "<input name='$id' id='$id' type='text' value='$value' class='regular-text' ";
			if (isset($defaults[$id])) {
				$default = su_esc_editable_html($defaults[$id]);
				echo "onkeyup=\"javascript:su_textbox_value_changed(this, '$default', '{$id}_reset')\" />";
				echo "&nbsp;<a href=\"#\" id=\"{$id}_reset\" onclick=\"javascript:su_reset_textbox('$id', '$default', '$resetmessage', this); return false;\"";
				if ($default == $value) echo ' class="hidden"';
				echo ">";
				_e('Reset', 'seo-ultimate');
				echo "</a>";
			} else {
				echo "/>";
			}
			
			if ($grouptext)
				echo "</div>\n";
			else
				echo "</td>\n</tr>\n";
		}
		
		if ($grouptext) $this->admin_form_group_end(false);
	}
	
	/**
	 * Outputs a single textbox into an admin form and saves its value into the database after form submission.
	 * 
	 * @since 0.1
	 * @uses textboxes()
	 * 
	 * @param string $id The field/setting ID.
	 * @param string $title The label of the HTML element.
	 * @param string|false $default The default textbox value. Setting this will trigger a "Reset" link. Optional.
	 * @return string The HTML that would render the textbox.
	 */
	function textbox($id, $title, $default=false, $grouptext=false) {
		if ($default === false) $default = array(); else $default = array($id => $default);
		$this->textboxes(array($id => $title), $default, $grouptext);
	}
	
	/**
	 * Outputs a group of textareas into an admin form, and saves the values into the database after form submission.
	 * 
	 * @since 0.1
	 * @uses is_action()
	 * @uses update_setting()
	 * @uses get_module_key()
	 * @uses get_setting()
	 * 
	 * @param array $textareas An array of textareas. (Field/setting IDs are the keys, and descriptions are the values.)
	 * @param int $rows The value of the textareas' rows attribute.
	 * @param int $cols The value of the textareas' cols attribute.
	 */
	function textareas($textareas, $rows = 5, $cols = 30) {
		
		if ($this->is_action('update')) {
			foreach ($textareas as $id => $title) {
				if (isset($_POST[$id]))
					$this->update_setting($id, stripslashes($_POST[$id]));
			}
		}
		
		foreach ($textareas as $id => $title) {
			register_setting($this->get_module_key(), $id);
			$value = su_esc_editable_html($this->get_setting($id));
			$id = su_esc_attr($id);
			
			echo "<tr valign='top'>\n";
			if ($title) echo "<th scope='row'><label for='$id'>$title</label></th>\n";
			echo "<td><textarea name='$id' id='$id' type='text' class='regular-text' cols='$cols' rows='$rows'>$value</textarea>";
			echo "</td>\n</tr>\n";
		}
	}
	
	/**
	 * Outputs a single textarea into an admin form and saves its value into the database after form submission.
	 * 
	 * @since 0.1
	 * @uses textareas()
	 * 
	 * @param string $id The field/setting ID.
	 * @param string $title The label of the HTML element.
	 * @param int $rows The value of the textarea's rows attribute.
	 * @param int $cols The value of the textarea's cols attribute.
	 * @return string The HTML that would render the textarea.
	 */
	function textarea($id, $title = '', $rows = 5, $cols = 30) {
		$this->textareas(array($id => $title), $rows, $cols);
	}
	
	/********** ADMIN SECURITY FUNCTIONS **********/
	
	/**
	 * Determines if a particular nonce-secured admin action is being executed.
	 * 
	 * @since 0.1
	 * @uses SEO_Ultimate::key_to_hook()
	 * @uses get_module_key()
	 * @uses nonce_validates()	 
	 * 
	 * @param string $action The name of the action to check.
	 * @return bool Whether or not the action is being executed.
	 */
	function is_action($action) {
		if (!isset($_GET['object']) || !($object = $_GET['object'])) $object = false;
		return (
					!empty($_GET['page'])
					&& (
						   ( strcasecmp($_GET['page'], $this->plugin->key_to_hook($this->get_module_key())) == 0 ) //Is $this module being shown?
						|| ( strlen($this->get_parent_module()) && strcasecmp($_GET['page'], $this->plugin->key_to_hook($this->get_parent_module())) == 0) //Is the parent module being shown?
					)
					&& (
						   (!empty($_GET['action']) && $_GET['action'] == $action)
						|| (!empty($_POST['action']) && $_POST['action'] == $action)
					) //Is this $action being executed?
					&& $this->nonce_validates($action, $object) //Is the nonce valid?
		);
	}
	
	/**
	 * Determines whether a nonce is valid.
	 * 
	 * @since 0.1
	 * @uses get_nonce_handle()
	 * 
	 * @param string $action The name of the action.
	 * @param mixed $id The ID of the object being acted upon. Optional.
	 * @return bool Whether or not the nonce is valid.
	 */
	function nonce_validates($action, $id = false) {
		return check_admin_referer($this->get_nonce_handle($action, $id));
	}
	
	/**
	 * Generates a unique name for a nonce.
	 * 
	 * @since 0.1
	 * @uses get_parent_module()
	 * @uses get_module_key()
	 * @uses SU_PLUGIN_NAME
	 * 
	 * @param string $action The name of the action.
	 * @param mixed $id The ID of the object being acted upon. Optional.
	 * @return The handle to use for the nonce.
	 */
	function get_nonce_handle($action, $id = false) {
		
		$key = $this->get_parent_module();
		if (!$key || !$this->plugin->module_exists($key)) $key = $this->get_module_key();
		
		$hook = $this->plugin->key_to_hook($key);
		
		if (strcmp($action, 'update') == 0) {
			//We use the settings_fields() function, which outputs a nonce in this particular format.
			return "$hook-options";
		} else {
			if ($id) $id = '-'.md5($id); else $id = '';
			$handle = SU_PLUGIN_NAME."-$hook-$action$id";
			return strtolower(str_replace(' ', '-', $handle));
		}
	}
	
	/**
	 * Returns a GET-action URL with an appended nonce.
	 * 
	 * @since 0.1
	 * @uses get_module_key()
	 * @uses get_nonce_handle()
	 * 
	 * @param string $action The name of the action.
	 * @param mixed $id The ID of the object being acted upon. Optional.
	 * @return The URL to use in an <a> tag.
	 */
	function get_nonce_url($action, $object=false) {
		$action = urlencode($action);
		if ($object) $objectqs = '&object='.urlencode($object); else $objectqs = '';
		
		$hook = $this->plugin->key_to_hook($this->get_module_or_parent_key());
		
		//We don't need to escape ampersands since wp_nonce_url will do that for us
		return wp_nonce_url("?page=$hook&action=$action$objectqs",
			$this->get_nonce_handle($action, $object));
	}
	
	
	/********** ADMIN MESSAGE FUNCTIONS **********/
	
	/**
	 * Print a message (and any previously-queued messages) right away.
	 * 
	 * @since 0.1
	 * @uses queue_message()
	 * @uses print_messages()
	 * 
	 * @param string $type The message's type. Valid values are success, error, warning, and info.
	 * @param string $message The message text.
	 */
	function print_message($type, $message) {
		$this->queue_message($type, $message);
		$this->print_messages();
	}
	
	/**
	 * Adds a message to the queue.
	 * 
	 * @since 0.1
	 * @uses $messages
	 * 
	 * @param string $type The message's type. Valid values are success, error, warning, and info.
	 * @param string $message The message text.
	 */
	function queue_message($type, $message) {
		$this->messages[$type][] = $message;
	}
	
	/**
	 * Prints all queued messages and flushes the queue.
	 * 
	 * @since 0.1
	 * @uses $messages
	 */
	function print_messages() {
		foreach ($this->messages as $type => $messages) {
			$messages = implode('<br />', $messages);
			if ($messages) {
				$type = su_esc_attr($type);
				echo "<div class='su-message'><p class='su-$type'>$messages</p></div>\n";
			}
		}
		
		$this->messages = array();
	}
	
	/**
	 * Prints a mini-style message.
	 * 
	 * @since 2.1
	 */
	function print_mini_message($type, $message) {
		$type = su_esc_attr($type);
		echo "<div class='su-status su-$type'>$message</div>";
	}
	
	/********** ADMIN META FUNCTIONS **********/
	
	/**
	 * Gets a specified meta value of the current post (i.e. the post currently being edited in the admin,
	 * the post being shown, the post now in the loop, or the post with specified ID).
	 * 
	 * @since 0.1
	 * 
	 * @param string $key The meta key to fetch.
	 * @param mixed $id The ID number of the post/page.
	 * @return string The meta value requested.
	 */
	function get_postmeta($key, $id=false) {
		
		if (!$id) {
			//This code is different from suwp::get_post_id();
			if (is_admin()) {
				$id = empty($_REQUEST['post']) ? false : intval($_REQUEST['post']);
				global $post;
			} elseif (in_the_loop()) {
				$id = intval(get_the_ID());
				global $post;
			} elseif (is_singular()) {
				global $wp_query;
				$id = $wp_query->get_queried_object_id();
				$post = $wp_query->get_queried_object();
			}
		}
		
		if ($id) {
			
			if (isset($post) && $post)
				$_post = $post;
			else
				$_post = get_post($id);
			
			$value = get_post_meta($id, "_su_$key", true);
			$value = apply_filters("su_get_postmeta", $value, $key, $_post);
			$value = apply_filters("su_get_postmeta-$key", $value, $key, $_post);
		} else
			$value = '';
		
		return $value;
	}
	
	/**
	 * Generates the HTML for multiple post meta textboxes.
	 * 
	 * @since 0.1
	 * @uses get_postmeta()
	 * 
	 * @param array $textboxes An array of textboxes. (Field/setting IDs are the keys, and descriptions are the values.)
	 * @return string The HTML that would render the textboxes.
	 */
	function get_postmeta_textboxes($textboxes) {

		$html = '';
		
		foreach ($textboxes as $id => $title) {
			
			register_setting('seo-ultimate', $id);
			$value = su_esc_editable_html($this->get_postmeta($id));
			$id = "_su_".su_esc_attr($id);
			//$title = str_replace(' ', '&nbsp;', $title);
			
			$html .= "<tr class='su textbox' valign='middle'>\n<th scope='row' class='su'><label for='$id'>$title</label></th>\n"
					."<td class='su'><input name='$id' id='$id' type='text' value='$value' class='regular-text' tabindex='2' /></td>\n</tr>\n";
		}
		
		return $html;
	}
	
	/**
	 * Generates the HTML for a single post meta textbox.
	 * 
	 * @since 0.1
	 * @uses get_postmeta_textboxes()
	 * 
	 * @param string $id The ID of the HTML element.
	 * @param string $title The label of the HTML element.
	 * @return string The HTML that would render the textbox.
	 */
	function get_postmeta_textbox($id, $title) {
		return $this->get_postmeta_textboxes(array($id => $title));
	}
	
	/**
	 * Generates the HTML for multiple post meta textareas.
	 * 
	 * @since 3.9
	 * @uses get_postmeta()
	 * 
	 * @param array $textareas An array of textareas. (Field/setting IDs are the keys, and descriptions are the values.)
	 * @return string The HTML that would render the textareas.
	 */
	function get_postmeta_textareas($textareas) {

		$html = '';
		
		foreach ($textareas as $id => $title) {
		
			register_setting('seo-ultimate', $id);
			$value = su_esc_editable_html($this->get_postmeta($id));
			$id = "_su_".su_esc_attr($id);
			
			$html .= "<tr class='su textarea' valign='top'>\n<th scope='row' class='su'><label for='$id'>$title</label></th>\n"
					."<td class='su'><textarea name='$id' id='$id' class='regular-text' tabindex='2' cols='60' rows='3'>$value</textarea></td>\n</tr>\n";
		}
		
		return $html;
	}
	
	/**
	 * Generates the HTML for a single post meta textarea.
	 * 
	 * @since 3.9
	 * @uses get_postmeta_textareas()
	 * 
	 * @param string $id The ID of the HTML element.
	 * @param string $title The label of the HTML element.
	 * @return string The HTML that would render the textarea.
	 */
	function get_postmeta_textarea($id, $title) {
		return $this->get_postmeta_textareas(array($id => $title));
	}
	
	/**
	 * Generates the HTML for a group of post meta checkboxes.
	 * 
	 * @since 0.1
	 * @uses get_module_key()
	 * @uses get_postmeta()
	 * 
	 * @param array $checkboxes An array of checkboxes. (Field/setting IDs are the keys, and descriptions are the values.)
	 * @param string $grouptext The text to display in a table cell to the left of the one containing the checkboxes.
	 */
	function get_postmeta_checkboxes($checkboxes, $grouptext) {
		
		$valign = (is_array($checkboxes) && count($checkboxes)) ? 'top' : 'middle';
		$html = "<tr class='su checkboxes' valign='$valign'>\n<th scope='row' class='su'>$grouptext</th>\n<td class='su'><fieldset><legend class='hidden'>$grouptext</legend>\n";
		
		if (is_array($checkboxes)) {
			foreach ($checkboxes as $name => $desc) {
				
				register_setting('seo-ultimate', $name);
				$checked = ($this->get_postmeta($name) == 1);
				$name = "_su_".su_esc_attr($name);
				
				$html .= "<label for='$name'><input name='$name' id='$name' type='checkbox' tabindex='2' value='1'";
				if ($checked) $html .= " checked='checked'";
				$html .= " /> $desc</label><br />\n";
			}
		}
		
		$html .= "</fieldset></td>\n</tr>\n";
		
		return $html;
	}
	
	/**
	 * Generates the HTML for a single post meta checkbox.
	 * 
	 * @since 0.1
	 * @uses get_postmeta_checkboxes()
	 * 
	 * @param string $id The ID of the HTML element.
	 * @param string $title The label of the HTML element.
	 * @param string $grouptext The text to display in a table cell to the left of the one containing the checkboxes.
	 * @return string The HTML that would render the textbox.
	 */
	function get_postmeta_checkbox($id, $title, $grouptext) {
		return $this->get_postmeta_checkboxes(array($id => $title), $grouptext);
	}
	
	/**
	 * Generates the HTML for a single <select> post meta dropdown.
	 * 
	 * @since 2.5
	 * @uses get_module_key()
	 * @uses get_postmeta()
	 * 
	 * @param string $name The name of the <select> element.
	 * @param array $options An array of options, where the array keys are the <option> values and the array values are the labels (<option> contents).
	 * @param string $grouptext The text to display in a table cell to the left of the one containing the dropdown.
	 * @return string $html
	 */
	function get_postmeta_dropdown($name, $options, $grouptext) {
		
		register_setting('seo-ultimate', $name);
		$current = $this->get_postmeta($name);
		if ($current === '') $current = array_shift(array_keys($options));
		$name = "_su_".su_esc_attr($name);
		
		$html = "<tr class='su dropdown' valign='middle'>\n<th scope='row' class='su'><label for='$name'>$grouptext</label></th>\n<td class='su'><fieldset><legend class='hidden'>$grouptext</legend>\n";
		$html .= "<select name='$name' id='$name' onchange='javascript:su_toggle_select_children(this)'>\n";
		$html .= suhtml::option_tags($options, $current);
		$html .= "</select>\n";
		$html .= "</fieldset></td>\n</tr>\n";
		
		return $html;
	}
	
	/**
	 * Turns a <tr> into a post meta subsection.
	 * 
	 * @since 2.5
	 * @uses get_postmeta
	 * 
	 * @param string $field
	 * @param string $value
	 * @param string $html
	 * @return string $html
	 */
	function get_postmeta_subsection($field, $value, $html) {
		$hidden = ($this->get_postmeta($field) == $value) ? '' : ' hidden';
		
		$field = su_esc_attr($field);
		$value = su_esc_attr($value);
		$html = str_replace('<tr ', "<tr class='su_{$field}_{$value}_subsection$hidden' ", $html);
		return $html;
	}
	
	/**
	 * Gets a specified meta value of the current term.
	 * 
	 * @since 5.4
	 * 
	 * @param string $key The database setting where the metadata is stored. The function will add a "taxonomy_" prefix.
	 * @param mixed $id The ID number of the post/page.
	 * @return string The meta value requested.
	 */
	function get_termmeta($key, $id=false, $module=false) {
		
		global $wp_query;
		
		if (!$id && (is_category() || is_tag() || is_tax()))
			$id = $wp_query->get_queried_object_id();
		
		if (!$id)
			return null;
		
		$tax_meta = $this->get_setting(sustr::startwith($key, 'taxonomy_'), array(), $module);
		
		if (is_array($tax_meta) && isset($tax_meta[$id]))
			return $tax_meta[$id];
		
		return null;
	}
	
	/********** CRON FUNCTION **********/
	
	/**
	 * Creates a cron job if it doesn't already exists, and ensures it runs at the scheduled time.
	 * Should be called in a module's init() function.
	 * 
	 * @since 0.1
	 * @uses get_module_key()
	 * 
	 * @param string $function The name of the module function that should be run.
	 * @param string $recurrance How often the job should be run. Valid values are hourly, twicedaily, and daily.
	 */
	function cron($function, $recurrance) {
		
		$mk = $this->get_module_key();
		
		$hook = "su-$mk-".str_replace('_', '-', $function);
		$start = time();
		
		if (wp_next_scheduled($hook) === false) {
			//This is a new cron job
			
			//Schedule the event
			wp_schedule_event($start, $recurrance, $hook);
			
			//Make a record of it
			$psdata = (array)get_option('seo_ultimate', array());
			$psdata['cron'][$mk][$function] = array($hook, $start, $recurrance);
			update_option('seo_ultimate', $psdata);
			
			//Run the event now
			call_user_func(array($this, $function));
		}
		
		add_action($hook, array(&$this, $function));
	}
	
	/********** JLSUGGEST **********/
	
	/**
	 * Initializes JLSuggest.
	 * Must be called in the admin_page_init() function of the module that wants to use JLSuggest.
	 * 
	 * @since 6.0
	 * @uses jlsuggest_xml_ns()
	 * @uses SEO_Ultimate::queue_js()
	 * @uses SEO_Ultimate::queue_css()
	 */
	function jlsuggest_init() {
		add_action('admin_xml_ns', array(&$this, 'jlsuggest_xml_ns'));
		$this->plugin->queue_js ('includes/jlsuggest', 'jlsuggest');
		$this->plugin->queue_css('includes/jlsuggest', 'jlsuggest');
	}
	
	/**
	 * Outputs the SEO Ultimate XMLNS used by JLSuggest.
	 * 
	 * @since 6.0
	 */
	function jlsuggest_xml_ns() {
		echo ' xmlns:su="http://johnlamansky.com/xmlns/seo-ultimate" ';
	}
	
	/**
	 * Explodes a JLSuggest database string into an array with the destination type and the destination ID.
	 * 
	 * @since 6.0
	 * 
	 * @param $valstr The database string, e.g. http://example.com or obj_posttype_post/1
	 * @return array
	 */
	function jlsuggest_value_explode($valstr) {
		
		if (is_array($valstr)) {
			
			if (count($valstr) == 3)
				return $valstr;
			
		} elseif (is_string($valstr)) {
			
			if (sustr::startswith($valstr, 'obj_')) {
				$valstr = sustr::ltrim_str($valstr, 'obj_');
				
				$valarr = explode('/', $valstr);
				if (count($valarr) == 2) {
					$valarr_type = explode('_', $valarr[0], 2);
					if (count($valarr_type) == 2)
						return array($valarr_type[0], $valarr_type[1], $valarr[1]);
					else
						return array($valarr[0], null, $valarr[1]);
				} else
					return array($valstr, null, null);
			} else {
				return array('url', null, $valstr);
			}
		}
		
		return array('url', null, '');
	}
	
	/**
	 * Returns the HTML code for a JLSuggest textbox
	 * 
	 * @since 6.0
	 * 
	 * @param string $name The value of the textbox's name/ID attributes
	 * @param string $value The current database string associated with this textbox
	 */
	function get_jlsuggest_box($name, $value, $params='') {
		
		list($to_genus, $to_type, $to_id) = $this->jlsuggest_value_explode($value);
		
		$text_dest = '';
		
		switch ($to_genus) {
			
			case 'posttype':
				$selected_post = get_post($to_id);
				$selected_post_type = get_post_type_object($selected_post->post_type);
				$text_dest = $selected_post->post_title . '<span class="type">&nbsp;&mdash;&nbsp;'.$selected_post_type->labels->singular_name.'</span>';
				break;
			case 'taxonomy':
				$selected_taxonomy = get_taxonomy($to_type);
				$selected_term = get_term($to_id, $selected_taxonomy->name);
				$text_dest = $selected_term->name . '<span class="type">&nbsp;&mdash;&nbsp;'.$selected_taxonomy->labels->singular_name.'</span>';
				break;
			case 'home':
				$text_dest = __('Blog Homepage', 'seo-ultimate');
				break;
			case 'author':
				$selected_author = get_userdata($to_id);
				$text_dest = $selected_author->user_login . '<span class="type">&nbsp;&mdash;&nbsp;'.__('Author', 'seo-ultimate').'</span>';
		}
		
		$is_url = (('url' == $to_genus) && !$text_dest);
		
		$to_genus_type = implode('_', array_filter(array($to_genus, $to_type)));
		$obj = 'obj_' . implode('/', array_filter(array($to_genus_type, $to_id)));
		
		//URL textbox
		//(hide if object is selected)
		$html = "<input name='$name' id='$name' value='";
		$html .= su_esc_editable_html($is_url ? $to_id : $obj);
		$html .= "'";
		
		if ($params) {
			$e_params = su_esc_attr($params);
			$html .= " su:params='$e_params'";
		}
		
		$html .= " type='text' class='textbox regular-text jlsuggest'";
		$html .= ' title="' . __('Type a URL or start typing the name of the item you want to link to', 'seo-ultimate') . '"';
		$html .= $is_url ? '' : ' style="display:none;" ';
		$html .= ' />';
		
		//Object box
		//(hide if URL is entered)
		$html .= '<div class="jls_text_dest"';
		$html .= $is_url ? ' style="display:none;" ' : '';
		$html .= '>';
		$html .= '<div class="jls_text_dest_text">';
		$html .= $text_dest;
		$html .= '</div>';
		$html .= '<div><a href="#" onclick="javascript:return false;" class="jls_text_dest_close" title="'.__('Remove this destination', 'seo-ultimate').'">'.__('X', 'seo-ultimate').'</a></div>';
		$html .= '</div>';
		
		return $html;
	}
	
	/**
	 * Converts a JLSuggest database string into a URL.
	 * 
	 * @since 6.0
	 * 
	 * @param string $value The JLSuggest database string to convert.
	 * @return string The URL of the referenced destination
	 */
	function jlsuggest_value_to_url($value) {
		
		list($to_genus, $to_type, $to_id) = $this->jlsuggest_value_explode($value);
		
		switch ($to_genus) {
			case 'url':
				return $to_id; break;
			case 'posttype':
				$to_id = (int)$to_id;
				$to_post = get_post($to_id);
				if (get_post_status($to_id) != 'publish') continue;
				return get_permalink($to_id); break;
			case 'taxonomy':
				$to_id = (int)$to_id;
				return get_term_link($to_id, $to_type); break;
			case 'home':
				return suwp::get_blog_home_url(); break;
			case 'author':
				$to_id = (int)$to_id;
				return get_author_posts_url($to_id); break;
		}
		
		return false;
	}
}
?>