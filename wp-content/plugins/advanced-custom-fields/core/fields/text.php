<?php

class acf_Text
{
	var $name;
	var $title;
	var $parent;
	
	function acf_Text($parent)
	{
		$this->name = 'text';
		$this->title = __("Text",'acf');
		$this->parent = $parent;
	}
	
	function html($field)
	{
		echo '<input type="text" value="'.$field->value.'" id="'.$field->input_name.'" class="'.$field->input_class.'" name="'.$field->input_name.'" />';
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
		<tr class="field_option field_option_text">
			<td class="label">
				<label><?php _e("Default Value",'acf'); ?></label>
			</td>
			<td>
				<?php 
					$temp_field = new stdClass();
					$temp_field->type = 'text';
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
		return htmlspecialchars($value, ENT_QUOTES);
	}
	
}

?>