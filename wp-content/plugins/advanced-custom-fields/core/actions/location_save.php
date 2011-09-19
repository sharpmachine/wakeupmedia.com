<?php
/*---------------------------------------------------------------------------------------------
	Fields Meta Box
---------------------------------------------------------------------------------------------*/
if(isset($_POST['location_meta_box']) && $_POST['location_meta_box'] == 'true')
{
	
	global $wpdb;
	$table_name = $wpdb->prefix.'acf_rules';
	
	
	// remove all old fields from the database
	$wpdb->query("DELETE FROM $table_name WHERE acf_id = '$post_id'");
	
	
	// turn inputs into database friendly data
	$rules = $_POST['acf']['location']['rules'];
	$allorany = $_POST['acf']['location']['allorany'];
	
	update_post_meta($post_id, 'allorany', $allorany);
	
	if($rules)
	{
		foreach($rules as $k => $rule)
		{
			$data = array(
				'acf_id'	=>	$post_id,
				'order_no'	=>	$k,
				'param'		=>	$rule['param'],
				'operator'	=>	$rule['operator'],
				'value'		=>	$rule['value']
			);
		
			if(isset($rule['id']))
			{
				$data['id'] = $rule['id'];
			}
			
			$wpdb->insert($table_name, $data);
		}
	}
	
}

?>