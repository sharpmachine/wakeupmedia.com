<?php

class acf_Date_picker
{
	var $name;
	var $title;
	var $plugin_dir;
	
	function acf_Date_picker($plugin_dir)
	{
		$this->name = 'date_picker';
		$this->title = __('Date Picker','acf');
		$this->plugin_dir = $plugin_dir;
	}
	
	function html($field)
	{
		echo '<input type="text" value="'.$field->value.'" class="acf_datepicker" name="'.$field->input_name.'" data-date_format="'.$field->options['date_format'].'" />';

	}
	
	function options_html($key, $field)
	{
		$options = $field->options;
		
		if(!isset($options['date_format']))
		{
			$options['date_format'] = "";
		}
		?>
		<tr class="field_option field_option_date_picker">
			<td class="label">
				<label for=""><?php _e("Date format",'acf'); ?></label>
				<p class="description"><?php _e("eg. dd/mm/yy. read more about",'acf'); ?> <a href="http://docs.jquery.com/UI/Datepicker/formatDate">formatDate</a></p>
			</td>
			<td>
				<input type="text" name="acf[fields][<?php echo $key; ?>][options][date_format]" id="" value="<?php echo $options['date_format']; ?>" />
			</td>
		</tr>

		<?php
	}
		
	
	
}

?>