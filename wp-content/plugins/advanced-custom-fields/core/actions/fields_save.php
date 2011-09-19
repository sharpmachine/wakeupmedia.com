<?php
/*---------------------------------------------------------------------------------------------
	Save Fields Meta Box
---------------------------------------------------------------------------------------------*/
if(isset($_POST['fields_meta_box']) &&  $_POST['fields_meta_box'] == 'true')
{
    
	// set table name
	global $wpdb;
	$table_name = $wpdb->prefix.'acf_fields';
	
	
	// remove all old fields from the database
	$wpdb->query("DELETE FROM $table_name WHERE post_id = '$post_id'");
	
	
	// loop through fields and save them
	$i = 0;
	foreach($_POST['acf']['fields'] as $key => $field)
	{
	
		if($key == 999)
		{
			continue;
		}
		
		
		// defaults
		if(!isset($field['label'])) { $field['label'] = ""; }
		if(!isset($field['name'])) { $field['label'] = ""; }
		if(!isset($field['type'])) { $field['label'] = "text"; }
		if(!isset($field['options'])) { $field['options'] = array(); }
		if(!isset($field['instructions'])) { $field['instructions'] = ""; }
		if(!isset($field['default_value'])) { $field['default_value'] = ""; }
		
		
		// clean field
		$field = stripslashes_deep($field);
		
		
		// format options if needed
		if($this->field_method_exists($field['type'], 'format_options'))
		{
			$field['options'] = $this->fields[$field['type']]->format_options($field['options']);
		}
		
		
		// create data
		$data = array(
			'order_no' 		=> 	$i,
			'post_id'		=>	$post_id,
			'label'			=>	$field['label'],
			'name'			=>	$field['name'],
			'type'			=>	$field['type'],
			'options'		=>	serialize($field['options']),
			'instructions'	=>	$field['instructions'],
			'default_value'	=>	$field['default_value'],
		);
		
		
		// if there is an id, this field already exists, so save it in the same ID spot
		if($field['id'])
		{
			$data['id']	= (int) $field['id'];
		}
		
		
		// save field as row in database
		$wpdb->insert($table_name, $data);
		
		
		// save field if needed (used to save sub fields)
		if($this->field_method_exists($field['type'], 'save_field'))
		{
			if($field['id'])
			{
				$parent_id = $field['id'];
			}
			else
			{
				$parent_id = $wpdb->insert_id;
			}
			
			
			$this->fields[$field['type']]->save_field($post_id, $parent_id, $field);
		}
		
		
		// increase order_no
		$i++;
	}
}

?>