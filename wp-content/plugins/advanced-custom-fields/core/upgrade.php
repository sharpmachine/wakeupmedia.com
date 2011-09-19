<?php

/*--------------------------------------------------------------------------------------
*
*	Update - run on update
*
*	@author Elliot Condon
*	@since 1.0.6
* 
*-------------------------------------------------------------------------------------*/

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
global $wpdb;


// tables
$acf_fields = $wpdb->prefix.'acf_fields';
$acf_values = $wpdb->prefix.'acf_values';
$acf_rules = $wpdb->prefix.'acf_rules';
$wp_postmeta = $wpdb->prefix.'postmeta';

// get current version
$version = get_option('acf_version','1.0.5');
$acf_update_msg = false;


/*--------------------------------------------------------------------------------------
*
*	1.1.0
* 
*-------------------------------------------------------------------------------------*/

if(version_compare($version,'1.1.0') < 0)
{


	// create acf_fields table
	$sql = "CREATE TABLE " . $acf_fields . " (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		order_no int(9) NOT NULL DEFAULT '0',
		post_id bigint(20) NOT NULL DEFAULT '0',
		parent_id bigint(20) NOT NULL DEFAULT '0',
		label text NOT NULL,
		name text NOT NULL,
		instructions text NOT NULL,
		default_value text NOT NULL,
		type text NOT NULL,
		options text NOT NULL,
		UNIQUE KEY id (id)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	dbDelta($sql);
	
	
	// create acf_values table
	$sql = "CREATE TABLE " . $acf_values . " (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		post_id bigint(20) NOT NULL DEFAULT '0',
		order_no int(9) NOT NULL DEFAULT '0',
		field_id bigint(20) NOT NULL DEFAULT '0',
		sub_field_id bigint(20) NOT NULL DEFAULT '0',
		value bigint(20) NOT NULL DEFAULT '0',
		UNIQUE KEY id (id)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	dbDelta($sql);
	
	
	// create acf_rules table
	$sql = "CREATE TABLE " . $acf_rules . " (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		acf_id bigint(20) NOT NULL DEFAULT '0',
		order_no int(9) NOT NULL DEFAULT '0',
		param text NOT NULL,
		operator text NOT NULL,
		value text NOT NULL,
		UNIQUE KEY id (id)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	dbDelta($sql);
	

	
	update_option('acf_version','1.1.0');
	$version = '1.1.0';
}


/*--------------------------------------------------------------------------------------
*
*	2.1.0
* 
*-------------------------------------------------------------------------------------*/

