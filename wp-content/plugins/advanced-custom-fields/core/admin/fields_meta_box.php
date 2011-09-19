<?php
	
	// get fields
	global $post;
	$fields = $this->get_fields($post->ID);
	

	// add clone
	$field = new stdClass();
	$field->label = 'New Field';
	$field->name = 'new_field';
	$field->type = 'text';
	$field->options = array();
	$field->instructions = '';
	$field->default_value = '';
	$fields[999] = $field;
	

	// get name of all fields for use in field type drop down
	$fields_names = array();
	foreach($this->fields as $field)
	{
		$fields_names[$field->name] = $field->title;
	}

?>

<input type="hidden" name="fields_meta_box" value="true" />
<input type="hidden" name="total_fields" value="<?php echo count($fields); ?>" />
<input type="hidden" name="fields_limit" value="99" />
<input type="hidden" name="ei_noncename" id="ei_noncename" value="<?php echo wp_create_nonce('ei-n'); ?>" />

<?php
/*--------------------------------------------------------------------------------------
*
*	Fields Header
* 
*-------------------------------------------------------------------------------------*/
?>
<div class="fields_header">
	<table class="acf widefat">
		<thead>
			<tr>
				<th class="field_order"><?php _e('Field Order','acf'); ?></th>
				<th class="field_label"><?php _e('Field Label','acf'); ?></th>
				<th class="field_name"><?php _e('Field Name','acf'); ?></th>
				<th class="field_type"><?php _e('Field Type','acf'); ?></th>
			</tr>
		</thead>
	</table>
</div>
<div class="fields">
		
	<div class="no_fields_message" <?php if(sizeof($fields) > 1){ echo 'style="display:none;"'; } ?>>
		<?php _e("No fields. Click the \"+ Add Field button\" to create your first field.",'acf'); ?>
	</div>
	
	<?php foreach($fields as $key => $field): ?>
	
	
	<div class="<?php if($key == 999){echo "field_clone";}else{echo "field";} ?>">
		<input type="hidden" name="acf[fields][<?php echo $key; ?>][id]'" value="<?php echo $field->id; ?>" />
		<?php
		/*--------------------------------------------------------------------------------------
		*
		*	Field Meta
		* 
		*-------------------------------------------------------------------------------------*/
		?>
		<div class="field_meta">
			<table class="acf widefat">
				<tr>
					<td class="field_order"><span class="circle"><?php echo ($key+1); ?></span></td>
					<td class="field_label">
						
						<strong>
							<a class="acf_edit_field row-title" title="Edit this Field" href="javascript:;"><?php echo $field->label; ?></a>
						</strong>
						<div class="row_options">
							<span><a class="acf_edit_field" title="Edit this Field" href="javascript:;"><?php _e("Edit",'acf'); ?></a> | </span>
							<span><a class="acf_delete_field" title="Delete this Field" href="javascript:;"><?php _e("Delete",'acf'); ?></a>
						</div>

					</td>
					<td class="field_name">
					
						<?php echo $field->name; ?>
						
					</td>
					<td class="field_type">
					
						<?php echo $field->type; ?>
						
					</td>
				</tr>
			</table>
		</div>
		<div class="field_form_mask">
			<div class="field_form">
				
				<table class="acf_input widefat">
					<tbody>
						<tr class="field_label">
							<td class="label">
								<label><span class="required">*</span><?php _e("Field Label",'acf'); ?></label>
								<p class="description"><?php _e("This is the name which will appear on the EDIT page",'acf'); ?></p>
							</td>
							<td>
								<?php 
								$temp_field = new stdClass();
								
								$temp_field->type = 'text';
								$temp_field->input_name = 'acf[fields]['.$key.'][label]';
								$temp_field->input_class = 'label';
								$temp_field->value = $field->label;
								
								$this->create_field($temp_field); 
						
								?>
								
							</td>
						</tr>
						<tr class="field_name">
							<td class="label"><label><span class="required">*</span><?php _e("Field Name",'acf'); ?></label>
							<p class="description"><?php _e("Single word, no spaces. Underscores and dashes allowed",'acf'); ?></p>
							</td>
							<td>
								<?php 
							
								$temp_field->type = 'text';
								$temp_field->input_name = 'acf[fields]['.$key.'][name]';
								$temp_field->input_class = 'name';
								$temp_field->value = $field->name;
								
								$this->create_field($temp_field); 
							
								?>
								
							</td>
						</tr>
						<tr class="field_type">
							<td class="label"><label><span class="required">*</span><?php _e("Field Type",'acf'); ?></label></td>
							<td>
								<?php 
							
								$temp_field->type = 'select';
								$temp_field->input_name = 'acf[fields]['.$key.'][type]';
								$temp_field->input_class = 'type';
								$temp_field->value = $field->type;
								$temp_field->options = array('choices' => $fields_names);
								
								$this->create_field($temp_field); 
							
								?>
							</td>
						</tr>
						<tr class="field_instructions">
							<td class="label"><label><?php _e("Field Instructions",'acf'); ?></label>
							<p class="description"><?php _e("Instructions for authors. Shown when submitting data",'acf'); ?></p></td>
							<td>
								<?php 
							
								$temp_field->type = 'textarea';
								$temp_field->input_name = 'acf[fields]['.$key.'][instructions]';
								$temp_field->input_class = 'instructions';
								$temp_field->value = $field->instructions;
								
								$this->create_field($temp_field); 
							
								?>
							</td>
						</tr>
						<?php
						/*<tr class="field_save_as_cf">
							<td class="label">
								<label><?php _e("Is field searchable?",'acf'); ?></label>
							</td>
							<td>
								<?php 
									$temp_field->type = 'true_false';
									$temp_field->input_name = 'acf[fields]['.$key.'][save_as_cf]';
									$temp_field->input_class = 'save_as_cf';
									$temp_field->value = $field->save_as_cf;
									$temp_field->options = array('message' => __("Save this field's value as a standard WordPress Custom Field",'acf'));
									$this->create_field($temp_field); 
								?>
							</td>
						</tr>*/
						?>
						
						<?php foreach($fields_names as $field_name => $field_title): ?>
							<?php if(method_exists($this->fields[$field_name], 'options_html')): ?>

								<?php $this->fields[$field_name]->options_html($key, $field); ?>

							<?php endif; ?>
						<?php endforeach; ?>
						<tr class="field_save">
							<td class="label">
								<label>Save Field</label>
							</td>
							<td><input type="submit" value="Save Field" class="button-primary" name="save" />
								or <a class="acf_edit_field" title="Hide this edit screen" href="javascript:;">continue editing ACF</a>
							</td>
							
						</tr>
					</tbody>
				</table>

			</div>
		</div>
			
	</div>
	<?php endforeach; ?>
		

</div>

<div class="table_footer">
	<div class="order_message"></div>
	<a href="javascript:;" id="add_field" class="button-primary"><?php _e('+ Add Field','acf'); ?></a>
</div>