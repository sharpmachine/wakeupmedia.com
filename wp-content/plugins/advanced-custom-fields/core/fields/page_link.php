<?php

class acf_Page_link
{
	var $name;
	var $title;
	var $parent;
	
	function acf_Page_link($parent)
	{
		$this->name = 'page_link';
		$this->title = __('Page Link','acf');
		$this->parent = $parent;
	}
	
	
	/*---------------------------------------------------------------------------------------------
	 * HTML
	 * - this is called all over the shop, it creates the input html
	 *
	 * @author Elliot Condon
	 * @since 1.1
	 * 
	 ---------------------------------------------------------------------------------------------*/
	function html($field)
	{	
		// get post types
		if(isset($field->options['post_type']) && is_array($field->options['post_type']) && $field->options['post_type'][0] != "")
		{
			// 1. If select has selected post types, just use them
			$post_types = $field->options['post_type'];
		}
		else
		{
			//2. If not post types have been selected, load all the public ones
			$post_types = get_post_types(array('public' => true));
			foreach($post_types as $key => $value)
			{
				if($value == 'attachment')
				{
					unset($post_types[$key]);
				}
			}
		}
		

		// start select
		if(isset($field->options["multiple"]) && $field->options["multiple"] == '1')
		{
			$name_extra = '[]';
			echo '<select id="'.$field->input_name.'" class="'.$field->input_class.'" name="'.$field->input_name.$name_extra.'" multiple="multiple" size="5" >';
		}
		else
		{
			echo '<select id="'.$field->input_name.'" class="'.$field->input_class.'" name="'.$field->input_name.'" >';	
			
			// add null
			if(isset($field->options['allow_null']) && $field->options['allow_null'] == '1')
			{
				echo '<option value="null"> - Select - </option>';
			}
		}
		
		
		
		foreach($post_types as $post_type)
		{
			// get posts
			$posts = false;
			
			if(is_post_type_hierarchical($post_type))
			{
				// get pages
				$posts = get_pages(array(
					'numberposts' => -1,
					'post_type' => $post_type,
					'sort_column' => 'menu_order',
					'order' => 'ASC',
					'meta_key' => $options['meta_key'],
					'meta_value' => $options['meta_value'],
				));
			}
			else
			{
				// get posts
				$posts = get_posts(array(
					'numberposts' => -1,
					'post_type' => $post_type,
					'orderby' => 'title',
					'order' => 'ASC',
					'meta_key' => $options['meta_key'],
					'meta_value' => $options['meta_value'],
				));
			}
			
			
			// if posts, make a group for them
			if($posts)
			{
				echo '<optgroup label="'.$post_type.'">';
				
				foreach($posts as $post)
				{
					$key = $post->ID;
					
					$value = '';
					$ancestors = get_ancestors($post->ID, $post_type);
					if($ancestors)
					{
						foreach($ancestors as $a)
						{
							$value .= '– ';
						}
					}
					$value .= get_the_title($post->ID);
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
				
				echo '</optgroup>';
				
			}// endif
			
		}// endforeach
		

		echo '</select>';

	}
	

	/*---------------------------------------------------------------------------------------------
	 * Options HTML
	 * - called from fields_meta_box.php
	 * - displays options in html format
	 *
	 * @author Elliot Condon
	 * @since 1.1
	 * 
	 ---------------------------------------------------------------------------------------------*/
	function options_html($key, $field)
	{
		$options = $field->options;

		$options['post_type'] = isset($options['post_type']) ? $options['post_type'] : '';
		$options['multiple'] = isset($options['multiple']) ? $options['multiple'] : '0';
		$options['allow_null'] = isset($options['allow_null']) ? $options['allow_null'] : '0';
		
		?>

		<tr class="field_option field_option_page_link">
			<td class="label">
				<label for=""><?php _e("Post Type",'acf'); ?></label>
				<p class="description"><?php _e("Filter posts by selecting a post type<br />
				Tip: deselect all post types to show all post type's posts",'acf'); ?></p>
			</td>
			<td>
				<?php 
				$post_types = array('' => '-All-');
				
				foreach (get_post_types() as $post_type ) {
				  $post_types[$post_type] = $post_type;
				}
				
				unset($post_types['attachment']);
				unset($post_types['nav_menu_item']);
				unset($post_types['revision']);
				unset($post_types['acf']);
				
				?>
				<?php 
					$temp_field = new stdClass();	
					$temp_field->type = 'select';
					$temp_field->input_name = 'acf[fields]['.$key.'][options][post_type]';
					$temp_field->input_class = '';
					$temp_field->value = $options['post_type'];
					$temp_field->options = array('choices' => $post_types, 'multiple' => '1');
					$this->parent->create_field($temp_field); 
				
				?>
				
			</td>
		</tr>
		<tr class="field_option field_option_page_link">
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
		<tr class="field_option field_option_page_link">
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
	 * Format Value
	 * - this is called from api.php
	 *
	 * @author Elliot Condon
	 * @since 1.1.3
	 * 
	 ---------------------------------------------------------------------------------------------*/
	function format_value_for_api($value, $options = null)
	{
		$value = $this->format_value_for_input($value);
		
		if($value == 'null')
		{
			return false;
		}
		
		if(is_array($value))
		{
			foreach($value as $k => $v)
			{
				$value[$k] = get_permalink($v);
			}
		}
		else
		{
			$value = get_permalink($value);
		}
		
		return $value;
	}
	
	
	/*---------------------------------------------------------------------------------------------
	 * Format Value for input
	 * - this is called from api.php
	 *
	 * @author Elliot Condon
	 * @since 1.1.3
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