if(version_compare($version,'2.1.0') < 0)
{

	// add default_value to fields table
	$sql = "CREATE TABLE " . $acf_fields . " (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		order_no int(9) NOT NULL DEFAULT '0',
		post_id bigint(20) NOT NULL DEFAULT '0',
		parent_id bigint(20) NOT NULL DEFAULT '0',
		label text NOT NULL,
		name text NOT NULL,
		instructions text NOT NULL,
		default_value text NOT NULL,
		type text NOT NULL,
		options text NOT NULL,
		UNIQUE KEY id (id)
	) DEFAULT CHARSET=utf8;";
	dbDelta($sql);
	
	
	// images and files are all now saved as id's
	$fields = $wpdb->get_results("SELECT id FROM $acf_fields WHERE type = 'image' OR type = 'file'");
	$postmeta = $wpdb->prefix.'postmeta';
	 	
	if($fields)
	{
		foreach($fields as $field)
		{
			$values = $wpdb->get_results("SELECT id,value FROM $acf_values WHERE field_id = '$field->id'");
			if($values)
			{
				foreach($values as $value)
				{
					if(!empty($value->value) && !is_numeric($value->value))
					{
						$find_value = str_replace(get_bloginfo('url') . '/wp-content/uploads/', '', $value->value);
						$attachment_id = $wpdb->get_var("SELECT post_id FROM $postmeta WHERE meta_value = '$find_value'");
						
						// update value 
						$wpdb->query("UPDATE $acf_values SET value = '$attachment_id' WHERE id = '$value->id'");
					}
				}
			}
		}
	}
	
	
	// values are now stored as custom fields
	$values = $wpdb->get_results("SELECT v.id, v.value, v.post_id, f.name FROM $acf_values v LEFT JOIN $acf_fields f ON v.field_id = f.id ORDER BY v.id ASC");
	if($values)
	{
		foreach($values as $value)
		{
			if($value->value == ""){continue;}
			
			$data = array(
				'post_id'		=>	$value->post_id,
				'meta_key'		=>	$value->name,
				'meta_value'	=>	$value->value,
			);
			
			$wpdb->insert($wp_postmeta, $data);
			
			$new_id = $wpdb->insert_id;
			
			if($new_id && $new_id != 0)
			{
				$wpdb->query("UPDATE $acf_values SET value = '$new_id' WHERE id = '$value->id'");
			}
			

		}
	}
	
	
	// value is now an int
	$sql = "CREATE TABLE " . $acf_values . " (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		post_id bigint(20) NOT NULL DEFAULT '0',
		order_no int(9) NOT NULL DEFAULT '0',
		field_id bigint(20) NOT NULL DEFAULT '0',
		sub_field_id bigint(20) NOT NULL DEFAULT '0',
		value bigint(20) NOT NULL DEFAULT '0',
		UNIQUE KEY id (id)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	dbDelta($sql);
	
	
	// now set sub_field_if values
	$values = $wpdb->get_results("SELECT v.id, f.id as sub_field_id, f.parent_id as field_id FROM $acf_values v LEFT JOIN $acf_fields f ON v.field_id = f.id WHERE f.parent_id != '0' ORDER BY v.id ASC");
	if($values)
	{
		foreach($values as $value)
		{
			$wpdb->query("UPDATE $acf_values SET field_id = '$value->field_id', sub_field_id = '$value->sub_field_id' WHERE id = '$value->id'");
		}
	}
	
	
	// set version
	update_option('acf_version','2.0.6');
	$version = '2.0.6';
	
}


/*--------------------------------------------------------------------------------------
*
*	2.1.4
* 
*-------------------------------------------------------------------------------------*/

if(version_compare($version,'2.1.4') < 0)
{
	
	// add back in post_id to values table (useful for duplicate posts / third party stuff)
	$sql = "CREATE TABLE " . $acf_values . " (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		post_id bigint(20) NOT NULL DEFAULT '0',
		order_no int(9) NOT NULL DEFAULT '0',
		field_id bigint(20) NOT NULL DEFAULT '0',
		sub_field_id bigint(20) NOT NULL DEFAULT '0',
		value bigint(20) NOT NULL DEFAULT '0',
		UNIQUE KEY id (id)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	dbDelta($sql);
	
	
	// copy across post_id
	$sql2 = "SELECT m.post_id, v.id 
		FROM $wp_postmeta m 
		LEFT JOIN $acf_values v ON m.meta_id = v.value";
		
	$values = $wpdb->get_results($sql2);
	if($values)
	{
		foreach($values as $value)
		{
			$wpdb->query("UPDATE $acf_values SET post_id = '$value->post_id' WHERE id = '$value->id'");
		}
	}

	// set version
	update_option('acf_version','2.1.4');
	$version = '2.1.4';
}
/*--------------------------------------------------------------------------------------
*
*	Finish
* 
*-------------------------------------------------------------------------------------*/

$this->admin_message('Advanced Custom Fields successfully upgraded to ' . $this->version . '! <a href="' . get_bloginfo('url') . '/wp-admin/plugin-install.php?tab=plugin-information&plugin=advanced-custom-fields&section=changelog&TB_iframe=true&width=640&height=559" class="thickbox">See what\'s new</a>');

update_option('acf_version',$this->version);

?>