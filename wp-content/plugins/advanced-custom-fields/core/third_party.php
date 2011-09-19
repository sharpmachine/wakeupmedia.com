<?php

/*--------------------------------------------------------------------------------------
*
*	Integrate with Duplicate Posts plugin
*
*	@author unknownnf - thanks mate
*	@since 2.0.6
* 
*-------------------------------------------------------------------------------------*/
function acf_duplicate($newId, $post)
{
	// global
	global $wpdb;
	
	// tables
	$acf_fields = $wpdb->prefix.'acf_fields';
	$acf_values = $wpdb->prefix.'acf_values';
	$acf_rules = $wpdb->prefix.'acf_rules';
	$wp_postmeta = $wpdb->prefix.'postmeta';
	
	if($post->post_type == 'acf')
	{
		
		// save fields
		$sql = "SELECT *
			FROM $acf_fields
			WHERE post_id = '$post->ID'
			ORDER BY parent_id ASC";
			
		$rows = $wpdb->get_results($sql);
		
		if($rows)
		{
			$repeater_fields = array();
			
			foreach ($rows as $row) {
				
				// save postmeta
				$data = array(
					'post_id' 		=> $newId, 
					'label' 		=> $row->label,
					'name' 			=> $row->name,
					'type' 			=> $row->type,
					'parent_id' 	=> $row->parent_id,
					'options' 		=> $row->options,
					'order_no' 		=> $row->order_no,
					'instructions' 	=> $row->instructions,
					'default_value' => $row->default_value,
				);
				
				// override parent_id
				if( (int) $row->parent_id != 0 )
				{
					$data['parent_id'] = (int) $repeater_fields[$row->parent_id];
				}
				
				// insert
				$wpdb->insert($acf_fields, $data);
				
				// update repeater id
				if($row->type == 'repeater')
				{
					$repeater_fields[$row->id] = $wpdb->insert_id;
				}

			}

		}
		
		// save rules
		$sql = "SELECT *
			FROM $acf_rules
			WHERE acf_id = '$post->ID'";
			
		$rows = $wpdb->get_results($sql);
		
		if($rows)
		{
			foreach ($rows as $row) {
				
				// save postmeta
				$data = array(
					'acf_id' 		=> $newId, 
					'param' 		=> $row->param,
					'operator' 		=> $row->operator,
					'value' 		=> $row->value,
					'order_no' 		=> $row->order_no,
				);
				
				$wpdb->insert($acf_rules, $data);
		
			}
		}
		
	}
	else
	{
		
		// deletes duplicated acf postmeta
		$sql = "SELECT f.name 
			FROM $wp_postmeta m 
			LEFT JOIN $acf_values v ON m.meta_id = v.value
			LEFT JOIN $acf_fields f ON v.field_id = f.id 
			WHERE m.post_id = '$post->ID' AND f.name != 'NULL'";
			
		$results = $wpdb->get_results($sql);
		
		if($results)
		{
			foreach($results as $result)
			{
				$wpdb->query("DELETE FROM $wp_postmeta WHERE meta_key = '$result->name' AND post_id = '$newId'");
			}
		}
		
				
		// duplicate postmen + values
		$sql = "SELECT m.meta_key, m.meta_value, v.value, v.field_id, v.sub_field_id, v.order_no 
			FROM $wp_postmeta m 
			LEFT JOIN $acf_values v ON m.meta_id = v.value 
			LEFT JOIN $acf_fields f ON v.field_id = f.id 
			WHERE m.post_id = '$post->ID' AND f.name != 'NULL'";
			
		$rows = $wpdb->get_results($sql);
		if($rows)
		{

			foreach ($rows as $row) {
				
				// save postmeta
				$data = array(
					'post_id' => $newId, 
					'meta_key' => $row->meta_key,
					'meta_value' => $row->meta_value,
				);
				
				$wpdb->insert($wp_postmeta, $data);
				
				$new_value_id = $wpdb->insert_id;
				
				if($new_value_id && $new_value_id != 0)
				{
					// create data object to save
					$data2 = array(
						'post_id' => $newId, 
						'order_no' => $row->order_no,
						'field_id' => $row->field_id,
						'sub_field_id' => $row->sub_field_id,
						'value' => $new_value_id,
					);
					
					$wpdb->insert($acf_values, $data2);
				}
		
			}
		}
		
	}

}

add_action('dp_duplicate_page', 'acf_duplicate', 10, 2);
add_action('dp_duplicate_post', 'acf_duplicate', 10, 2); 

?>