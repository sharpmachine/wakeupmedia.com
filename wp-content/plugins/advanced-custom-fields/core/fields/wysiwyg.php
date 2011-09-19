<?php

class acf_Wysiwyg
{
	var $name;
	var $title;
	
	function acf_Wysiwyg()
	{
		$this->name = 'wysiwyg';
		$this->title = __("Wysiwyg Editor",'acf');
	}
	
	function html($field)
	{
		echo '<div class="acf_wysiwyg">';
		?>
		<div id="editor-toolbar" style="display:none;">
		
		<div id="media-buttons">
Upload/Insert <a title="Add an Image" class="thickbox" id="add_image" href="media-upload.php?post_id=1802&amp;type=image&amp;TB_iframe=1&amp;width=640&amp;height=314"><img onclick="return false;" alt="Add an Image" src="http://localhost:8888/acf/wp-admin/images/media-button-image.gif?ver=20100531"></a><a title="Add Video" class="thickbox" id="add_video" href="media-upload.php?post_id=1802&amp;type=video&amp;TB_iframe=1&amp;width=640&amp;height=314"><img onclick="return false;" alt="Add Video" src="http://localhost:8888/acf/wp-admin/images/media-button-video.gif?ver=20100531"></a><a title="Add Audio" class="thickbox" id="add_audio" href="media-upload.php?post_id=1802&amp;type=audio&amp;TB_iframe=1&amp;width=640&amp;height=314"><img onclick="return false;" alt="Add Audio" src="http://localhost:8888/acf/wp-admin/images/media-button-music.gif?ver=20100531"></a><a title="Add Media" class="thickbox" id="add_media" href="media-upload.php?post_id=1802&amp;TB_iframe=1&amp;width=640&amp;height=314"><img onclick="return false;" alt="Add Media" src="http://localhost:8888/acf/wp-admin/images/media-button-other.gif?ver=20100531"></a>		</div>
	</div>
		<?php
		echo '<div id="editorcontainer"><textarea name="'.$field->input_name.'" >';
		echo wp_richedit_pre($field->value);
		echo '</textarea></div></div>';
	}
	
	function format_value_for_api($value, $options = null)
	{
		$value = apply_filters('the_content',$value); 
		return $value;
	}
}

?>