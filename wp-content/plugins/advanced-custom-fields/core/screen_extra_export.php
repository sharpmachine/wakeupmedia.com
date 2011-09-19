<div id="screen-meta-export-acf-wrap" class="screen-meta-wrap hidden acf">
	<div class="screen-meta-content">
		<form enctype="multipart/form-data" method="post" >
			<h5><?php _e("Import",'acf'); ?></h5>
		
			<p><?php _e("Have an ACF export file? Import it here.",'acf'); ?></p>
		
			<input type="file" id="file" name="import" />
			<input type="submit" class="button" name="acf_import" value="Import" />

			
			<p><br /></p>
			<h5><?php _e("Export",'acf'); ?></h5>
			<p><?php _e("Want to create an ACF export file? Just select the desired ACF's and hit Export",'acf'); ?></p>
				
			<?php
			
			$acfs = get_pages(array(
				'numberposts' 	=> 	-1,
				'post_type'		=>	'acf',
				'sort_column' 	=>	'menu_order',
			));
			
			// blank array to hold acfs
			$acf_objects = array();
			
			if($acfs)
			{
				foreach($acfs as $acf)
				{
					$acf_objects[$acf->ID] = $acf->post_title;
				}
			}
			
			$temp_field = new stdClass();
			
			$temp_field->type = 'select';
			$temp_field->input_name = 'acf_objects';
			$temp_field->input_class = '';
			$temp_field->value = '';
			$temp_field->options = array('choices' => $acf_objects, 'multiple' => 1);
			
			$this->create_field($temp_field); 
			
			?>
			<input type="submit" name="acf_export" value="Export" />
			
		</form>
		
	</div>
</div>
<div id="screen-meta-export-acf-link-wrap" class="hide-if-no-js screen-meta-toggle acf">
	<a href="#screen-meta-export-acf" id="screen-meta-export-acf-link" class="show-settings"><?php _e("Import / Export",'acf'); ?></a>
</div>