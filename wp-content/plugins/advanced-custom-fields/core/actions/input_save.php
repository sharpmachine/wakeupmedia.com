<?php
/*---------------------------------------------------------------------------------------------
	Fields Meta Box
---------------------------------------------------------------------------------------------*/
if(isset($_POST['input_meta_box']) && $_POST['input_meta_box'] == 'true')
{

    // If acf was not posted, don't go any further
    if(!isset($_POST['acf']))
    {
    	return true;
    }
    
    
	// tables
	global $wpdb;
	$acf_values = $wpdb->prefix.'acf_values';
	$wp_postmeta = $wpdb->prefix.'postmeta';
	
	
	// delete old data
	$values = $wpdb->get_results("SELECT v.id, m.meta_id FROM $acf_values v LEFT JOIN $wp_postmeta m ON v.value = m.meta_id WHERE v.post_id = '$post_id'");		
	if($values)
	{
		foreach($values as $value)
		{	
			$wpdb->query("DELETE FROM $acf_values WHERE id = '$value->id'");
			$wpdb->query("DELETE FROM $wp_postmeta WHERE meta_id = '$value->meta_id'");
		}
	}
		
	// add the new values to the database
    foreach($_POST['acf'] as $field)
    {	
    	
    	// remove all old values from the database
    	$field_id = $field['field_id'];
    	
    	
    	if(method_exists($this->fields[$field['field_type']], 'save_input'))
		{
			$this->fields[$field['field_type']]->save_input($post_id, $field);
		}
		else
		{
			//$field = apply_filters('wp_insert_post_data', $field);
			$field = stripslashes_deep( $field );
			
			
			// if select is a multiple (multiple select value), you need to save it as an array!
			if(is_array($field['value']))
			{
				$field['value'] = serialize($field['value']);
			}
			

			// create data: wp_postmeta
			$data1 = array(
				'meta_id'		=>	null,
				'post_id'		=>	$post_id,
				'meta_key'		=>	$field['field_name'],
				'meta_value'	=>	$field['value']
			);
			if(isset($field['meta_id']) && !empty($field['meta_id']))
			{
				$data1['meta_id'] = $field['meta_id'];
			}
			
			$wpdb->insert($wp_postmeta, $data1);
			
			$new_id = $wpdb->insert_id;
			
			// create data: acf_values
			if($new_id && $new_id != 0)
			{

				$data2 = array(
					'id'		=>	null,
					'post_id'	=>	$post_id,
					'field_id'	=>	$field['field_id'],
					'value'		=>	$new_id,
				);
				if(isset($field['value_id']) && !empty($field['value_id']))
				{
					$data2['id'] = $field['value_id'];
				}
				
				$wpdb->insert($acf_values, $data2);
				
			}

		}

		
    }
    //foreach($_POST['acf'] as $field)
  
	
}

?>