<?php

class acf_Image
{
	var $name;
	var $title;
	var $parent;
	
	function acf_Image($parent)
	{
		$this->name = 'image';
		$this->title = __('Image','acf');
		$this->parent = $parent;
		
		add_action('admin_head-media-upload-popup', array($this, 'popup_head'));
		add_filter('media_send_to_editor', array($this, 'media_send_to_editor'), 15, 2 );
		//add_action('admin_init', array($this, 'admin_init'));

	}
	
	
	/*---------------------------------------------------------------------------------------------
	 * Options HTML
	 * - called from fields_meta_box.php
	 * - displays options in html format
	 *
	 * @author Elliot Condon
	 * @since 2.0.3
	 * 
	 ---------------------------------------------------------------------------------------------*/
	function options_html($key, $field)
	{
		// vars
		$options = $field->options;
		$options['save_format'] = isset($options['save_format']) ? $options['save_format'] : 'url';
		$options['preview_size'] = isset($options['preview_size']) ? $options['preview_size'] : 'thumbnail';
		
		?>
		<tr class="field_option field_option_image">
			<td class="label">
				<label><?php _e("Return Value",'acf'); ?></label>
			</td>
			<td>
				<?php 
					$temp_field = new stdClass();	
					$temp_field->type = 'select';
					$temp_field->input_name = 'acf[fields]['.$key.'][options][save_format]';
					$temp_field->input_class = '';
					$temp_field->value = $options['save_format'];
					$temp_field->options = array('choices' => array(
						'url'	=>	'Image URL',
						'id'	=>	'Attachment ID'
					));
					$this->parent->create_field($temp_field);
				?>
			</td>
		</tr>
		<tr class="field_option field_option_image">
			<td class="label">
				<label><?php _e("Preview Size",'acf'); ?></label>
			</td>
			<td>
				<?php 
					$temp_field->type = 'select';
					$temp_field->input_name = 'acf[fields]['.$key.'][options][preview_size]';
					$temp_field->input_class = '';
					$temp_field->value = $options['preview_size'];
					$temp_field->options = array('choices' => array(
						'thumbnail'	=>	'Thumbnail',
						'medium'	=>	'Medium',
						'large'		=>	'Large',
						'full'		=>	'Full'
					));
					$this->parent->create_field($temp_field);
				?>
			</td>
		</tr>

		<?php
	}


	 
	/*---------------------------------------------------------------------------------------------
	 * popup_head - STYLES MEDIA THICKBOX
	 *
	 * @author Elliot Condon
	 * @since 1.1.4
	 * 
	 ---------------------------------------------------------------------------------------------*/
	function popup_head()
	{
		if(isset($_GET["acf_type"]) && $_GET['acf_type'] == 'image')
		{
			$preview_size = isset($arr_postinfo['preview_size']) ? $arr_postinfo['preview_size'] : 'medium';
			
			?>
			<style type="text/css">
				#media-upload-header #sidemenu li#tab-type_url,
				#media-upload-header #sidemenu li#tab-gallery {
					display: none;
				}
				
				#media-items tr.url,
				#media-items tr.align,
				#media-items tr.image_alt,
				#media-items tr.image-size,
				#media-items tr.post_excerpt,
				#media-items tr.post_content,
				#media-items tr.image_alt p,
				#media-items table thead input.button,
				#media-items table thead img.imgedit-wait-spin,
				#media-items tr.submit a.wp-post-thumbnail {
					display: none;
				} 

				.media-item table thead img {
					border: #DFDFDF solid 1px; 
					margin-right: 10px;
				}

			</style>
			<script type="text/javascript">
			(function($){
			
				$(document).ready(function(){
				
					$('#media-items').bind('DOMNodeInserted',function(){
						$('input[value="Insert into Post"]').each(function(){
							$(this).attr('value','<?php _e("Select Image",'acf'); ?>');
						});
					}).trigger('DOMNodeInserted');
					
					$('form#filter').each(function(){
						
						$(this).append('<input type="hidden" name="acf_preview_size" value="<?php echo $preview_size; ?>" />');
						$(this).append('<input type="hidden" name="acf_type" value="image" />');
						
					});
				});
							
			})(jQuery);
			</script>
			<?php
		}
	}
	
	
	/*---------------------------------------------------------------------------------------------
	 * media_send_to_editor - SEND IMAGE TO ACF DIV
	 *
	 * @author Elliot Condon
	 * @since 1.1.4
	 * 
	 ---------------------------------------------------------------------------------------------*/
	function media_send_to_editor($html, $id)
	{
		parse_str($_POST["_wp_http_referer"], $arr_postinfo);
		
		if(isset($arr_postinfo["acf_type"]) && $arr_postinfo["acf_type"] == "image")
		{
			
			$preview_size = isset($arr_postinfo['acf_preview_size']) ? $arr_postinfo['acf_preview_size'] : 'medium';
			
			$file_src = wp_get_attachment_image_src($id, $preview_size);
			$file_src = $file_src[0];
		
			?>
			<script type="text/javascript">
				
				self.parent.acf_div.find('input.value').val('<?php echo $id; ?>');
			 	self.parent.acf_div.find('img').attr('src','<?php echo $file_src; ?>');
			 	self.parent.acf_div.addClass('active');
			 	
			 	// reset acf_div and return false
			 	self.parent.acf_div = null;
			 	self.parent.tb_remove();
				
			</script>
			<?php
			exit;
		} 
		else 
		{
			return $html;
		}

	}
	
	
	function html($field)
	{
		
		$class = "";
		$file_src = "";
		$preview_size = isset($field->options['preview_size']) ? $field->options['preview_size'] : 'medium';
		
		if($field->value != '' && is_numeric($field->value))
		{
			$file_src = wp_get_attachment_image_src($field->value, $preview_size);
			$file_src = $file_src[0];
			
			if($file_src)
			{
				$class = " active";
			}
		}


		echo '<div class="acf_image_uploader'.$class.'" data-preview_size="' . $preview_size . '">';
			echo '<a href="#" class="remove_image"></a>';
			echo '<img src="'.$file_src.'" alt=""/>';	
			echo '<input class="value" type="hidden" name="'.$field->input_name.'" value="'.$field->value.'" />';
			echo '<p>'.__('No image selected','acf').'. <input type="button" class="button" value="'.__('Add Image','acf').'" /></p>';
		echo '</div>';

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
		
		$format = isset($options['save_format']) ? $options['save_format'] : 'url';
		
		if($format == 'url')
		{
			$value = wp_get_attachment_url($value);
		}
		
		return $value;
		
	}
		
}

?>