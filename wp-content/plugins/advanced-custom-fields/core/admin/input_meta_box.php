<input type="hidden" name="ei_noncename" id="ei_noncename" value="<?php echo wp_create_nonce('ei-n'); ?>" />
<input type="hidden" name="input_meta_box" value="true" />

<div id="acf_loading"></div>
<div id="acf_fields_ajax">
<?php
	global $acf;
	global $post;
	
	// false parameter stops the function from calling die (ajax on/off)
	$acf->input_meta_box_html_no_ajax($post->ID);
?>
</div>

<script type="text/javascript">

(function($){
	
	/*--------------------------------------------------------------------------------------
	*
	*	overrides
	*
	*-------------------------------------------------------------------------------------*/
	var page_template = false;
	var page_parent = false;
	var page_type = false;
	var page = $('input#post_ID').val();
	var post = $('input#post_ID').val();
	var post_category = false;
	var post_format = false;
	
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	Change
	*
	*-------------------------------------------------------------------------------------*/

	$('#page_template').change(function(){
		
		page_template = $(this).val();
		update_fields();
	    
	});
	
	$('#parent_id').change(function(){
		
		page_parent = $(this).val();
		
		if($(this).val() != "")
		{
			page_type = 'child';
		}
		else
		{
			page_type = 'parent';
		}
		
		update_fields();
	    
	});
	
	$('#categorychecklist input[type="checkbox"]').change(function(){
		
		post_category = [];
		
		$('#categorychecklist :checked').each(function(){
			post_category.push($(this).val())
		});
		
		console.log(post_category);
				
		update_fields();
		
	});	
	
	
	$('#post-formats-select input[type="radio"]').change(function(){
		
		post_format = $(this).val();
		update_fields();
		
	});	
	
	function update_fields()
	{
		
				
		// fade out fields and show loading
		/*$('#acf_input').addClass('loading');
		$('#acf_fields_ajax').animate({
			opacity	: 0.5
		}, 500);*/
		

		// get post id
		var post_id = $('input#post_ID').val();
		
		// create data
		var data = {
			action			:	'input_meta_box_html',
			post_id			:	post_id,
			page_template	:	page_template,
			page_parent		:	page_parent,
			page_type		:	page_type,
			page			:	page,
			post			:	post,
			post_category	:	post_category,
			post_format		:	post_format,
			
		};
		//console.log(data);
	
		// post off and find new fields
		$.post(ajaxurl, data, function(data) {
			
			var new_divs = [];
			var old_divs = [];
			
			/*$('#acf_input').removeClass('loading');
			$('#acf_fields_ajax').animate({
				opacity	: 1
			}, 500);*/
			
			$('#acf_fields_ajax .acf_ajax_fields').each(function(){
				
				old_divs[$(this).attr('data-acf_id')] = $(this);
				
				$(this).remove();
			});
			
			var divs = $(data).filter(function(){ return $(this).is('.acf_ajax_fields') });
			divs.each(function(){
				
				if(old_divs[$(this).attr('data-acf_id')])
				{
					$('#acf_fields_ajax').append(old_divs[$(this).attr('data-acf_id')]);
				}
				else
				{
					$('#acf_fields_ajax').append($(this));
				}

			});
			
			
			// new dynamic style
			$('#acf_fields_ajax #acf_dynamic_style').remove();
			var style = $(data).filter(function(){ return $(this).is('style')});
			style.each(function(){
				$('#acf_fields_ajax').append($(this));
			});
			
			
			$('body').setup_acf();
				
			
		});
	}
	
	$(document).ready(function(){
		update_fields();
	});
	

})(jQuery);

</script>