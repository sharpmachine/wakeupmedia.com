<?php

global $acf_global;

$acf_global = array(
	'field'	=>	0,
	'order_no'	=>	-1,
);

	
/*--------------------------------------------------------------------------------------
*
*	get_fields
*
*	@author Elliot Condon
*	@since 1.0.3
* 
*-------------------------------------------------------------------------------------*/

function get_fields($post_id = false)
{
	global $post;
	global $wpdb;
	global $acf;
	
	
	$values = array();
	
	
	// tables
	$acf_values = $wpdb->prefix.'acf_values';
	$acf_fields = $wpdb->prefix.'acf_fields';
	$wp_postmeta = $wpdb->prefix.'postmeta';
	
	
	if(!$post_id)
	{
		$post_id = $post->ID;
	}
	elseif($post_id == "options")
	{
		$post_id = 0;
	}
	
	
	$sql = "SELECT f.name 
		FROM $wp_postmeta m 
		LEFT JOIN $acf_values v ON m.meta_id = v.value
		LEFT JOIN $acf_fields f ON v.field_id = f.id 
		WHERE m.post_id = '$post_id' AND f.name != 'NULL'";
		
	$results = $wpdb->get_results($sql);


	// no value
	if(!$results)
	{
		return false;
	}
	
	
	// repeater field
	foreach($results as $field)
	{
		$values[$field->name] = get_field($field->name, $post_id);
	}


	return $values;
	
}


/*--------------------------------------------------------------------------------------
*
*	get_field
*
*	@author Elliot Condon
*	@since 1.0.3
* 
*-------------------------------------------------------------------------------------*/

function get_field($field_name, $post_id = false, $options = array())
{

	global $post;
	global $wpdb;
	global $acf;
	
	$return_id = isset($options['return_id']) ? $options['return_id'] : false;
	
	// tables
	$acf_values = $wpdb->prefix.'acf_values';
	$acf_fields = $wpdb->prefix.'acf_fields';
	$wp_postmeta = $wpdb->prefix.'postmeta';
	
	
	if(!$post_id)
	{
		$post_id = $post->ID;
	}
	elseif($post_id == "options")
	{
		$post_id = 0;
	}
	
	
	$sql = "SELECT m.meta_value as value, v.id, f.type, f.options, v.sub_field_id, v.order_no  
		FROM $wp_postmeta m 
		LEFT JOIN $acf_values v ON m.meta_id = v.value
		LEFT JOIN $acf_fields f ON v.field_id = f.id 
		WHERE f.name = '$field_name' AND m.post_id = '$post_id' ORDER BY v.order_no ASC";
		
	$results = $wpdb->get_results($sql);
	
	
	// no value
	if(!$results)
	{
		return false;
	}
	
	
	
	// normal field
	$field = $results[0];
	
	
	// repeater field
	if($field->type == 'repeater')
	{
		$return_array = array();
		
		foreach($results as $result)
		{
			$sql2 = "SELECT type, name, options 
			FROM $acf_fields 
			WHERE id = '$result->sub_field_id'";
			
			$sub_field = $wpdb->get_row($sql2);
			
			
			// format the sub field value
			if($acf->field_method_exists($sub_field->type, 'format_value_for_api'))
			{
				if(@unserialize($sub_field->options))
				{
					$sub_field->options = unserialize($sub_field->options);
				}
				else
				{
					$sub_field->options = array();
				}
				
				$result->value = $acf->fields[$sub_field->type]->format_value_for_api($result->value, $sub_field->options);
			}
			
			
			// only add the value if it is not null or false
			if($result->value != '' || $result->value != false)
			{
				if($return_id)
				{
					$return_array[$result->order_no][$sub_field->name]['id'] = (int) $result->id;
					$return_array[$result->order_no][$sub_field->name]['value'] = $result->value;
				}
				else
				{
					$return_array[$result->order_no][$sub_field->name] = $result->value;
				}
				
			}
			
		}
		
		
		// if empty, just return false
		if(empty($return_array))
		{
			$return_array = false;
		}
		
		return $return_array;
		
	}
	
	
	$value = $field->value;
	
	
	// format if needed
 	if($acf->field_method_exists($field->type, 'format_value_for_api'))
	{
		
		if(@unserialize($field->options))
		{
			$field->options = unserialize($field->options);
		}
		else
		{
			$field->options = array();
		}
		
		$value = $acf->fields[$field->type]->format_value_for_api($value, $field->options);
	}
	
	
	if($return_id)
	{
		$return_array = array(
			'id'	=>	(int) $field->id,
			'value'	=>	$value,
		);
		return $return_array;
	}
				
				
	return $value;
	
}


