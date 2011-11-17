window.acf_div = null;
	
(function($){
	
	/*----------------------------------------------------------------------
	*
	*	vars
	*
	*---------------------------------------------------------------------*/
	var shift_is_down = false;
	
	/*----------------------------------------------------------------------
	*
	*	Exists
	*
	*---------------------------------------------------------------------*/
	
	$.fn.exists = function()
	{
		return $(this).length>0;
	};
	
	
	
	/*----------------------------------------------------------------------
	*
	*	WYSIWYG
	*
	*---------------------------------------------------------------------*/
	var wysiwyg_count = 0;
	
	$.fn.setup_wysiwyg = function()
	{
		
		$(this).find('.acf_wysiwyg').each(function(){
			
			var tiny_1_old = '';
			var tiny_2_old = '';
			
			// setup extra tinymce buttons
			if(tinyMCE.settings.theme_advanced_buttons1)
			{
				tiny_1_old = tinyMCE.settings.theme_advanced_buttons1;
				tinyMCE.settings.theme_advanced_buttons1 += ",|,add_image,add_video,add_audio,add_media";
			}
			
			if(tinyMCE.settings.theme_advanced_buttons2)
			{
				tiny_2_old = tinyMCE.settings.theme_advanced_buttons2;
				tinyMCE.settings.theme_advanced_buttons2 += ",code";
			}
			
			
			
			if($(this).find('table').exists())
			{
				//alert('had wysiwyg')
				$(this).children('#editorcontainer').children('span').remove();
				$(this).children('#editorcontainer').children('textarea').removeAttr('aria-hidden').removeAttr('style');
			}
			
			// get a unique id
			wysiwyg_count = wysiwyg_count + 1;
			
			// add id
			var id = 'acf_wysiwyg_'+wysiwyg_count;
			$(this).find('textarea').attr('id',id);
			
			// create wysiwyg
			tinyMCE.execCommand('mceAddControl', false, id);
			
			
			// restore old tinymce buttons
			if(tinyMCE.settings.theme_advanced_buttons1)
			{
				tinyMCE.settings.theme_advanced_buttons1 = tiny_1_old;
			}
			
			if(tinyMCE.settings.theme_advanced_buttons2)
			{
				tinyMCE.settings.theme_advanced_buttons2 = tiny_2_old;
			}
			
		});
	
	};
	
	
	/*----------------------------------------------------------------------
	*
	*	Relationship
	*
	*---------------------------------------------------------------------*/
	
	$.fn.setup_relationship = function()
	{
		$(this).find('.acf_relationship').each(function(){
		
			//console.log('setup');
		
			var div = $(this);
			//var min = parseInt(div.attr('data-min'));
			var max = parseInt(div.attr('data-max')); if(max == -1){max = 9999;}
			var input = div.children('input');
			var left = div.find('.relationship_left .relationship_list');
			var right = div.find('.relationship_right .relationship_list');
			var search = div.find('input.relationship_search');

			
			left.find('a').unbind('click').click(function(){
				
				if(right.find('a:not(.hide)').length >= max)
				{
					alert('Maximum values reached ( ' + max + ' values )');
					return false;
				}

				var id = $(this).attr('data-post_id');
				
				$(this).addClass('hide');
				right.find('a[data-post_id="' + id + '"]').removeClass('hide').appendTo(right);
				
				update_input_val();
				
				return false;
				
			});
			
			
			right.find('a').unbind('click').click(function(){
				
				var id = $(this).attr('data-post_id');
				
				$(this).addClass('hide');
				left.find('a[data-post_id="' + id + '"]').removeClass('hide');
				
				update_input_val();
				
				return false;
				
			});
			
			
			right.unbind('sortable').sortable({
				axis: "y", // limit the dragging to up/down only
			    start: function(event, ui)
			    {
					ui.item.addClass('sortable_active');
			    },
			    stop: function(event, ui)
			    {
			    	ui.item.removeClass('sortable_active');
			    	update_input_val();
			    }
			});
			
			
			$.expr[':'].Contains = function(a,i,m){
		    	return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())>=0;
			};
			
			search.val('');
			search.change(function ()
			{
				var filter = $(this).val();
			    if(filter)
			    {
					left.find("a:not(:Contains(" + filter + "))").addClass('filter_hide');
			        left.find("a:Contains(" + filter + "):not(.hide)").removeClass('filter_hide');
			    }
			    else
			    {
			    	left.find("a:not(.hide)").removeClass('filter_hide');
			    }
		
			    return false;
			    
			})
			.keyup( function () {
			    $(this).change();
			});
			
			search.focus(function(){
				$(this).siblings('label').hide();
			})
			.blur(function(){
				if($(this).val() == "")
				{
					$(this).siblings('label').show();
				}
			});
			
			
			function update_input_val()
			{
				var value = new Array();
				right.find('a:not(.hide)').each(function(){
					value.push($(this).attr('data-post_id'));
				});
				input.val(value.join(','));
			}
			
		});
	};
	
	
	/*----------------------------------------------------------------------
	*
	*	Datepicker
	*
	*---------------------------------------------------------------------*/
	
	$.fn.setup_datepicker = function()
	{
		$(this).find('.acf_datepicker').each(function(){
		
			var format = $(this).attr('data-date_format') ? $(this).attr('data-date_format') : 'dd/mm/yy';
			
			$(this).datepicker({ 
				dateFormat: format 
			});
			
			$('#ui-datepicker-div').wrap('<div class="acf_datepicker" />');
		
		});
	};
	
	
	/*----------------------------------------------------------------------
	*
	*	Image
	*
	*---------------------------------------------------------------------*/
	
	$.fn.setup_image = function(){
		
		var post_id = $('input#post_ID').val();
		
		$(this).find('.acf_image_uploader').each(function(){
			
			var div = $(this);
			var preview_size = div.attr('data-preview_size');
			
			div.find('input.button').unbind('click').click(function(){
			
				// set global var
				window.acf_div = div;
				
				// show the thickbox
				tb_show('Add Image to field', 'media-upload.php?post_id=' + post_id + '&type=image&acf_type=image&acf_preview_size=' + preview_size + 'TB_iframe=1');
				
				return false;
			});
			
			
			div.find('a.remove_image').unbind('click').click(function()
			{
				div.find('input.value').val('');
				div.removeClass('active');
			
				return false;
			});
			
		});
		
	};
	
	
	/*----------------------------------------------------------------------
	*
	*	File
	*
	*---------------------------------------------------------------------*/
	
	$.fn.setup_file = function(){
		
		var post_id = $('input#post_ID').val();
		
		$(this).find('.acf_file_uploader').each(function(){
			
			//console.log('file setup');
			var div = $(this);
	
			div.find('p.no_file input.button').click(function(){
				
				// set global var
				window.acf_div = div;
				
				// show the thickbox
				tb_show('Add File to field', 'media-upload.php?post_id='+post_id+'&type=file&acf_type=file&TB_iframe=1');
				
				return false;
			});
			
			
			div.find('p.file input.button').unbind('click').click(function()
			{
				div.find('input.value').val('');
				div.removeClass('active');
			
				return false;
			});
		
		});
	
	};

	
	/*----------------------------------------------------------------------
	*
	*	Repeater
	*
	*---------------------------------------------------------------------*/
	
	$.fn.setup_repeater = function(){
		
		$(this).find('.repeater').each(function(){
		
			var r = $(this);
			var row_limit = parseInt(r.attr('data-row_limit'));
			var row_count = r.children('table').children('tbody').children('tr').length;
			
			// has limit been reached?
			if(row_count >= row_limit)
			{
				r.find('#add_field').attr('disabled','true');
				//return false;
			}
			
			// sortable
			if(row_limit > 1){
				r.make_sortable();
			}
			
			if(row_count == 1)
			{
				r.addClass('hide_remove_buttons');
			}
		});
		

		// add field
		$('.repeater #add_field').die('click');
		$('.repeater #add_field').live('click', function(){
			
			var r = $(this).closest('.repeater');
			var row_limit = parseInt(r.attr('data-row_limit'));			
			var row_count = r.children('table').children('tbody').children('tr').length;
			
			// row limit
			if(row_count >= row_limit)
			{
				// reached row limit!
				r.find('#add_field').attr('disabled','true');
				return false;
			}
			
			// create and add the new field
			var new_field = r.children('table').children('tbody').children('tr:last-child').clone(false);
			r.children('table').children('tbody').append(new_field); 
			
			// update names
			new_field.find('[name]').each(function(){
			
				var name = $(this).attr('name').replace('[value]['+(row_count-1)+']','[value]['+(row_count)+']');
				$(this).attr('name', name);
				$(this).attr('id', name);
				
				if(name.indexOf("[value_id]") != -1 || name.indexOf("[meta_id]") != -1)
				{
					$(this).val('');
				}
				
			});
			
			// reset values
			if(!shift_is_down)
			{
				new_field.reset_values();
			}
			
			// setup sub fields
			new_field.setup_wysiwyg();
			new_field.setup_relationship();
			new_field.setup_datepicker();
			new_field.setup_image();
			new_field.setup_file();
			
			r.update_order_numbers();
			
			// there is now 1 more row
			row_count ++;
			
			// hide remove buttons if only 1 field
			if(row_count > 1)
			{
				r.removeClass('hide_remove_buttons');
			}
			
			// disable the add field button if row limit is reached
			if((row_count+1) >= row_limit)
			{
				r.find('#add_field').attr('disabled','true');
			}
			
			return false;
			
		});
		
		
		// remove field
		$('.repeater a.remove_field').die('click');
		$('.repeater a.remove_field').live('click', function(){
			
			var r = $(this).closest('.repeater');
			var row_count = r.children('table').children('tbody').children('tr').length;
						
			// needs at least one
			if(row_count <= 1)
			{
				return false;
			}
			else if(row_count == 2)
			{
				// total fields will be 1 after the tr is removed
				r.addClass('hide_remove_buttons');
			}
			
			var tr = $(this).closest('tr');
			
			tr.find('td').animate({'opacity':'0', 'height' : '0px'}, 300,function(){
				tr.remove();
				r.update_order_numbers();
			});
			
			
			r.find('#add_field').removeAttr('disabled');
			
			return false;
			
		});
		
		
	};
	
	
	/*----------------------------------------------------------------------
	*
	*	Update Order Numbers
	*
	*---------------------------------------------------------------------*/

	$.fn.update_order_numbers = function(){
		
		$(this).children('table').children('tbody').children('tr').each(function(i){
			$(this).children('td.order').html(i+1);
		});
	
	};
	
	
	/*----------------------------------------------------------------------
	*
	*	Sortable
	*
	*---------------------------------------------------------------------*/
	$.fn.make_sortable = function(){
		
		var r = $(this);
		
		var fixHelper = function(e, ui) {
			ui.children().each(function() {
				$(this).width($(this).width());
			});
			return ui;
		};
		
		r.children('table').children('tbody').unbind('sortable').sortable({
			update: function(event, ui){
				r.update_order_numbers();
				r.setup_wysiwyg();
				r.setup_relationship();
				r.setup_datepicker();
				r.setup_image();
				r.setup_file();
			},
			handle: 'td.order',
			helper: fixHelper,
		    start: function(event, ui)
		    {

		    },
		    stop: function(event, ui)
		    {
		    	ui.item.setup_wysiwyg();
		    }
		});
	};
	

	
	
	/*----------------------------------------------------------------------
	*
	*	reset_values
	*
	*---------------------------------------------------------------------*/
	
	$.fn.reset_values = function(){
		
		var div = $(this);
		
		
		// wysiwyg
		if(div.find('.acf_wysiwyg').exists())
		{
			div.find('.acf_wysiwyg').each(function(){
			
				var name = $(this).find('textarea').first().attr('name');
				$(this).html('<div id="editorcontainer"><textarea name="'+name+'"></textarea></div>');
				
			});
			
		}
		
		
		// relationship
		if(div.find('.acf_relationship').exists())
		{
			div.find('.acf_relationship').each(function(){
			
				$(this).find('.relationship_left .relationship_list a').each(function(){
					$(this).removeClass('hide');
				});
				
				$(this).find('.relationship_right .relationship_list a').each(function(){
					$(this).addClass('hide');
				});
				
			});
		}
		
		
		// image upload
		div.find('.acf_image_uploader').each(function(){
			$(this).removeClass('active');
		});
		
		
		// file upload
		div.find('.acf_file_uploader').each(function(){
			$(this).removeClass('active');
		});
		
		
		// date picker
		div.find('.acf_datepicker').each(function(){
			$(this).removeClass('hasDatepicker');
		});
		
		

		// reset all values
		$(this).find('[name]').each(function()
		{
			var name = $(this).attr('name');
				
			if(name.indexOf("[field_id]") != -1)
			{
				// do nothing, we want to keep this hidden field with it's current values
			}
			else if(name.indexOf("[field_type]") != -1)
			{
				// do nothing, we want to keep this hidden field with it's current values
			}
			else if(name.indexOf("date_format") != -1)
			{
				// do nothing, we want to keep this hidden field with it's current values
			}
			else
			{
				$(this).val('');
			}
	
			// selected / ticked
			if($(this).is(':selected'))
			{
				$(this).removeAttr('selected');
				
			}
			else if($(this).is(':checked'))
			{
				$(this).removeAttr('checked');
			}			
			
		});
		
		
	};
	
	
	/*----------------------------------------------------------------------
	*
	*	Setup Validation
	*
	*---------------------------------------------------------------------*/
	
	$.fn.setup_validation = function()
	{
		/*var div = $(this);
		
		$('#publish').click(function(){
			
			
			// relationship
			div.find('.acf_relationship').each(function(){
				
				var r = $(this);
				var min = parseInt(r.attr('data-min'));
				
				if(r.find('.relationship_right .relationship_list a:not(.hide)').length < min)
				{
					alert('Minimum values not reached ( ' + max + ' values )');
					return false;
				}
				
			});
		
			console.log('setup');
		
			var div = $(this);
			
			
			
		});*/
	}
	
	/*----------------------------------------------------------------------
	*
	*	Setup ACF
	*
	*---------------------------------------------------------------------*/
	
	$.fn.setup_acf = function()
	{

		var div = $('#acf_fields_ajax');
		
		
		div.setup_wysiwyg();
		div.setup_relationship();
		div.setup_datepicker();
		div.setup_image();
		div.setup_file();
		div.setup_repeater();
		div.setup_validation();
		
	};

	

	/*----------------------------------------------------------------------
	*
	*	Document Ready
	*
	*---------------------------------------------------------------------*/
	
	$(document).ready(function(){
		
		// add shift key trigger for duplicating repeater field
		
		$(window).keydown(function(evt){
			if(evt.which == 16)
			{
		    	shift_is_down = true;
		 	}
		}).keyup(function(evt) {
		  if(evt.which == 16)
			{
		    	shift_is_down = false;
		 	}
		});

	
	});
	
	
	
})(jQuery);
