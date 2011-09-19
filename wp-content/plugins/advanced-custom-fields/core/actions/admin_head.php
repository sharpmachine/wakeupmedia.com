<?php

global $post;


/*----------------------------------------------------------------------
*
*	Add Post Boxes
*
*---------------------------------------------------------------------*/

if(in_array($GLOBALS['pagenow'], array('post.php', 'post-new.php')))
{
	
	if($GLOBALS['post_type'] == 'acf')
	{
		echo '<script type="text/javascript" src="'.$this->dir.'/js/functions.fields.js" ></script>';
		echo '<script type="text/javascript" src="'.$this->dir.'/js/functions.location.js" ></script>';
		
		echo '<link rel="stylesheet" type="text/css" href="'.$this->dir.'/css/style.global.css" />';
		echo '<link rel="stylesheet" type="text/css" href="'.$this->dir.'/css/style.fields.css" />';
		echo '<link rel="stylesheet" type="text/css" href="'.$this->dir.'/css/style.location.css" />';
		echo '<link rel="stylesheet" type="text/css" href="'.$this->dir.'/css/style.options.css" />';
		
		add_meta_box('acf_fields', 'Fields', array($this, '_fields_meta_box'), 'acf', 'normal', 'high');
		add_meta_box('acf_location', 'Location </span><span class="description">- Add Fields to Edit Screens', array($this, '_location_meta_box'), 'acf', 'normal', 'high');
		add_meta_box('acf_options', 'Advanced Options</span><span class="description">- Customise the edit page', array($this, '_options_meta_box'), 'acf', 'normal', 'high');
	
	}
	else
	{
		// find post type and add wysiwyg support
		$post_type = get_post_type($post);
		if(!post_type_supports($post_type, 'editor'))
		{
			wp_tiny_mce();
		}

		// add css + javascript
		echo '<link rel="stylesheet" type="text/css" href="'.$this->dir.'/css/style.global.css" />';
		echo '<link rel="stylesheet" type="text/css" href="'.$this->dir.'/css/style.input.css" />';
		echo '<script type="text/javascript" src="'.$this->dir.'/js/functions.input.js" ></script>';
		
		// add datepicker
		echo '<link rel="stylesheet" type="text/css" href="'.$this->dir.'/core/fields/date_picker/style.date_picker.css" />';
		echo '<script type="text/javascript" src="'.$this->dir.'/core/fields/date_picker/jquery.ui.datepicker.js" ></script>';
		
		// add meta box
		add_meta_box('acf_input', 'ACF Fields', array($this, 'input_meta_box'), $post_type, 'normal', 'high');

	}
}


?>