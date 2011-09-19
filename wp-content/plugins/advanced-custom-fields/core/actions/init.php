<?php

/*----------------------------------------------------------------------
*
*	upgrade
*
*---------------------------------------------------------------------*/

$version = get_option('acf_version','1.0.5');

if(isset($_POST['acf_upgrade']))
{
	$this->upgrade();
}
else
{
	// if current version is less than the latest upgrade version, show the upgrade message
	if(version_compare($version,$this->upgrade_version) < 0)
	{
		global $acf_temp_mesage;
		$acf_temp_mesage = '<form method="post"><p>Advanced Custom Fields v' . $this->version . ' requires a database upgrade. Please <a href="http://codex.wordpress.org/Backing_Up_Your_Database">backup your database</a> then click <input type="submit" class="button" name="acf_upgrade" value="Upgrade Database" /></p></form>';
		
		function my_temp_notice()
		{
			global $acf_temp_mesage;
		    echo '<div class="updated" id="message">'.$acf_temp_mesage.'</div>';
		}
		add_action('admin_notices', 'my_temp_notice');
	}
	elseif($version != $this->version)
	{
		update_option('acf_version',$this->version);
	}
}



		



/*----------------------------------------------------------------------
*
*	deactivate_field
*
*---------------------------------------------------------------------*/

if(isset($_POST['acf_field_deactivate']))
{
	// delete field
	$field = $_POST['acf_field_deactivate'];
	$option = 'acf_'.$field.'_ac';
	delete_option($option);
	
	
	// update activated fields
	$this->activated_fields = $this->get_activated_fields();
	$this->fields = $this->get_field_types();
	
	
	//set message
	$acf_message_field = "";
	if($field == "repeater")
	{
		$acf_message_field = "Repeater Field";
	}
	elseif($field == "options_page")
	{
		$acf_message_field = "Options Page";
	}
	
	
	// show message on page
	$this->admin_message($acf_message_field.' deactivated');
	
}



/*----------------------------------------------------------------------
*
*	activate_field
*
*---------------------------------------------------------------------*/

if(isset($_POST['acf_field_activate']) && isset($_POST['acf_ac']))
{
	
	$field = $_POST['acf_field_activate'];
	$ac = $_POST['acf_ac'];
	
	
	// update option
	$option = 'acf_'.$field.'_ac';
	update_option($option, $ac);
	
	
	// update activated fields
	$old_count = count($this->activated_fields);
	$this->activated_fields = $this->get_activated_fields();
	$this->fields = $this->get_field_types();
	$new_count = count($this->activated_fields);
	
	
	// set message
	global $acf_message_field;
	$acf_message_field = "";
	if($field == "repeater")
	{
		$acf_message_field = "Repeater Field activated";
	}
	elseif($field == "options_page")
	{
		$acf_message_field = "Options Page activated";
	}
	
	
	// show message
	if($new_count == $old_count)
	{
		$this->admin_message('Activation code unrecognized');
	}
	else
	{
		$this->admin_message($acf_message_field);
	}
	
}



/*--------------------------------------------------------------------------------------
*
*	Create Post Type
*
*	@author Elliot Condon
*	@since 1.0.6
* 
*-------------------------------------------------------------------------------------*/

$labels = array(
    'name' => __( 'Advanced&nbsp;Custom&nbsp;Fields', 'acf' ),
	'singular_name' => __( 'Advanced Custom Fields', 'acf' ),
    'add_new' => __( 'Add New' , 'acf' ),
    'add_new_item' => __( 'Add New Advanced Custom Field Group' , 'acf' ),
    'edit_item' =>  __( 'Edit Advanced Custom Field Group' , 'acf' ),
    'new_item' => __( 'New Advanced Custom Field Group' , 'acf' ),
    'view_item' => __('View Advanced Custom Field Group'),
    'search_items' => __('Search Advanced Custom Field Groups'),
    'not_found' =>  __('No Advanced Custom Field Groups found'),
    'not_found_in_trash' => __('No Advanced Custom Field Groups found in Trash'), 
);


$supports = array(
	'title',
	//'revisions',
	//'custom-fields',
	'page-attributes'
);

register_post_type('acf', array(
	'labels' => $labels,
	'public' => false,
	'show_ui' => true,
	'_builtin' =>  false,
	'capability_type' => 'page',
	'hierarchical' => true,
	'rewrite' => array("slug" => "acf"),
	'query_var' => "acf",
	'supports' => $supports,
	'show_in_menu'	=>false,
));


/*--------------------------------------------------------------------------------------
*
*	Custom Columns
*
*	@author Elliot Condon
*	@since 2.1.0
* 
*-------------------------------------------------------------------------------------*/
 
function acf_columns_filter($columns)
{
	$columns = array(
		'cb'	 	=> '<input type="checkbox" />',
		'title' 	=> 'Title',
	);
	return $columns;
}

add_filter("manage_edit-acf_columns", "acf_columns_filter");

?>