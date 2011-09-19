<?php

/*----------------------------------------------------------------------
*
*	import
*
*---------------------------------------------------------------------*/

require_once( ABSPATH . 'wp-admin/includes/import.php');
require_once( ABSPATH . 'wp-admin/includes/file.php');


	

// Checkpoint: Upload directory errors
$upload_dir = wp_upload_dir();
if (!empty($upload_dir['error']))
{
	$this->admin_error($upload_dir['error']);
	return;
}


// get file
$file = wp_import_handle_upload();


// Checkpoint: File Error
if(isset($file['error']))
{	
	$this->admin_error($file['error']);
	return;
}

// Checkpoint: File Type
$pos = strpos($file['file'], '.xml');
if($pos === false)
{	
	$this->admin_error('File uploaded is not a valid ACF export .xml file');
	return;
}


// Start Importing!
$posts = simplexml_load_file($file['file']);
 
 
 // Checkpoint: Import File must not be empty	
if(!$posts)
{
	$this->admin_error("Error: File is empty");
	return;
}


foreach($posts as $post)
{
	$new_post = array(
		'post_type' 	=>	'acf',
		'post_title' 	=>	$post->title,
		'post_status' 	=>	$post->post_status,
		'post_author'	=>	get_current_user_id(),
		'menu_order'	=>	$post->menu_order,
		'post_parent'	=>	$post->post_parent,
	);

	$post_id = wp_insert_post( $new_post, false );
	
	$_POST = array(
		'fields_meta_box'	=>	'true',
		'location_meta_box'	=>	'true',
		'options_meta_box'	=>	'true',
		'ei_noncename'		=>	wp_create_nonce('ei-n'),
	);

	if($post_id != 0)
	{

		if($post->fields[0]->children())
		{
			$i = -1;
			
			foreach($post->fields[0]->children() as $field)
			{
				$i++;
				
				$post_field = array(
					'label'					=>	isset($field->label) ? the_xml_value($field->label) : '',
					'name'					=>	isset($field->name) ? the_xml_value($field->name) : '',
					'type'					=>	isset($field->type) ? the_xml_value($field->type) : '',
					'default_value'			=>	isset($field->default_value) ? the_xml_value($field->default_value) : '',
					'options'				=>	array(),
					'instructions'			=>	isset($field->instructions) ? the_xml_value($field->instructions) : '',
				);
				
				if($field->options[0]->children())
				{
					foreach($field->options[0]->children() as $k => $v)
					{
						if($k == 'sub_fields')
						{
							$sub_fields = array();
							$j = -1;
							
							foreach($v->children() as $sub_field)
							{
								$j++;
								
								$post_sub_field = array(
									'label'					=>	isset($sub_field->label) ? the_xml_value($sub_field->label) : '',
			 						'name'					=>	isset($sub_field->name) ? the_xml_value($sub_field->name) : '',
			 						'type'					=>	isset($sub_field->type) ? the_xml_value($sub_field->type) : '',
			 						'default_value'			=>	isset($sub_field->default_value) ? the_xml_value($sub_field->default_value) : '',
			 						'options'				=>	array(),
								);
								
								if($sub_field->options[0]->children())
								{
									foreach($sub_field->options[0]->children() as $k2 => $v2)
									{
										$post_sub_field['options'][$k2] = the_xml_value($v2);
									}
								}
								
								$sub_fields[$j] = $post_sub_field;
							}
							// foreach($v->children() as $sub_field)
							
							$post_field[$k] = $sub_fields;
						}
						else
						{
							$post_field['options'][$k] = the_xml_value($v);
						}
					}
					// foreach($field->options[0]->children() as $k => $v)
					
				}
				// if($field->options[0]->children())
	
				$_POST['acf']['fields'][$i] = $post_field;
				
			}
			// foreach($post->fields[0]->children() as $field)
			
			
			
		}
		// if($post->fields[0]->children())
		
		
		// add location
		if($post->location[0]->children())
		{
			$i = -1;
			
			foreach($post->location[0]->rule as $rule)
			{
				$i++;
				
				$_POST['acf']['location']['rules'][$i] = array(
					'param'		=>	the_xml_value($rule->param),
					'operator'	=>	the_xml_value($rule->operator),
					'value'		=>	the_xml_value($rule->value),
				);
				
				
			}
			
			$_POST['acf']['location']['allorany'] = the_xml_value($post->location[0]->allorany);
		}
			
		// ad options
		if($post->options[0]->children())
		{
			
			foreach($post->options[0]->children() as $k => $v)
			{
				
				$_POST['acf']['options'][$k] = the_xml_value($v);
			}
			
		}
			
		$this->save_post($post_id);		
	}
	// if($post_id != 0)
	
	unset($_POST);
	
} 
// foreach($posts as $post)


if(count($posts) == 1)
{
	$this->admin_message('Imported 1 Advanced Custom Field Groups');
}
else
{
	$this->admin_message('Imported '.count($posts).' Advanced Custom Field Groups');
}





function the_xml_value($value)
{
	if(isset($value->array[0]))
	{
		$array = array();
		foreach($value->array[0]->children() as $v)
		{
			$att = $v->attributes();
			if(isset($att['key']))
			{
				$key = (string) $att['key'];
				$array[$key] = (string) $v;
			}
			else
			{
				$array[] = (string) $v;
			}
				
		}
		return $array;
	}
	else
	{
		return (string) $value;
	}
}

?>