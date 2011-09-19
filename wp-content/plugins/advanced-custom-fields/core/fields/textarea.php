<?php

class acf_Textarea
{
	var $name;
	var $title;
	var $parent;
	
	function acf_Textarea($parent)
	{
		$this->name = 'textarea';
		$this->title = __("Text Area",'acf');
		$this->parent = $parent;
	}
	
	function html($field)
	{
		// remove unwanted <br /> tags
		$field->value = str_replace('<br />','',$field->value);
		echo '<textarea id="'.$field->input_name.'" rows="4" class="'.$field->input_class.'" name="'.$field->input_name.'" >'.$field->value.'</textarea>';
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	Options HTML
	*
	*	@author Elliot Condon
	*	@since 2.0.6
	* 
	*-------------------------------------------------------------------------------------*/
	
	function options_html($key, $field)
	{
	
		?>
		<tr class="field_option field_option_textarea">
			<td class="label">
				<label><?php _e("Default Value",'acf'); ?></label>
			</td>
			<td>
				<?php 
					$temp_field = new stdClass();
					$temp_field->type = 'textarea';
					$temp_field->input_name = 'acf[fields]['.$key.'][default_value]';
					$temp_field->input_class = 'default_value';
					$temp_field->value = $field->default_value;
					$this->parent->create_field($temp_field); 
				?>
			</td>
		</tr>
		<?php
	}
	
	function format_value_for_input($value)
	{
		$value = htmlspecialchars($value, ENT_QUOTES);
		return $value;
	}
	
	function format_value_for_api($value, $options = null)
	{
		$value = nl2br($value);
		return $value;
	}
}

?>