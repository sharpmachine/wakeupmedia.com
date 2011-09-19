(function($){

		
	
	/*--------------------------------------------------------------------------
		setup_fields
	--------------------------------------------------------------------------*/
	function setup_rules()
	{
		var tbody = $('table#location_rules tbody');
		
		
		// show field type options
		tbody.find('td.param select').live('change', function(){
			
			var tr = $(this).closest('tr');
			var val = $(this).val();
			
			
			// does it have options?
			if(!$(this).find('option[value="options_page"]').exists())
			{
				//console.log('select: '+type+'. parent length: '+$(this).closest('.repeater').length);
				$(this).append('<option value="options_page" disabled="true">Options Page (Unlock field with activation code)</option>');
				
			}
			
			
			tr.find('td.value div').hide();
			tr.find('td.value div [name]').attr('disabled', 'true');
			
			tr.find('td.value div[rel="'+val+'"]').show();
			tr.find('td.value div[rel="'+val+'"] [name]').removeAttr('disabled');
			
		}).trigger('change');
		
		
		// Add Button
		tbody.find('td.buttons a.add').live('click',function(){
			
			var tr_count = $(this).closest('tbody').children('tr').length;
			var tr = $(this).closest('tr').clone();
			
			tr.insertAfter($(this).closest('tr'));
			
			update_names();
			
			can_remove_more();
			
			return false;
			
		});
		
		
		// Remove Button
		tbody.find('td.buttons a.remove').live('click',function(){
			
			var tr = $(this).closest('tr').remove();
			
			can_remove_more();
			
			return false;
			
		});
		
		function can_remove_more()
		{
			if(tbody.children('tr').length == 1)
			{
				tbody.children('tr').each(function(){
					$(this).find('td.buttons a.remove').addClass('disabled');
				});
			}
			else
			{
				tbody.children('tr').each(function(){
					$(this).find('td.buttons a.remove').removeClass('disabled');
				});
			}
			
		}
		
		can_remove_more();
		
		function update_names()
		{
			tbody.children('tr').each(function(i){
			
				$(this).find('[name]').each(function(){
				
					var name = $(this).attr('name').split("][");
					
					var new_name = name[0] + "][" + name[1] + "][" + i + "][" + name[3];

					$(this).attr('name', new_name);
				});
				
			})
		}
		
	}

	/*--------------------------------------------------------------------------
		Document Ready
	--------------------------------------------------------------------------*/
	$(document).ready(function(){
	
		setup_rules();
 		
	});

})(jQuery);
