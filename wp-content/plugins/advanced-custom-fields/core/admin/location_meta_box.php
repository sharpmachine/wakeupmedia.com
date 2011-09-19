<?php
	
	global $post;
		
	// get options
	$location = $this->get_acf_location($post->ID);
	if(!isset($location->rules) || empty($location->rules))
	{
		$rule = new stdClass();
		$rule->param = '';
		$rule->operator = '';
		$rule->value = '';
		
		$location->rules = array();
		$location->rules[] = $rule;
	}
	// create temp field from creating inputs
	$temp_field = new stdClass();
?>

<input type="hidden" name="location_meta_box" value="true" />
<input type="hidden" name="ei_noncename" id="ei_noncename" value="<?php echo wp_create_nonce('ei-n'); ?>" />

<table class="acf_input widefat" id="acf_location">
	<tbody>
	<tr>
		<td class="label">
			<label for="post_type">Rules</label>
			<p class="description">Create a set of rules to determine which edit screens will use these advanced custom fields</p>
		</td>
		<td>
			
			<div class="location_rules">
				<?php if($location->rules): ?>
				<table class="acf_input widefat" id="location_rules">
					<tbody>
						<?php foreach($location->rules as $k => $rule): ?>
						<tr>
						<td class="param">
							<?php 
							
							$temp_field->type = 'select';
							$temp_field->input_name = 'acf[location][rules]['.$k.'][param]';
							$temp_field->input_class = '';
							$temp_field->value = $rule->param;
							$temp_field->options = array('choices' => array(
								'post_type'		=>	'Post Type',
								'page'			=>	'Page',
								'page_type'		=>	'Page Type',
								'page_parent'	=>	'Page Parent',
								'page_template'	=>	'Page Template',
								'post'			=>	'Post',
								'post_category'	=>	'Post Category',
								'post_format'	=>	'Post Format',
								'user_type'		=>	'User Type',
							));		
							
							if(array_key_exists('options_page', $this->activated_fields))
							{
								$temp_field->options['choices']['options_page'] = "Options Page";
							}
							
							$this->create_field($temp_field); 
							
							?>
							
						</td>
						<td class="operator">
							<?php 
							
							$temp_field->type = 'select';
							$temp_field->input_name = 'acf[location][rules]['.$k.'][operator]';
							$temp_field->input_class = '';
							$temp_field->value = $rule->operator;
							$temp_field->options = array('choices' => array(
								'=='	=>	'is equal to',
								'!='	=>	'is not equal to',
							));		
							
							$this->create_field($temp_field); 
							
							?>
						</td>
						<td class="value">
							<div rel="post_type">
								<?php 
								$choices = get_post_types();
								
								unset($choices['attachment']);
								unset($choices['nav_menu_item']);
								unset($choices['revision']);
								unset($choices['acf']);
								
								$temp_field->type = 'select';
								$temp_field->input_name = 'acf[location][rules]['.$k.'][value]';
								$temp_field->input_class = '';
								$temp_field->value = $rule->value;
								$temp_field->options = array(
									'choices' => $choices, 
								);
								
								$this->create_field($temp_field); 
								
								?>
							</div>
							<div rel="page">
								<?php 
								$choices = array();
								
								foreach(get_pages('sort_column=menu_order&sort_order=desc') as $page)
								{
									if($page->post_parent != 0)
									{
										$choices[$page->ID] = '- '.$page->post_title;
									}
									else
									{
										$choices[$page->ID] = $page->post_title;
									}
									
								}
								$temp_field->options = array(
									'choices' => $choices, 
								);
								
								$this->create_field($temp_field); 
								
								?>
							</div>
							<div rel="page_type">
								<?php 
								$choices = array(
									'parent'	=>	'Parent Page',
									'child'		=>	'Child Page'
								);
								
								$temp_field->options = array(
									'choices' => $choices, 
								);
								
								$this->create_field($temp_field); 
								
								?>
							</div>
							<div rel="page_parent">
								<?php 
								$choices = array();
								foreach(get_pages('sort_column=menu_order&sort_order=desc') as $page)
								{
									if($page->post_parent != 0)
									{
										$choices[$page->ID] = '- '.$page->post_title;
									}
									else
									{
										$choices[$page->ID] = $page->post_title;
									}
									
								}
								$temp_field->options = array(
									'choices' => $choices, 
								);
								
								$this->create_field($temp_field); 
								
								?>
							</div>
							<div rel="page_template">
							
								<?php 
									
								$choices = array();
								$choices['default'] = 'Default Template';
								foreach(get_page_templates() as $k => $v)
								{
									$choices[$v] = $k;
								}
								$temp_field->options = array(
									'choices' => $choices, 
								);
								
								$this->create_field($temp_field); 
								
								?>
							</div>
							<div rel="post">
							
								<?php 
								$choices = array();
								foreach(get_posts(array('numberposts'=>'-1')) as $v)
								{
									$choices[$v->ID] = $v->post_title;
								}
								$temp_field->options = array(
									'choices' => $choices, 
								);
								
								$this->create_field($temp_field); 
								
								?>
							</div>
							<div rel="post_category">
							
								<?php 
								$choices = array();
								$category_ids = get_all_category_ids();
								
								foreach($category_ids as $cat_id) 
								{
								  $cat_name = get_cat_name($cat_id);
								  $choices[$cat_id] = $cat_name;
								}
								
								
								$temp_field->options = array(
									'choices' => $choices, 
								);
								
								$this->create_field($temp_field);
								
								?>
							</div>
							<div rel="post_format">
								<?php 
								$choices = array(
									'0'			=>	'Standard',
									'aside'		=>	'Aside',
									'link'		=>	'Link',
									'gallery'	=>	'Gallery',
									'status'	=>	'Status',
									'quote'		=>	'Quote',
									'image'		=>	'Image',
								);
								
								$temp_field->options = array(
									'choices' => $choices, 
								);
								
								$this->create_field($temp_field); 
								?>
							</div>
							<div rel="user_type">
							
								<?php 
									
								$choices = array(
									'administrator' => 'Administrator', 
									'editor' => 'Editor', 
									'author' => 'Author', 
									'contributor' => 'contributor'
								);
								
								$temp_field->options = array(
									'choices' => $choices, 
								);
								
								$this->create_field($temp_field); 
								
								?>
							</div>
							<div rel="options_page">
							
								<?php 
									
								$choices = array(
									'acf_options' => 'Options', 
								);
								
								$temp_field->options = array(
									'choices' => $choices, 
								);
								
								$this->create_field($temp_field); 
								
								?>
							</div>
						</td>
						<td class="buttons">
							<a href="javascript:;" class="remove"></a>
							<a href="javascript:;" class="add"></a>
						</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
					
				</table>
				<?php endif; ?>
				<p>match <?php 
							
							$temp_field->type = 'select';
							$temp_field->input_name = 'acf[location][allorany]';
							$temp_field->input_class = '';
							$temp_field->value = $location->allorany;
							$temp_field->options = array('choices' => array(
								'all'	=>	'all',
								'any'	=>	'any',							
							));		
							
							$this->create_field($temp_field); 
							
							?> of the above criteria</p>
			</div>
			
			
		</td>
		
	</tr>

	</tbody>
</table>
