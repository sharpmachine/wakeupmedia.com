<?php

class acf_Radio
{
	var $name;
	var $title;
	
	function acf_Radio()
	{
		$this->name = 'radio';
		$this->title = __('Radio Button','acf');
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	HTML
	*
	*	@author Elliot Condon
	*	@since 2.0.6
	* 
	*-------------------------------------------------------------------------------------*/
	
	function html($field)
	{
		if(empty($field->value))
		{
			$field->value = array();
		}
		
		if(empty($field->options['choices']))
		{
			
			echo '<p>' . __("No choices to choose from",'acf') . '</p>';
			
			return false;
		}
		
		$layout = isset($field->options['layout']) ? $field->options['layout'] : 'vertical';
		
		
		echo '<ul class="radio_list ' .$field->input_class . ' ' . $layout . '">';
			
		foreach($field->options['choices'] as $key => $value)
		{
			$selected = '';
			
			if($key == $field->value)
			{
				$selected = 'checked="checked"';
			}
			
			echo '<li><label><input type="radio" class="'.$field->input_class.'" name="'.$field->input_name.'" value="'.$key.'" '.$selected.' />'.$value.'</label></li>';
		}
		
		echo '</ul>';

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
		$options = $field->options;
		
		// layout
		$options['layout'] = isset($options['layout']) ? $options['layout'] : 'vertical';
		
		// implode checkboxes so they work in a textarea
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
		
		?>


		<tr class="field_option field_option_radio">
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
			</td>
		</tr>
		<tr class="field_option field_option_radio">
			<td class="label">
				<label for=""><?php _e("Layout",'acf'); ?></label>
			</td>
			<td>
				<?php 
					$temp_field = new stdClass();	
					$temp_field->type = 'radio';
					$temp_field->input_name = 'acf[fields]['.$key.'][options][layout]';
					$temp_field->input_class = '';
					$temp_field->value = $options['layout'];
					$temp_field->options = array('layout' => 'horizontal', 'choices' => array('vertical' => 'Vertical', 'horizontal' => 'Horizontal'));
					$this->html($temp_field); 
				?>
			</td>
		</tr>

	
		<?php
	}

	
	/*--------------------------------------------------------------------------------------
	*
	*	Format Options
	*	- this is called from save_field.php, this function formats the options into a savable format
	*
	*	@author Elliot Condon
	*	@since 2.0.6
	* 
	*-------------------------------------------------------------------------------------*/

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
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	Format Value
	*	- this is called from api.php
	*
	*	@author Elliot Condon
	*	@since 2.0.6
	* 
	*-------------------------------------------------------------------------------------*/
	
	function format_value_for_api($value, $options = null)
	{
		if(!$value)
		{
			return false;
		}
		
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
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	Format Value for input
	*	- this is called from acf.php
	*
	*	@author Elliot Condon
	*	@since 2.0.6
	* 
	*-------------------------------------------------------------------------------------*/

	function format_value_for_input($value)
	{
		return $this->format_value_for_api($value);
	}
}

?>