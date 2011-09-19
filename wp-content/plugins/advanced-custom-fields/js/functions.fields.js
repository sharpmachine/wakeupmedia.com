(function($){

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
	*	Update Names
	*
	*---------------------------------------------------------------------*/

	$.fn.update_names = function(new_no, new_sub_no)
	{
		
		//alert('passed through '+total_fields);
		$(this).find('[name]').each(function()
		{	
			
			var name = $(this).attr('name');
			var id = $(this).attr('id');

			if(name && name.indexOf("[fields][999]") != -1)
			{
				name = name.replace('[fields][999]','[fields]['+new_no+']');
			}
			if(id && id.indexOf("[fields][999]") != -1)
			{
				id = id.replace('[fields][999]','[fields]['+new_no+']');
			}
			
			if($(this).closest('.sub_field').hasClass('field_clone'))
			{
				// dont change this input (its the clone sub field!)
			}
			else
			{
				if(name && name.indexOf("[sub_fields][999]") != -1)
				{
					name = name.replace('[sub_fields][999]','[sub_fields]['+new_sub_no+']');
				}
				if(id && id.indexOf("[sub_fields][999]") != -1)
				{
					id = id.replace('[sub_fields][999]','[sub_fields]['+new_sub_no+']');
				}
			}
			
			$(this).attr('name', name);
			$(this).attr('id', id);
		});
	}
	
	
	/*----------------------------------------------------------------------
	*
	*	Update Order Numbers
	*
	*---------------------------------------------------------------------*/
	
	function update_order_numbers(){
		
		$('#acf_fields .fields').each(function(){
			$(this).children('.field').each(function(i){
				$(this).find('td.field_order .circle').first().html(i+1);
			});
		});

	}
	
	
	/*----------------------------------------------------------------------
	*
	*	setup_fields
	*
	*---------------------------------------------------------------------*/
	
	function setup_fields()
	{

		// add edit button functionality
		$('#acf_fields a.acf_edit_field').live('click', function(){

			var field = $(this).closest('.field');
			
			if(field.hasClass('form_open'))
			{
				field.removeClass('form_open');
			}
			else
			{
				field.addClass('form_open');
			}
			
			field.children('.field_form_mask').animate({'height':'toggle'}, 500);

		});
		
		
		// add delete button functionality
		$('#acf_fields a.acf_delete_field').live('click', function(){

			var field = $(this).closest('.field');
			var fields = field.closest('.fields');
			
			field.remove();
			update_order_numbers();
			
			if(!fields.children('.field').exists())
			{
				// no more fields, show the message
				fields.children('.no_fields_message').show();
			}

		});
		
		
		// show field type options
		$('#acf_fields tr.field_type select').live('change', function(){
			
			var tbody = $(this).closest('tbody');
			var type = $(this).val();
			
			// does it have repeater?
			if(!$(this).find('option[value="repeater"]').exists() && !$(this).find('option[value="null"]').exists())
			{
				if($(this).closest('.repeater').length == 0)
				{
					$(this).append('<option value="null" disabled="true">Repeater (Unlock field with activation code)</option>');
				}
			}
			
			tbody.children('tr.field_option').hide();
			tbody.children('tr.field_option').find('[name]').attr('disabled', 'true');
			
			var tr = tbody.children('tr.field_option_'+type);
			tr.find('[name]').removeAttr('disabled');
			
			var tr_top = tbody.children('tr.field_type');
			
			tr.insertAfter(tr_top);
			tr.show();
			
			
		}).trigger('change');
		
		
		// Add Field Button
		$('#acf_fields #add_field').live('click',function(){
			
			var table_footer = $(this).closest('.table_footer');
			var fields = table_footer.siblings('.fields');
			
			
			// clone last tr
			var new_field = fields.children('.field_clone').clone();
			new_field.removeClass('field_clone').addClass('field');
			
			
			// update input names
			if(new_field.hasClass('sub_field'))
			{
				
				// it is a sub field
				//console.log(fields.parents('.fields').last());
				var field_length = fields.parents('.fields').last().children('.field').length;
				var sub_field_length = fields.children('.field').length;
				//alert(sub_field_length);
				//alert('update numbers for sub field! field:'+field_length+', sub:'+sub_field_length);
				
				new_field.update_names(field_length, sub_field_length);
			}
			else
			{
				var field_length = fields.children('.field').length;
				new_field.update_names(field_length, 0);
				
				//alert('update numbers for field! field:'+field_length);
			}
			
			
			// append to table
			fields.children('.field_clone').before(new_field);
			//fields.append(new_field);
			
			
			// remove no fields message
			if(fields.children('.no_fields_message').exists())
			{
				fields.children('.no_fields_message').hide();
			}
			
			// clear name
			new_field.find('.field_form input[type="text"]').val('');
			new_field.find('.field_form input[type="text"]').first().focus();
			new_field.find('tr.field_type select').trigger('change');	
			
			// open up form
			new_field.find('a.acf_edit_field').first().trigger('click');

			
			// update order numbers
			update_order_numbers();
		
			return false;
			
			
		});
		
		
		// Auto complete field name
		$('.field_form tr.field_label input.label').live('blur', function()
		{
			//console.log('blur');
			var label = $(this);
			var name = $(this).closest('tr').siblings('tr.field_name').find('input.name');

			if(name.val() == '')
			{
				var val = label.val().toLowerCase().split(' ').join('_').split('\'').join('');
				name.val(val);
				name.trigger('keyup');
			}
		});
		
		
		// update field text when typing
		$('.field_form tr.field_label input.label').live('keyup', function()
		{
			var val = $(this).val();
			var name = $(this).closest('.field').find('td.field_label strong a').first().html(val);
		});
		$('.field_form tr.field_name input.name').live('keyup', function()
		{
			var val = $(this).val();
			var name = $(this).closest('.field').find('td.field_name').first().html(val);
		});
		$('.field_form tr.field_type select.type').live('change', function()
		{
			var val = $(this).val();
			var name = $(this).closest('.field').find('td.field_type').first().html(val);
		});
		
		
		// sortable
		$('.fields').sortable({
			update: function(event, ui){update_order_numbers();},
			handle: 'td.field_order'
		});
		
	}

	/*----------------------------------------------------------------------
	*
	*	Document Ready
	*
	*---------------------------------------------------------------------*/
	
	$(document).ready(function(){
		
		// firefox radio button bug
		if($.browser.mozilla) $("form").attr("autocomplete", "off");
	
		
		// add active to Settings Menu
		$('#adminmenu #menu-settings').addClass('current');
		
		// setup fields
		setup_fields();
		
		
		$('#acf_fields input[type="radio"]').each(function(){
			
			if($(this).is(':checked'))
			{
				$(this).removeAttr('checked').attr('checked', 'checked');
			}
			else
			{
				
			}
			
		});
		
	});

})(jQuery);
