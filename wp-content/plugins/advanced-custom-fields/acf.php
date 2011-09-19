<?php
/*
Plugin Name: Advanced Custom Fields
Plugin URI: http://plugins.elliotcondon.com/advanced-custom-fields/
Description: Customise your edit pages with an assortment of field types: Wysiwyg, Repeater, text, textarea, image, file, select, checkbox post type, page link and more! Hide unwanted metaboxes and assign to any edit page!
Version: 2.1.4
Author: Elliot Condon
Author URI: http://www.elliotcondon.com/
License: GPL
Copyright: Elliot Condon
*/

//ini_set('display_errors',1);
//error_reporting(E_ALL|E_STRICT);


include('core/admin/options_page.php');

$acf = new Acf();

include('core/api.php');


class Acf
{ 

	var $dir;
	var $path;
	var $siteurl;
	var $wpadminurl;
	var $version;
	var $fields;
	var $activated_fields;
	var $options_page;
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	Constructor
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function Acf()
	{
		
		// set class variables
		$this->path = dirname(__FILE__).'';
		$this->dir = plugins_url('',__FILE__);
		$this->siteurl = get_bloginfo('url');
		$this->wpadminurl = admin_url();
		$this->version = '2.1.4';
		$this->upgrade_version = '2.1.4'; // this is the latest version which requires an upgrade
		$this->activated_fields = $this->get_activated_fields();
		$this->options_page = new Acf_options_page($this);
		
		
		// set text domain
		load_plugin_textdomain('acf', false, $this->path.'/lang' );
		

		// populate post types
		$this->fields = $this->get_field_types();

		
		// add actions
		add_action('init', array($this, 'init'));
		add_action('init', array($this, 'import'));
		add_action('init', array($this, 'export'));
		add_action('init', array($this, 'third_party'));
		add_action('admin_head', array($this,'admin_head'));
		add_action('admin_menu', array($this,'admin_menu'));
		add_action('save_post', array($this, 'save_post'));
		add_action('delete_post', array($this, 'delete_post'), 10);
		add_action('admin_footer', array($this, 'admin_footer'));
		add_action('wp_ajax_input_meta_box_html', array($this, 'input_meta_box_html'));
		
		
		// admin styles + scripts
		add_action("admin_print_scripts", array($this, 'admin_print_scripts'));
	    add_action("admin_print_styles", array($this, 'admin_print_styles'));
	    
		
		return true;
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	Upgrade
	*
	*	@author Elliot Condon
	*	@since 2.0.6
	* 
	*-------------------------------------------------------------------------------------*/
	
	function upgrade()
	{
		include('core/upgrade.php');
	}
	
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	Init
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function init()
	{	
		include('core/actions/init.php');
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_print_scripts
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_print_scripts()
	{
		if(in_array($GLOBALS['pagenow'], array('post.php', 'post-new.php', 'edit.php')))
		{
			// jquery
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-core');
			
			
			// wysiwyg
			wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');
			wp_enqueue_script('word-count');
			wp_enqueue_script('post');
			wp_enqueue_script('editor');

			
			// repeater
			wp_enqueue_script('jquery-ui-sortable');
		}
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_print_styles
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_print_styles()
	{
		if(in_array($GLOBALS['pagenow'], array('post.php', 'post-new.php', 'edit.php')))
		{
			wp_enqueue_style('thickbox');
		}
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	save_post
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function save_post($post_id)
	{	
		
		// do not save if this is an auto save routine
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
		
		
		// verify this with nonce because save_post can be triggered at other times
		if(!isset($_POST['ei_noncename'])) return $post_id;
		if(!wp_verify_nonce($_POST['ei_noncename'], 'ei-n')) return $post_id;
		
		
		// only save once! WordPress save's twice for some strange reason.
		global $flag;
		if ($flag != 0) return $post_id;
		$flag = 1;
		

		// set post ID if is a revision
		if(wp_is_post_revision($post_id)) 
		{
			$post_id = wp_is_post_revision($post_id);
		}
		
		
		// include save files
		include('core/actions/fields_save.php');
		include('core/actions/location_save.php');
		include('core/actions/options_save.php');
		include('core/actions/input_save.php');
	}

	
	/*--------------------------------------------------------------------------------------
	*
	*	delete_post
	*
	*	@author Elliot Condon
	*	@since 2.1.4
	* 
	*-------------------------------------------------------------------------------------*/
	
	function delete_post($post_id)
	{
		//echo 'delete_posts';
		
		// global
		global $wpdb;
		
		// tables
		$acf_fields = $wpdb->prefix.'acf_fields';
		$acf_values = $wpdb->prefix.'acf_values';
		$acf_rules = $wpdb->prefix.'acf_rules';
		$wp_postmeta = $wpdb->prefix.'postmeta';
		
		if(get_post_type($post_id) == 'acf')
		{
			// delete fields
			$wpdb->query("DELETE FROM $acf_fields WHERE post_id = '$post_id'");
			
			// delete rules
			$wpdb->query("DELETE FROM $acf_rules WHERE acf_id = '$post_id'");
		}
		else
		{
			// delete values
			$wpdb->query("DELETE FROM $acf_values WHERE post_id = '$post_id'");
		}
		
		return true;
		
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_menu
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_menu() {
	
		// add sub menu
		add_options_page(__("Adv Custom Fields",'acf'), __("Adv Custom Fields",'acf'), 'manage_options', 'edit.php?post_type=acf');
		
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_head
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_head()
	{
		include('core/actions/admin_head.php');
	}

	
	/*--------------------------------------------------------------------------------------
	*
	*	get_field_types
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_field_types()
	{
		$array = array();
		
		include_once('core/fields/text.php');
		include_once('core/fields/textarea.php');
		include_once('core/fields/wysiwyg.php');
		include_once('core/fields/image.php');
		include_once('core/fields/file.php');
		include_once('core/fields/select.php');
		include_once('core/fields/checkbox.php');
		include_once('core/fields/radio.php');
		include_once('core/fields/true_false.php');
		include_once('core/fields/page_link.php');
		include_once('core/fields/post_object.php');
		include_once('core/fields/relationship.php');
		include_once('core/fields/date_picker/date_picker.php');
		include_once('core/fields/repeater.php');
		
		$array['text'] = new acf_Text($this); 
		$array['textarea'] = new acf_Textarea($this); 
		$array['wysiwyg'] = new acf_Wysiwyg(); 
		$array['image'] = new acf_Image($this); 
		$array['file'] = new acf_File($this); 
		$array['select'] = new acf_Select($this); 
		$array['checkbox'] = new acf_Checkbox();
		$array['radio'] = new acf_Radio();
		$array['true_false'] = new acf_True_false();
		$array['page_link'] = new acf_Page_link($this);
		$array['post_object'] = new acf_Post_object($this);
		$array['relationship'] = new acf_Relationship($this);
		$array['date_picker'] = new acf_Date_picker($this->dir);
		
		if(array_key_exists('repeater', $this->activated_fields))
		{
			$array['repeater'] = new acf_Repeater($this);
		}
		
		return $array;
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	create_field
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_field($field)
	{
		if(!is_object($this->fields[$field->type]))
		{
			_e('Error: Field Type does not exist!','acf');
			return false;
		}
		
		$this->fields[$field->type]->html($field);
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	save_field
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function save_field($options)
	{
		if(!$this->fields[$options['field_type']])
		{
			_e('Error: Field Type does not exist!','acf');
			return false;
		}
		
		$this->fields[$options['field_type']]->save_field($options['post_id'], $options['field_name'], $options['field_value']);
	}
	

	/*--------------------------------------------------------------------------------------
	*
	*	_fields_meta_box
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function _fields_meta_box()
	{
		include('core/admin/fields_meta_box.php');
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	_location_meta_box
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function _location_meta_box()
	{
		include('core/admin/location_meta_box.php');
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	_options_meta_box
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function _options_meta_box()
	{
		include('core/admin/options_meta_box.php');
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	_input_meta_box
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function input_meta_box($post, $args)
	{
		include('core/admin/input_meta_box.php');
	}
	

	
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_fields
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/

	function get_fields($acf_id)
	{
	 
	 	// set table name
		global $wpdb;
		$table_name = $wpdb->prefix.'acf_fields';
	 	
	 	
	 	// get fields
	 	$parent_id = 0;
	 	$fields = $wpdb->get_results("SELECT * FROM $table_name WHERE post_id = '$acf_id' AND parent_id = $parent_id ORDER BY order_no,name");
	 	
	 	
	 	// if fields are empty, this must be a new or broken acf. add blank field
	 	if(!$fields)
	 	{
	 		return array();
	 	}
	 	

		// loop through fields
	 	foreach($fields as $field)
	 	{
	 		
			// unserialize options
			if(@unserialize($field->options))
			{
				$field->options = unserialize($field->options);
			}
			else
			{
				$field->options = array();
			}

	 		
	 		// sub fields
	 		if($field->type == 'repeater')
	 		{
	 			$sub_fields = $wpdb->get_results("SELECT * FROM $table_name WHERE parent_id = '$field->id' ORDER BY order_no,name");

	 			
	 			// if fields are empty, this must be a new or broken acf. 
			 	if(empty($sub_fields))
			 	{
			 		$field->options['sub_fields'] = array();
			 	}
			 	else
			 	{
			 		// loop through fields
				 	foreach($sub_fields as $sub_field)
				 	{
				 		// unserialize options
				 		if(@unserialize($sub_field->options))
						{
							$sub_field->options = @unserialize($sub_field->options);
						}
						else
						{
							$sub_field->options = array();
						}

					}
					
					
					// assign array to the field options array
					$field->options['sub_fields'] = $sub_fields;
			 	}
			 			 	
	 		}
	 		// end if sub field
	 	}
	 	// end foreach $fields
	 	
	 	
	 	// return fields
		return $fields;
		
	}
	 
	 
	/*--------------------------------------------------------------------------------------
	*
	*	get_field_options
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_field_options($type, $options)
	{
	 	$field_options = $this->fields[$type]->options();
	 	
	 	?>
	 	<table class="field_options">
	 		<?php foreach($field_options as $field_option): ?>
			<tr>
				<td class="label">
					<label for="post_type"><?php echo $field_options[0]['label'] ?></label>
				</td>
				<td>
					<?php $acf->create_field('text',$options); ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</table>
	 	<?php
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_acf_location
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_acf_location($acf_id)
	{

	 	// set table name
		global $wpdb;
		$table_name = $wpdb->prefix.'acf_rules';
	 	$location = new stdClass();
	 	
	 	
	 	// get fields and add them to $options
	 	$location->rules = $wpdb->get_results("SELECT * FROM $table_name WHERE acf_id = '$acf_id' ORDER BY order_no ASC");
	 	$location->allorany = get_post_meta($acf_id, 'allorany', true) ? get_post_meta($acf_id, 'allorany', true) : 'all'; 
	 	
	 		 	
	 	// return location
	 	return $location;
	 	
	}

	
	/*--------------------------------------------------------------------------------------
	*
	*	get_acf_options
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_acf_options($acf_id)
	{
	
	 	$options = new stdClass();
	 	
	 
	 	// If this is a new acf, there will be no custom keys!
	 	if(!get_post_custom_keys($acf_id))
	 	{
	 		$options->show_on_page = array('the_content', 'discussion', 'custom_fields', 'comments', 'slug', 'author');
	 	}
	 	else
	 	{
	 		if(@unserialize(get_post_meta($acf_id, 'show_on_page', true)))
	 		{
	 			$options->show_on_page = unserialize(get_post_meta($acf_id, 'show_on_page', true));
	 		}
	 		else
	 		{
	 			$options->show_on_page = array();
	 		}
	 		
	 		if(get_post_meta($acf_id, 'field_group_layout', true))
	 		{
	 			$options->field_group_layout = get_post_meta($acf_id, 'field_group_layout', true);
	 		}
	 		else
	 		{
	 			$options->field_group_layout = "no_box";
	 		}
	 				
	 	}
	 	
	 	return $options;

	}

	 
	/*--------------------------------------------------------------------------------------
	*
	*	admin_footer
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_footer()
	{
	
		if($GLOBALS['pagenow'] == 'edit.php' && $GLOBALS['post_type'] == 'acf')
		{
			echo '<link rel="stylesheet" type="text/css" href="'.$this->dir.'/css/style.screen_extra.css" />';
			echo '<script type="text/javascript" src="'.$this->dir.'/js/functions.screen_extra.js" ></script>';
			include('core/screen_extra.php');
		}
		
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	field_method_exists
	*
	*	@author Elliot Condon
	*	@since 2.0.6
	* 
	*-------------------------------------------------------------------------------------*/
	
	function field_method_exists($field_type, $method)
	{
		if(method_exists($this->fields[$field_type], $method))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/*--------------------------------------------------------------------------------------
	*
	*	load_value_for_input
	*
	*	@author Elliot Condon
	*	@since 1.0.6
	* 
	*-------------------------------------------------------------------------------------*/

	function load_value_for_input($post_id, $field)
	{
		
		$value;

		
		if($this->field_method_exists($field->type, 'load_value_for_input'))
		{
			$value = $this->fields[$field->type]->load_value_for_input($post_id, $field);
		}
		else
		{
			// tables
			global $wpdb;
			$acf_values = $wpdb->prefix.'acf_values';
			$wp_postmeta = $wpdb->prefix.'postmeta';
		 	
		 	
		 	// get row
		 	$value = $wpdb->get_row("SELECT m.meta_value as value, m.meta_id, v.id as value_id FROM $wp_postmeta m LEFT JOIN $acf_values v ON m.meta_id = v.value WHERE v.field_id = '$field->id' AND m.post_id = '$post_id'");
		 	//$value = $wpdb->get_var("SELECT value FROM $table_name WHERE field_id = '$field->id' AND post_id = '$post_id'");
			
			if($value)
			{
				// format if needed
				if($this->field_method_exists($field->type, 'format_value_for_input'))
				{
					$value->value = $this->fields[$field->type]->format_value_for_input($value->value);
				}
			}
			else
			{
				$value = new stdClass();
		 		$value->value = false;
		 		
		 		
		 		// override with default value
				if($post_id != 0)
				{
					$post_meta = get_post_custom($post_id);
					if(empty($post_meta) && isset($field->default_value))
					{
						$value->value = $field->default_value;
					}
		
				}
				
				
			}
		}
		
		
		// return value
		return $value;
	}
	

	
	/*--------------------------------------------------------------------------------------
	*
	*	load_value_for_api
	*
	*	@author Elliot Condon
	*	@since 1.0.6
	* 
	*-------------------------------------------------------------------------------------*/

	function load_value_for_api($post_id, $field)
	{
		
		if($this->field_method_exists($field->type, 'load_value_for_api'))
		{
			$value = $this->fields[$field->type]->load_value_for_api($post_id, $field);
		}
		else
		{
			// tables
			global $wpdb;
			$acf_values = $wpdb->prefix.'acf_values';
			$wp_postmeta = $wpdb->prefix.'postmeta';
		 	
		 	
		 	// get var
		 	$value = $wpdb->get_var("SELECT m.meta_value FROM $wp_postmeta m LEFT JOIN $acf_values v ON m.meta_id = v.value WHERE v.field_id = '$field->id' AND m.post_id = '$post_id'");
		 	
		 			 	
		 	// format if needed
		 	if($this->field_method_exists($field->type, 'format_value_for_api'))
			{
				$value = $this->fields[$field->type]->format_value_for_api($value, $field->options);
			}
		}
		
		
		if(empty($value) || $value == null || $value == "")
		{
			$value = false;
		}
		
		
		// return value
		return $value;
	}
	 
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_activated_fields
	*
	*	@author Elliot Condon
	*	@since 2.0.0
	* 
	*-------------------------------------------------------------------------------------*/

	function get_activated_fields()
	{
		$activated = array();
		
		// repeater
		if(get_option("acf_repeater_ac"))
		{
			$md5 = md5(get_option("acf_repeater_ac"));
			if($md5 == "bbefed143f1ec106ff3a11437bd73432")
			{
				$activated['repeater'] = get_option("acf_repeater_ac");
			}
		}
		
		
		// options
		if(get_option("acf_options_page_ac"))
		{
			$md5 = md5(get_option("acf_options_page_ac"));
			if($md5 == "1fc8b993548891dc2b9a63ac057935d8")
			{
				$activated['options_page'] = get_option("acf_options_page_ac");
			}
		}
		
		return $activated;
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	match_location_rule
	*
	*	@author Elliot Condon
	*	@since 2.0.0
	* 
	*-------------------------------------------------------------------------------------*/

	function match_location_rule($post, $rule, $overrides = array())
	{
		
		switch ($rule->param) {
		
			// POST TYPE
		    case "post_type":
		    
		    	$post_type = isset($overrides['post_type']) ? $overrides['post_type'] : get_post_type($post);
		        
		        if($rule->operator == "==")
		        {
		        	if($post_type == $rule->value)
		        	{
		        		return true; 
		        	}
		        	
		        	return false;
		        }
		        elseif($rule->operator == "!=")
		        {
		        	if($post_type != $rule->value)
		        	{
		        		return true; 
		        	}
		        	
		        	return false;
		        }
		        
		        break;
		        
		    // PAGE
		    case "page":
		        
		        $page = isset($overrides['page']) ? $overrides['page'] : $post->ID;
		        
		        if($rule->operator == "==")
		        {
		        	if($page == $rule->value)
		        	{
		        		return true; 
		        	}
		        	
		        	return false;
		        }
		        elseif($rule->operator == "!=")
		        {
		        	if($page != $rule->value)
		        	{
		        		return true; 
		        	}
		        	
		        	return false;
		        }
		        
		        break;
		        
			// PAGE
		    case "page_type":
		        
		        $page_type = isset($overrides['page_type']) ? $overrides['page_type'] : $post->post_parent;
		        
		        if($rule->operator == "==")
		        {
		        	if($rule->value == "parent" && $page_type == "0")
		        	{
		        		return true; 
		        	}
		        	
		        	if($rule->value == "child" && $page_type != "0")
		        	{
		        		return true; 
		        	}
		        	
		        	return false;
		        }
		        elseif($rule->operator == "!=")
		        {
		        	if($rule->value == "parent" && $page_type != "0")
		        	{
		        		return true; 
		        	}
		        	
		        	if($rule->value == "child" && $page_type == "0")
		        	{
		        		return true; 
		        	}
		        	
		        	return false;
		        }
		        
		        break;
		        
		    // PAGE PARENT
		    case "page_parent":
		        
		        $page_parent = isset($overrides['page_parent']) ? $overrides['page_parent'] : $post->post_parent;
		        
		        if($rule->operator == "==")
		        {
		        	if($page_parent == $rule->value)
		        	{
		        		return true; 
		        	}
		        	
		        	return false;
		        	
		        }
		        elseif($rule->operator == "!=")
		        {
		        	if($page_parent != $rule->value)
		        	{
		        		return true; 
		        	}
		        	
		        	return false;
		        }
		        
		        break;
		    
		    // PAGE
		    case "page_template":
		        
		        $page_template = isset($overrides['page_template']) ? $overrides['page_template'] : get_post_meta($post->ID,'_wp_page_template',true);
		        
		        if($rule->operator == "==")
		        {
		        	if($page_template == $rule->value)
		        	{
		        		return true; 
		        	}
		        	
		        	if($rule->value == "default" && !$page_template)
		        	{
		        		return true;
		        	}
		        	
		        	return false;
		        }
		        elseif($rule->operator == "!=")
		        {
		        	if($page_template != $rule->value)
		        	{
		        		return true; 
		        	}
		        	
		        	return false;
		        }
		        
		        break;
		       
		    // POST
		    case "post":
		        
		        $post_id = isset($overrides['post']) ? $overrides['post'] : $post->ID;
		        
		        if($rule->operator == "==")
		        {
		        	if($post_id == $rule->value)
		        	{
		        		return true; 
		        	}
		        	
		        	return false;
		        }
		        elseif($rule->operator == "!=")
		        {
		        	if($post_id != $rule->value)
		        	{
		        		return true; 
		        	}
		        	
		        	return false;
		        }
		        
		        break;
		        
		    // POST CATEGORY
		    case "post_category":
		        
		        $cats = array();
		        
		        if(isset($overrides['post_category']))
		        {
		        	$cats = $overrides['post_category'];
		        }
		        else
		        {
		        	$all_cats = get_the_category($post->ID);
		        	foreach($all_cats as $cat)
					{
						$cats[] = $cat->term_id;
					}
		        }
		        
		        if($rule->operator == "==")
		        {
		        	if($cats)
					{
						if(in_array($rule->value, $cats))
						{
							return true; 
						}
					}
		        	
		        	return false;
		        }
		        elseif($rule->operator == "!=")
		        {
		        	if($cats)
					{
						if(!in_array($rule->value, $cats))
						{
							return true; 
						}
					}
		        	
		        	return false;
		        }
		        
		        break;
			
			// PAGE PARENT
			/*
		    case "post_format":
		        
		        $post_format = isset($overrides['post_format']) ? $overrides['post_format'] : get_post_format(); 
		        
		        if($rule->operator == "==")
		        {
		        	if($post_format == $rule->value)
		        	{
		        		return true; 
		        	}
		        	
		        	return false;
		        	
		        }
		        elseif($post_format == "!=")
		        {
		        	if($post->post_parent != $rule->value)
		        	{
		        		return true; 
		        	}
		        	
		        	return false;
		        }
		        
		        break;
			*/
			
			// USER TYPE
		    case "user_type":
		        		
		        if($rule->operator == "==")
		        {
		        	if(current_user_can($rule->value))
		        	{
		        		return true;
		        	}
		        	
		        	return false;
		        }
		        elseif($rule->operator == "!=")
		        {
		        	if(!current_user_can($rule->value))
		        	{
		        		return true;
		        	}
		        	
		        	return false;
		        }
		        
		        break;
		    
		    // Options Page
		    case "options_page":
		        
		
		        if($rule->operator == "==")
		        {
		        	if(get_admin_page_title() == $rule->value)
		        	{
		        		return true;
		        	}
		        	
		        	return false;
		        }
		        elseif($rule->operator == "!=")
		        {
		        	if(get_admin_page_title() != $rule->value)
		        	{
		        		return true;
		        	}
		        	
		        	return false;
		        }
		        
		        break;
		    
		    
		    // Post Format
		    case "post_format":
		        
		       
		        $post_format = isset($overrides['post_format']) ? has_post_format($overrides['post_format'],$post->ID) : has_post_format($rule->value,$post->ID); 
		        
		        if($rule->operator == "==")
		        {
		        	if($post_format)
		        	{
		        		return true;
		        	}
		        	
		        	return false;
		        }
		        elseif($rule->operator == "!=")
		        {
		        	if(!$post_format)
		        	{
		        		return true;
		        	}
		        	
		        	return false;
		        }
		        
		        break;
		    
		
		}
		
	}

	
	
	/*--------------------------------------------------------------------------------------
	*
	*	export
	*
	*	@author Elliot Condon
	*	@since 2.0.5
	* 
	*-------------------------------------------------------------------------------------*/
	
	function export()
	{
		if(!isset($_POST['acf_export']))
		{
			return;
		}
		
		
		// get the acfs to save
		$acfs =  isset($_POST['acf_objects']) ? $_POST['acf_objects'] : null;
		
		
		// quick function for writing an array
		function echo_value_xml($value)
		{
			if(!is_array($value))
			{
				echo $value;
			}
			else
			{
				echo '<array>';
				foreach($value as $k => $v)
				{
					echo '<piece key="'.$k.'">'.$v.'</piece>';
				}
				echo '</array>';
			}
		}
		
		// save as file
		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=advanced-custom-fields.xml' );
		header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true );
		
		
		
		// display document in browser as plain text
		//header("Content-Type: text/plain");
		echo '<?xml version="1.0"?> ';
?>

<?php if($acfs): ?>
<posts>
<?php 
	foreach($acfs as $acf): 
	$post = get_post($acf); 
	$fields = $this->get_fields($post->ID);
	$location = $this->get_acf_location($post->ID);
	$options = $this->get_acf_options($post->ID);	
?>
	<post>
		<title><?php echo apply_filters( 'the_title_rss', $post->post_title ); ?></title>
		<post_status><?php echo $post->post_status; ?></post_status>
		<post_parent><?php echo $post->post_parent; ?></post_parent>
		<menu_order><?php echo $post->menu_order; ?></menu_order>
		<fields>
<?php		if($fields):
			foreach($fields as $field): ?>
			<field>
				<label><?php echo $field->label; ?></label>
				<name><?php echo $field->name; ?></name>
				<type><?php echo $field->type; ?></type>
				<default_value><?php echo $field->default_value; ?></default_value>
				<options>
<?php				if($field->options):
					foreach($field->options as $k => $option):
					if($k == 'sub_fields'): ?>
					<<?php echo $k; ?>>
<?php					foreach($field->options['sub_fields'] as $sub_field): ?>
						<field>
							<label><?php echo $sub_field->label; ?></label>
							<name><?php echo $sub_field->name; ?></name>
							<type><?php echo $sub_field->type; ?></type>
							<default_value><?php echo $sub_field->default_value; ?></default_value>
							<options>
<?php							if($sub_field->options):
								foreach($sub_field->options as $k2 => $option2): ?>
								<<?php echo $k2; ?>><?php echo_value_xml($option2); ?></<?php echo $k2; ?>>
<?php							endforeach;
								endif; ?>
							</options>
						</field>
<?php 					endforeach; ?>
					</<?php echo $k; ?>>
<?php				else: ?>
					<<?php echo $k; ?>><?php echo_value_xml($option); ?></<?php echo $k; ?>>
<?php				endif;
					endforeach;
					endif; ?>
				</options>
				<instructions><?php echo $field->instructions ?></instructions>
			</field>
<?php 		endforeach;
			endif; ?>
		</fields>
		<location>
<?php		if($location->rules):
			foreach($location->rules as $k => $rule): ?>
			<rule>
				<param><?php echo $rule->param; ?></param>
				<operator><?php echo $rule->operator; ?></operator>
				<value><?php echo $rule->value; ?></value>
			</rule>
<?php		endforeach;
			endif; ?>
			<allorany><?php echo $location->allorany; ?></allorany>
		</location>
		<options>
			<show_on_page><?php echo_value_xml($options->show_on_page); ?></show_on_page>
			<field_group_layout><?php echo $options->field_group_layout; ?></field_group_layout>
		</options>
	</post>
<?php endforeach; ?>
</posts>
<?php 	
		endif;
				
		die;
	}
	
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	import
	*
	*	@author Elliot Condon
	*	@since 2.0.5
	* 
	*-------------------------------------------------------------------------------------*/
	
	function import()
	{
		// Checkpoint: Did someone submit the form
		if(isset($_POST['acf_import']))
		{
			include('core/import.php');
		}
	}
	

	/*--------------------------------------------------------------------------------------
	*
	*	admin_error
	*
	*	@author Elliot Condon
	*	@since 2.0.5
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_error($message = "")
	{
		global $acf_mesage;
		$acf_mesage = $message;

		function my_admin_notice()
		{
			global $acf_mesage;
		    echo '<div class="error" id="message"><p>'.$acf_mesage.'</p></div>';
		}
		add_action('admin_notices', 'my_admin_notice');
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_message
	*
	*	@author Elliot Condon
	*	@since 2.0.5
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_message($message = "")
	{
		global $acf_mesage;
		$acf_mesage = $message;
		
		function my_admin_notice()
		{
			global $acf_mesage;
		    echo '<div class="updated" id="message"><p>'.$acf_mesage.'</p></div>';
		}
		add_action('admin_notices', 'my_admin_notice');
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	input_meta_box_html
	*
	*	@author Elliot Condon
	*	@since 2.0.6
	* 
	*-------------------------------------------------------------------------------------*/
	function input_meta_box_html($ajax = true)
	{
			
		$overrides = array();
		if(isset($_POST['page_template']) && $_POST['page_template'] != 'false') $overrides['page_template'] = $_POST['page_template'];
		if(isset($_POST['page_parent']) && $_POST['page_parent'] != 'false') $overrides['page_parent'] = $_POST['page_parent'];
		if(isset($_POST['page_type']) && $_POST['page_type'] != 'false') $overrides['page_type'] = $_POST['page_type'];
		if(isset($_POST['page']) && $_POST['page'] != 'false') $overrides['page'] = $_POST['page'];
		if(isset($_POST['post']) && $_POST['post'] != 'false') $overrides['post'] = $_POST['post'];
		if(isset($_POST['post_category']) && $_POST['post_category'] != 'false') $overrides['post_category'] = $_POST['post_category'];
		if(isset($_POST['post_format']) && $_POST['post_format'] != 'false') $overrides['post_format'] = $_POST['post_format'];
		
		$this->input_meta_box_html_no_ajax($_POST['post_id'], $overrides);
		
		die;
		
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	input_meta_box_html_no_ajax
	*
	*	@author Elliot Condon
	*	@since 2.0.6
	* 
	*-------------------------------------------------------------------------------------*/
	
	function input_meta_box_html_no_ajax($post_id, $overrides = array())
	{
		// create post object to match against
		$post = get_post($post_id);
		
		
		//var_dump($overrides);
		$acfs = get_pages(array(
			'numberposts' 	=> 	-1,
			'post_type'		=>	'acf',
			'sort_column' 	=>	'menu_order',
		));
		
		
		// blank array to hold acfs
		$add_acf = array();
		
		if($acfs)
		{
			foreach($acfs as $acf)
			{
				$add_box = false;
				$location = $this->get_acf_location($acf->ID);

				
				if($location->allorany == 'all')
				{
					// ALL
					
					$add_box = true;
					
					if($location->rules)
					{
						foreach($location->rules as $rule)
						{
							// if any rules dont return true, dont add this acf
							if(!$this->match_location_rule($post, $rule, $overrides))
							{
								$add_box = false;
							}
						}
					}
					
				}
				elseif($location->allorany == 'any')
				{
					// ANY
					
					$add_box = false;
					
					if($location->rules)
					{
						foreach($location->rules as $rule)
						{
							// if any rules return true, add this acf
							if($this->match_location_rule($post, $rule, $overrides))
							{
								$add_box = true;
							}
						}
					}
				}
							
				if($add_box == true)
				{
					$add_acf[] = $acf;
				}
				
			}// end foreach
			
			if(!empty($add_acf))
			{

				$adv_options = $this->get_acf_options($add_acf[0]->ID);
				
				
				$fields = array();
				foreach($add_acf as $acf)
				{
					// get this acf's fields and add them to the global $fields
					$this_fields = $this->get_fields($acf->ID);
					foreach($this_fields as $this_field)
					{
						$fields[] = $this_field;
					}
				
				}
				
			?>
				
			
			<style type="text/css" id="acf_dynamic_style">
				<?php if(!in_array('the_content',$adv_options->show_on_page)): ?>
					#postdivrich {display: none;}
				<?php endif; ?>
				
				<?php if(!in_array('custom_fields',$adv_options->show_on_page)): ?>
					#postcustom,
					#screen-meta label[for=postcustom-hide] {display: none;}
				<?php endif; ?>
				
				<?php if(!in_array('discussion',$adv_options->show_on_page)): ?>
					#commentstatusdiv,
					#screen-meta label[for=commentstatusdiv-hide] {display: none;}
				<?php endif; ?>
				
				<?php if(!in_array('comments',$adv_options->show_on_page)): ?>
					#commentsdiv,
					#screen-meta label[for=commentsdiv-hide] {display: none;}
				<?php endif; ?>
				
				<?php if(!in_array('slug',$adv_options->show_on_page)): ?>
					#slugdiv,
					#screen-meta label[for=slugdiv-hide] {display: none;}
				<?php endif; ?>
				
				<?php if(!in_array('author',$adv_options->show_on_page)): ?>
					#authordiv,
					#screen-meta label[for=authordiv-hide] {display: none;}
				<?php endif; ?>
				
				#screen-meta label[for=acf_input-hide] {display: none;}
			</style>
			
			

			
				<?php 

				foreach($add_acf as $acf)
				{
				
					// load acf data
					$options = $this->get_acf_options($acf->ID);
					$fields = $this->get_fields($acf->ID);
					$html = '';
					
					
					if($options->field_group_layout == "in_box")
					{
						echo '<div class="acf_ajax_fields postbox" data-acf_id="'.$acf->ID.'"><h3><span>'.$acf->post_title.'</span></h3><div class="inside">';
					}
					else
					{
						echo '<div class="acf_ajax_fields" data-acf_id="'.$acf->ID.'">';
					}
			
			
					foreach($fields as $field)
					{
					
						// if they didn't select a type, skip this field
						if($field->type == 'null')
						{
							continue;
						}
						
						
						// set value, id and name for field
						$field->value = $this->load_value_for_input($post->ID, $field);
						$field->input_name = isset($field->input_name) ? $field->input_name : '';
						
						$temp_field = new stdClass();
						
						
						echo '<div class="field">';
						
							echo '<input type="hidden" name="acf['.$field->id.'][field_id]" value="'.$field->id.'" />';
							echo '<input type="hidden" name="acf['.$field->id.'][field_type]" value="'.$field->type.'" />';
							echo '<input type="hidden" name="acf['.$field->id.'][field_name]" value="'.$field->name.'" />';	
							
							if($field->type != 'repeater')
							{
								$value_id = isset($field->value->value_id) ? $field->value->value_id : '';
								$meta_id = isset($field->value->meta_id) ? $field->value->meta_id : '';
								$temp_field->value = $field->value->value;
								
								echo '<input type="hidden" name="acf['.$field->id.'][value_id]" value="' . $value_id . '" />';
								echo '<input type="hidden" name="acf['.$field->id.'][meta_id]" value="' . $meta_id . '" />';
							}
							else
							{
								$temp_field->value = $field->value;
							}
		
		
							echo '<label for="'.$field->input_name.'">'.$field->label.'</label>';
						
							
							if($field->instructions)
							{
								echo '<p class="instructions">'.$field->instructions.'</p>';
							}
						
							$temp_field->type = $field->type;
							$temp_field->input_name = 'acf['.$field->id.'][value]';
							$temp_field->input_class = $field->type;
							$temp_field->options = $field->options;
							
							$this->create_field($temp_field); 
						
					
						echo '</div>';
						

					} 
					
					
					if($options->field_group_layout == "in_box")
					{
						echo '</div></div>';
					}
					else
					{
						echo '</div>';
					}
				}
			
			}
			
		}// end if
		
		
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	third_party
	*
	*	@author Elliot Condon
	*	@since 2.0.6
	* 
	*-------------------------------------------------------------------------------------*/
	function third_party()
	{
		include('core/third_party.php');
	}
	
	
}