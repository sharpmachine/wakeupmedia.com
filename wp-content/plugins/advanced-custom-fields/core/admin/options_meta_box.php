<?php
	
	global $post;
		
	// get options
	$options = $this->get_acf_options($post->ID);
	
	// create temp field from creating inputs
	$temp_field = new stdClass();
?>

<input type="hidden" name="options_meta_box" value="true" />
<input type="hidden" name="ei_noncename" id="ei_noncename" value="<?php echo wp_create_nonce('ei-n'); ?>" />

<table class="acf_input widefat" id="acf_options">
	<tr>
		<td class="label">
			<label for="post_type"><?php _e("Show on page",'acf'); ?></label>
			<p class="description"><?php _e("Deselect items to hide them on the edit page",'acf'); ?></p>
			<p class="description"><?php _e("If multiple ACF groups appear on an edit page, the first ACF group's options will be used. The first ACF group is the one with the lowest order number.",'acf'); ?></p>
		</td>
		<td>
			<?php 
			
			$temp_field->type = 'checkbox';
			$temp_field->input_name = 'acf[options][show_on_page]';
			$temp_field->input_class = '';
			$temp_field->value = $options->show_on_page;
			$temp_field->options = array(
				'choices' => array(
					'the_content'	=>	'Content Editor',
					'custom_fields'	=>	'Custom Fields',
					'discussion'	=>	'Discussion',
					'comments'		=>	'Comments',
					'slug'			=>	'Slug',
					'author'		=>	'Author'
				)
			);
			
			$this->create_field($temp_field); 
			
			?>
			
			
		</td>
	</tr>
	<tr>
		<td class="label">
			<label for="post_type"><?php _e("Field Group Layout",'acf'); ?></label>
			<p class="description"><?php _e("Display your field group with or without a box",'acf'); ?></p>
		</td>
		<td>
			<?php 
			
			$temp_field->type = 'select';
			$temp_field->input_name = 'acf[options][field_group_layout]';
			$temp_field->input_class = '';
			$temp_field->value = $options->field_group_layout;
			$temp_field->options = array(
				'choices' => array(
					'in_box'	=>	'In a postbox',
					'default'	=>	'No box',
				)
			);
			
			$this->create_field($temp_field); 
			
			?>
			
			
		</td>
	</tr>	
</table>