<?php
/*---------------------------------------------------------------------------------------------
	Fields Meta Box
---------------------------------------------------------------------------------------------*/
if(isset($_POST['options_meta_box']) && $_POST['options_meta_box'] == 'true')
{

	// turn inputs into database friendly data
	$options = $_POST['acf']['options'];
	
	if(!isset($options['show_on_page'])) { $options['show_on_page'] = array(); }
	if(!isset($options['field_group_layout'])) { $options['field_group_layout'] = 'default'; }

	update_post_meta($post_id, 'show_on_page', serialize($options['show_on_page']));
	update_post_meta($post_id, 'field_group_layout', $options['field_group_layout']);
		
}

?>