<?php

class acf_Relationship
{
	var $name;
	var $title;
	var $parent;
	
	function acf_Relationship($parent)
	{
		$this->name = 'relationship';
		$this->title = __("Relationship",'acf');
		$this->parent = $parent;
	}
	
	function html($field)
	{
		$options = $field->options;
		//$options['min'] = isset($options['min']) ? $options['min'] : '0';
		$options['max'] = isset($options['max']) ? $options['max'] : '-1';
		$options['meta_key'] = isset($options['meta_key']) ? $options['meta_key'] : '';
		$options['meta_value'] = isset($options['meta_value']) ? $options['meta_value'] : '';
		
		// get post types
		$post_types = isset($options['post_type']) ? $options['post_type'] : false;
		if(!$post_types || $post_types[0] == "")
		{
			
			$post_types = get_post_types(array('public' => true));
			foreach($post_types as $key => $value)
			{
				if($value == 'attachment')
				{
					unset($post_types[$key]);
				}
			}
		}
		
		
		// get posts for list
		$posts = get_posts(array(
			'numberposts' 	=> 	-1,
			'post_type'		=>	$post_types,
			'orderby'		=>	'title',
			'order'			=>	'ASC',
			'meta_key'		=>	$options['meta_key'],
			'meta_value'	=>	$options['meta_value'],
		));
		

		$values_array = array();
		if($field->value != "")
		{
			$values_array = explode(',', $field->value);
		}
		
		?>
		<div class="acf_relationship" data-max="<?php echo $options['max']; ?>">
			
			<input type="hidden" name="<?php echo $field->input_name; ?>" value="<?php echo $field->value; ?>" />
			
			<div class="relationship_left">
				<table class="widefat">
					<thead>
						<tr>
							<th>
								<label class="relationship_label" for="relationship_<?php echo $field->input_name; ?>">Search...</label>
								<input class="relationship_search" type="text" id="relationship_<?php echo $field->input_name; ?>" />
								<div class="clear_relationship_search"></div>
							</th>
						</tr>
					</thead>
				</table>
				<div class="relationship_list">
				<?php
				if($posts)
				{
					foreach($posts as $post)
					{
						if(!get_the_title($post->ID)) continue;
						
						$class = in_array($post->ID, $values_array) ? 'hide' : '';
						echo '<a href="javascript:;" class="' . $class . '" data-post_id="' . $post->ID . '">' . get_the_title($post->ID) . '<span class="add"></span></a>';
					}
				}
				?>
				</div>
			</div>
			
			<div class="relationship_right">
				<div class="relationship_list">
				<?php
				$temp_posts = array();
				
				if($posts)
				{
					foreach($posts as $post)
					{
						$temp_posts[$post->ID] = $post;
					}
				}
				
				if($temp_posts)
				{
					foreach($values_array as $value)
					{
						echo '<a href="javascript:;" class="" data-post_id="' . $temp_posts[$value]->ID . '">' . get_the_title($temp_posts[$value]->ID) . '<span class="remove"></span></a>';
						unset($temp_posts[$value]);
					}
					
					foreach($temp_posts as $id => $post)
					{
						echo '<a href="javascript:;" class="hide" data-post_id="' . $post->ID . '">' . get_the_title($post->ID) . '<span class="remove"></span></a>';
					}
				}
					
				?>
				</div>
			</div>
			
			
		</div>
		<?php

	
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
		$options['max'] = isset($options['max']) ? $options['max'] : '-1';
		$options['meta_key'] = isset($options['meta_key']) ? $options['meta_key'] : '';
		$options['meta_value'] = isset($options['meta_value']) ? $options['meta_value'] : '';
		
		?>
		
		<tr class="field_option field_option_relationship">
			<td class="label">
				<label for=""><?php _e("Post Type",'acf'); ?></label>
				<p class="description"><?php _e("Filter posts by selecting a post type",'acf'); ?></p>
			</td>
			<td>
				<?php 
				$post_types = array('' => '- All -');
				
				foreach (get_post_types() as $post_type ) {
				  $post_types[$post_type] = $post_type;
				}
				
				unset($post_types['attachment']);
				unset($post_types['nav_menu_item']);
				unset($post_types['revision']);
				unset($post_types['acf']);
				

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
		<tr class="field_option field_option_relationship">
			<td class="label">
				<label><?php _e("Filter Posts",'acf'); ?></label>
				<p class="description"><?php _e("Where meta_key == meta_value",'acf'); ?></p>
			</td>
			<td>
				<div style="width:45%; float:left">
				<?php 
					$temp_field->type = 'text';
					$temp_field->input_name = 'acf[fields]['.$key.'][options][meta_key]';
					$temp_field->input_class = '';
					$temp_field->value = $options['meta_key'];
					$this->parent->create_field($temp_field); 
				?>
				</div>
				<div style="width:10%; float:left; text-align:center; padding:5px 0 0;">is equal to</div>
				<div style="width:45%; float:left">
				<?php 
					$temp_field->type = 'text';
					$temp_field->input_name = 'acf[fields]['.$key.'][options][meta_value]';
					$temp_field->input_class = '';
					$temp_field->value = $options['meta_value'];
					$this->parent->create_field($temp_field); 
				?>
				</div>
			</td>
		</tr>
		<tr class="field_option field_option_relationship">
			<td class="label">
				<label><?php _e("Maximum posts",'acf'); ?></label>
				<p class="description"><?php _e("Set to -1 for inifinit",'acf'); ?></p>
			</td>
			<td>
				<?php 
					$temp_field->type = 'text';
					$temp_field->input_name = 'acf[fields]['.$key.'][options][max]';
					$temp_field->input_class = '';
					$temp_field->value = $options['max'];
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
	 * @since 1.1
	 * 
	 ---------------------------------------------------------------------------------------------*/
	function format_value_for_api($value, $options = null)
	{
		$return = false;
		
		if(!$value || $value == "")
		{
			return $return;
		}
		
		$value = explode(',', $value);
		
		if(is_array($value))
		{
			$return = array();
			foreach($value as $v)
			{
				$return[$v] = get_post($v);
			}
		}
		else
		{
			$return = array(get_post($value));
		}
		
		return $return;
	}
	

	
}

?>