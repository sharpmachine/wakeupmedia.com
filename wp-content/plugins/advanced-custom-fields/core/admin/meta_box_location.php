<?php

// vars
global $post;
		
// get location data
$location = $this->get_acf_location($post->ID);

if(empty($location['rules']))
{
	$location['rules'] = array(
		array(
			'param'		=>	'',
			'operator'	=>	'',
			'value'		=>	'',
		)
	);
}

?>
<table class="acf_input widefat" id="acf_location">
	<tbody>
	<tr>
		<td class="label">
			<label for="post_type"><?php _e("Rules",'acf'); ?></label>
			<p class="description"><?php _e("Create a set of rules to determine which edit screens will use these advanced custom fields",'acf'); ?></p>
		</td>
		<td>
			<div class="location_rules">
				<table class="acf_input widefat" id="location_rules">
					<tbody>
						<?php foreach($location['rules'] as $k => $rule): ?>
						<tr>
						<td class="param">
							<?php 
							$args = array(
								'type'	=>	'select',
								'name'	=>	'location[rules]['.$k.'][param]',
								'value'	=>	$rule['param'],
								'choices' => array(
									'post_type'		=>	'Post Type',
									'page'			=>	'Page',
									'page_type'		=>	'Page Type',
									'page_parent'	=>	'Page Parent',
									'page_template'	=>	'Page Template',
									'post'			=>	'Post',
									'post_category'	=>	'Post Category',
									'post_format'	=>	'Post Format',
									'user_type'		=>	'User Type',
									'taxonomy'		=>	'Taxonomy'
								)
							);
							
							// validate
							if($this->is_field_unlocked('options_page'))
							{
								$args['choices']['options_page'] = "Options Page";
							}
							
							$this->create_field($args);							
							?>
						</td>
						<td class="operator">
							<?php 	
							$this->create_field(array(
								'type'	=>	'select',
								'name'	=>	'location[rules]['.$k.'][operator]',
								'value'	=>	$rule['operator'],
								'choices' => array(
									'=='	=>	'is equal to',
									'!='	=>	'is not equal to',
								)
							)); 	
							?>
						</td>
						<td class="value">
							<div rel="post_type">
								<?php 
								$choices = get_post_types(array('public' => true));
								unset($choices['attachment']);

								
								$this->create_field(array(
									'type'	=>	'select',
									'name'	=>	'location[rules]['.$k.'][value]',
									'value'	=>	$rule['value'],
									'choices' => $choices,
								));
								?>
							</div>
							<div rel="page">
								<?php 
								$choices = array();
								
								foreach(get_pages('sort_column=menu_order&sort_order=desc') as $page)
								{
									$value = '';
									$ancestors = get_ancestors($page->ID, 'page');
									if($ancestors)
									{
										foreach($ancestors as $a)
										{
											$value .= '– ';
										}
									}
									$value .= get_the_title($page->ID);
									
									$choices[$page->ID] = $value;
									
								}
								
								$this->create_field(array(
									'type'	=>	'select',
									'name'	=>	'location[rules]['.$k.'][value]',
									'value'	=>	$rule['value'],
									'choices' => $choices,
								));
																
								?>
							</div>
							<div rel="page_type">
								<?php 
								$this->create_field(array(
									'type'	=>	'select',
									'name'	=>	'location[rules]['.$k.'][value]',
									'value'	=>	$rule['value'],
									'choices' => array(
										'parent'	=>	'Parent Page',
										'child'		=>	'Child Page'
									),
								));
								?>
							</div>
							<div rel="page_parent">
								<?php 
								
								$choices = array();
								
								foreach(get_pages('sort_column=menu_order&sort_order=desc') as $page)
								{
									$value = '';
									$ancestors = get_ancestors($page->ID, 'page');
									if($ancestors)
									{
										foreach($ancestors as $a)
										{
											$value .= '– ';
										}
									}
									$value .= get_the_title($page->ID);
									
									$choices[$page->ID] = $value;
									
								}
								
								$this->create_field(array(
									'type'	=>	'select',
									'name'	=>	'location[rules]['.$k.'][value]',
									'value'	=>	$rule['value'],
									'choices' => $choices,
								));
																
								?>
							</div>
							<div rel="page_template">
								<?php 
									
								$choices = array(
									'default'	=>	'Default Template',
								);
								foreach(get_page_templates() as $tk => $tv)
								{
									$choices[$tv] = $tk;
								}
								
								$this->create_field(array(
									'type'	=>	'select',
									'name'	=>	'location[rules]['.$k.'][value]',
									'value'	=>	$rule['value'],
									'choices' => $choices,
								));
								
								?>
							</div>
							<div rel="post">
								<?php 
								
								$choices = array();
								foreach(get_posts(array('numberposts'=>'-1')) as $v)
								{
									$choices[$v->ID] = $v->post_title;
								}
								
								$this->create_field(array(
									'type'	=>	'select',
									'name'	=>	'location[rules]['.$k.'][value]',
									'value'	=>	$rule['value'],
									'choices' => $choices,
								));
								
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
								
								$this->create_field(array(
									'type'	=>	'select',
									'name'	=>	'location[rules]['.$k.'][value]',
									'value'	=>	$rule['value'],
									'choices' => $choices,
								));
								
								?>
							</div>
							<div rel="post_format">
								<?php 
								
								$this->create_field(array(
									'type'	=>	'select',
									'name'	=>	'location[rules]['.$k.'][value]',
									'value'	=>	$rule['value'],
									'choices' => array(
										'0'			=>	'Standard',
										'aside'		=>	'Aside',
										'link'		=>	'Link',
										'gallery'	=>	'Gallery',
										'status'	=>	'Status',
										'quote'		=>	'Quote',
										'image'		=>	'Image',
									),
								));
								
								?>
							</div>
							<div rel="user_type">
								<?php 
								
								$this->create_field(array(
									'type'	=>	'select',
									'name'	=>	'location[rules]['.$k.'][value]',
									'value'	=>	$rule['value'],
									'choices' => array(
										'administrator' => 'Administrator', 
										'editor' => 'Editor', 
										'author' => 'Author', 
										'contributor' => 'contributor'
									)
								));
								
								?>
							</div>
							<div rel="options_page">
								<?php 
								
								$this->create_field(array(
									'type'	=>	'select',
									'name'	=>	'location[rules]['.$k.'][value]',
									'value'	=>	$rule['value'],
									'choices' => array(
										'Options' => 'Options', 
									),
								));
								
								?>
							</div>
							<div rel="taxonomy">
							
								<?php 
									
								$post_types = get_post_types();
								
								//unset($post_types['attachment']);
								//unset($post_types['nav_menu_item']);
								//unset($post_types['revision']);
								//unset($post_types['acf']);
								
								$choices = array();
								
								if($post_types)
								{
									foreach($post_types as $post_type)
									{
										$taxonomies = get_object_taxonomies($post_type);
										if($taxonomies)
										{
											foreach($taxonomies as $taxonomy)
											{
												$terms = get_terms($taxonomy, array('hide_empty' => false));
												if($terms)
												{
													foreach($terms as $term)
													{
														$choices[$post_type . ': ' . $taxonomy][$term->term_id] = $term->name; 
													}
												}
											}
										}
									}
								}
								
								$this->create_field(array(
									'type'	=>	'select',
									'name'	=>	'location[rules]['.$k.'][value]',
									'value'	=>	$rule['value'],
									'choices' => $choices,
									'optgroup' => true,
								));

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
				<p>match <?php $this->create_field(array(
									'type'	=>	'select',
									'name'	=>	'location[allorany]',
									'value'	=>	$location['allorany'],
									'choices' => array(
										'all'	=>	'all',
										'any'	=>	'any',							
									),
								)); ?> of the above</p>
			</div>
			
			
		</td>
		
	</tr>

	</tbody>
</table>