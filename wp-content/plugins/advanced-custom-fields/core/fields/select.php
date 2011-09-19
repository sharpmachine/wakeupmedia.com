<?php

class acf_Select
{
	var $name;
	var $title;
	var $parent;
	
	function acf_Select($parent)
	{
		$this->name = 'select';
		$this->title = __("Select",'acf');
		$this->parent = $parent;
	}
	
	function html($field)
	{
		if(isset($field->options['multiple']) && $field->options['multiple'] == '1')
		{
			$name_extra = '[]';
			if(count($field->options['choices']) <= 1)
			{
				$name_extra = '';
			}
			echo '<select id="'.$field->input_name.'" class="'.$field->input_class.'" name="'.$field->input_name.$name_extra.'" multiple="multiple" size="5" >';
		}
		else
		{
			echo '<select id="'.$field->input_name.'" class="'.$field->input_class.'" name="'.$field->input_name.'" >';	
			// add top option
			
			if(isset($field->options['allow_null']) && $field->options['allow_null'] == '1')
			{
				echo '<option value="null"> - Select - </option>';
			}
			
		}
		
		if(empty($field->options['choices']))
		{
			
			echo '<p>' . __("No choices to choose from",'acf') . '</p>';
			return false;
		}
		
		// loop through values and add them as options
		foreach($field->options['choices'] as $key => $value)
		{
			$selected = '';
			if(is_array($field->value))
			{
				// 2. If the value is an array (multiple select), loop through values and check if it is selected
				if(in_array($key, $field->value))
				{
					$selected = 'selected="selected"';
				}
			}
			else
			{
				// 3. this is not a multiple select, just check normaly
				if($key == $field->value)
				{
					$selected = 'selected="selected"';
				}
			}	
			
			
			echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
		}

		echo '</select>';
	}
	
	
	function options_html($key, $field)
	{
		$options = $field->options;
		
		// implode selects so they work in a textarea
		if(isset($options['choices']) && is_array($options['choices']))
		{		
			foreach($options['choices'] as $choice_key => $choice_val)
			{
				$options['choices'][$choice_key] = $choice_key.' : '.$choice_val;
			}
			$options['choices'] = implode("\n", $options['choices']);
		}
		else
		{
			$options['choices'] = "";
		}
		
		$options['multiple'] = isset($options['multiple']) ? $options['multiple'] : '0';
		$options['allow_null'] = isset($options['allow_null']) ? $options['allow_null'] : '0';

		?>

		<tr class="field_option field_option_select">
			<td class="label">
				<label for=""><?php _e("Choices",'acf'); ?></label>
				<p class="description"><?php _e("Enter your choices one per line<br />
				<br />
				Red<br />
				Blue<br />
				<br />
				or<br />
				<br />
				red : Red<br />
				blue : Blue",'acf'); ?></p>
			</td>
			<td>
				<textarea rows="5" name="acf[fields][<?php echo $key; ?>][options][choices]" id=""><?php echo $options['choices']; ?></textarea>
				<p class="description"></p>
			</td>
		</tr>
		<tr class="field_option field_option_select">
			<td class="label">
				<label><?php _e("Allow Null?",'acf'); ?></label>
			</td>
			<td>
				<?php 
					$temp_field = new stdClass();	
					$temp_field->type = 'true_false';
					$temp_field->input_name = 'acf[fields]['.$key.'][options][allow_null]';
					$temp_field->input_class = '';
					$temp_field->value = $options['allow_null'];
					$temp_field->options = array('message' => 'Add null value above choices');
					$this->parent->create_field($temp_field); 
				?>
			</td>
		</tr>
		<tr class="field_option field_option_select">
			<td class="label">
				<label><?php _e("Select multiple values?",'acf'); ?></label>
			</td>
			<td>
				<?php 
					$temp_field->type = 'true_false';
					$temp_field->input_name = 'acf[fields]['.$key.'][options][multiple]';
					$temp_field->input_class = '';
					$temp_field->value = $options['multiple'];
					$temp_field->options = array('message' => 'Turn this drop-down into a multi-select');
					$this->parent->create_field($temp_field); 
				?>
			</td>
		</tr>

		<?php
	}
		
	
	/*---------------------------------------------------------------------------------------------
	 * Format Options
	 * - this is called from save_field.php, this function formats the options into a savable format
	 *
	 * @author Elliot Condon
	 * @since 1.1
	 * 
	 ---------------------------------------------------------------------------------------------*/
	function format_options($options)
	{	
		// if no choices, dont do anything
		if(!$options['choices'] || is_array($options['choices']))
		{
			return $options;
		}
		
		
		// explode choices from each line
		if(strpos($options['choices'], "\n") !== false)
		{
			// found multiple lines, explode it
			$choices = explode("\n", $options['choices']);
		}
		else
		{
			// no multiple lines! 
			$choices = array($options['choices']);
		}
		
		
		
		$new_choices = array();
		foreach($choices as $choice)
		{
			if(strpos($choice, ' : ') !== false)
			{

				$choice = explode(' : ', $choice);
				$new_choices[trim($choice[0])] = trim($choice[1]);
			}
			else
			{
				$new_choices[trim($choice)] = trim($choice);
			}
		}
		
		
		// return array containing all choices
		$options['choices'] = $new_choices;
		
		return $options;
	}
	
	
	/*---------------------------------------------------------------------------------------------
	 * Format Value
	 * - this is called from api.php
	 *
	 * @author Elliot Condon
	 * @since 1.1
	 * 
	 ---------------------------------------------------------------------------------------------*/
	function format_value_for_api($value, $options = null)
	{
		$value = $this->format_value_for_input($value);
		
		if($value == 'null')
		{
			return false;
		}
		
		return $value;
	}
	
	
	/*---------------------------------------------------------------------------------------------
	 * Format Value for input
	 * - this is called from api.php
	 *
	 * @author Elliot Condon
	 * @since 1.1
	 * 
	 ---------------------------------------------------------------------------------------------*/
	function format_value_for_input($value)
	{
		$is_array = @unserialize($value);
		
		if($is_array)
		{
			return unserialize($value);
		}
		else
		{
			return $value;
		}
	}
	
	
	
}

?>