/*--------------------------------------------------------------------------------------
*
*	the_field
*
*	@author Elliot Condon
*	@since 1.0.3
* 
*-------------------------------------------------------------------------------------*/

function the_field($field_name, $post_id = false)
{

	$value = get_field($field_name, $post_id);
	
	if(is_array($value))
	{
		$value = @implode(', ',$value);
	}
	
	echo $value;
		
}


/*--------------------------------------------------------------------------------------
*
*	the_repeater_field
*
*	@author Elliot Condon
*	@since 1.0.3
* 
*-------------------------------------------------------------------------------------*/

function the_repeater_field($field_name, $post_id = false)
{
	global $acf_global;
	
	
	// if no field, create field + reset count
	if(!$acf_global['field'])
	{
		$acf_global['order_no'] = -1;
		$acf_global['field'] = get_field($field_name, $post_id);
	}
	
	
	// increase order_no
	$acf_global['order_no']++;
	
	
	// vars
	$field = $acf_global['field'];
	$i = $acf_global['order_no'];
	
	if(isset($field[$i]))
	{
		return true;
	}
	
	// no row, reset the global values
	$acf_global['order_no'] = -1;
	$acf_global['field'] = false;
	return false;
	
}


/*--------------------------------------------------------------------------------------
*
*	get_sub_field
*
*	@author Elliot Condon
*	@since 1.0.3
* 
*-------------------------------------------------------------------------------------*/

function get_sub_field($field_name)
{
	// global
	global $acf_global;
	
	// vars
	$field = $acf_global['field'];
	$i = $acf_global['order_no'];
	
	// no value
	if(!$field) return false;

	if(!isset($field[$i][$field_name])) return false;
	
	return $field[$i][$field_name];
}


/*--------------------------------------------------------------------------------------
*
*	the_sub_field
*
*	@author Elliot Condon
*	@since 1.0.3
* 
*-------------------------------------------------------------------------------------*/

function the_sub_field($field_name, $field = false)
{
	$value = get_sub_field($field_name, $field);
	
	if(is_array($value))
	{
		$value = implode(', ',$value);
	}
	
	echo $value;
}


/*--------------------------------------------------------------------------------------
*
*	update_the_field
*
*	@author Elliot Condon
*	@since 2.1.4
* 
*-------------------------------------------------------------------------------------*/

function update_the_field($field_name = false, $value = false, $post_id = false)
{
	// checkpoint
	if(!$field_name || !$value || !$post_id) return false;
	
	// global
	global $wpdb;
	
	// tables
	$acf_values = $wpdb->prefix.'acf_values';
	$acf_fields = $wpdb->prefix.'acf_fields';
	$wp_postmeta = $wpdb->prefix.'postmeta';
	
	// sql
	$sql = "SELECT m.meta_id  
		FROM $wp_postmeta m 
		LEFT JOIN $acf_values v ON m.meta_id = v.value
		LEFT JOIN $acf_fields f ON v.field_id = f.id 
		WHERE f.name = '$field_name' AND m.post_id = '$post_id'";
		
	$meta_id = $wpdb->get_var($sql);
	
	// no meta_value
	if(!$meta_id)
	{
		return false;
	}
	
	// update
	$save = $wpdb->update($wp_postmeta, array('meta_value' => $value), array('meta_id' => $meta_id));
	
	// return
	if($save) return true;
	
	return false;
	
}

?>