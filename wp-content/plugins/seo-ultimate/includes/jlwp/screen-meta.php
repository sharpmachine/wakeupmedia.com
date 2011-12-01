<?php
/********** DROPDOWN CODE **********/

//Special thanks to the Drafts Dropdown plugin for the abstracted code
//http://alexking.org/projects/wordpress

if (!function_exists('screen_meta_html')) {

function screen_meta_html($meta) {
	extract($meta);
	if (function_exists($content)) {
		$content = $content();
	}
	echo '
<div id="screen-meta-'.$key.'-wrap" class="jlwp-screen-meta-content screen-meta-wrap hidden">
	<div class="screen-meta-content">'.$content.'</div>
</div>
<div id="screen-meta-'.$key.'-link-wrap" class="jlwp-screen-meta-toggle hide-if-no-js screen-meta-toggle cf">
<a href="#screen-meta-'.$key.'-wrap" id="screen-meta-'.$key.'-link" class="show-settings">'.$label.'</a>
</div>
	';
}

}

if (!function_exists('screen_meta_output')) {

function screen_meta_output() {
	global $screen_meta;
/*
expected format:
$screen_meta = array(
	array(
		'key' => 'drafts',
		'label' => 'Drafts',
		'content' => 'screen_meta_drafts_content' // can be content or function name
	)
);
*/
	if (!$screen_meta) $screen_meta = array();
	$screen_meta = apply_filters('screen_meta', $screen_meta);
	if (!$screen_meta) return;
	echo '<div id="screen-meta-extra-content">';
	foreach ($screen_meta as $meta) {
		screen_meta_html($meta);
	}
	echo '</div>';
?>
<style type="text/css">
.screen-meta-toggle {
	float: right;
	height: 22px;
	padding: 0;
	margin: 0 0 0 6px;
	border-bottom-left-radius: 3px;
	border-bottom-right-radius: 3px;
}

.screen-meta-wrap h5 {
	margin: 8px 0;
	font-size: 13px;
}
.screen-meta-wrap {
	border-style: none solid solid;
	border-top: 0 none;
	border-width: 0 1px 1px;
	margin: 0 15px;
	padding: 8px 12px 12px;
	-moz-border-radius: 0 0 0 4px;
	-webkit-border-bottom-left-radius: 4px;
	-khtml-border-bottom-left-radius: 4px;
	border-bottom-left-radius: 4px;
}
</style>
<script type="text/javascript">
jQuery(function($) {

// These hacks not needed if adopted into core
// move tabs into place
	$('#screen-meta-extra-content .screen-meta-toggle.cf').each(function() {
		$('#screen-meta-links').append($(this));
	});
// Move content into place
	$('#screen-meta-extra-content .screen-meta-wrap').each(function() {
		$('#screen-meta-links').before($(this));
	});
// end hacks

// simplified generic code to handle all screen meta tabs
	
	$('#screen-meta-links a.show-settings').unbind().click(function() {
		var link = $(this);
		
		var content;
		if ($(link.attr('href') + '-wrap').length)
			content = $(link.attr('href') + '-wrap');
		else
			content = $(link.attr('href'));
		
		content.slideToggle('fast', function() {
			if (link.parents('.screen-meta-toggle').hasClass('screen-meta-active')) {
				link.parents('.screen-meta-toggle').removeClass('screen-meta-active');
				$('a.show-settings').parents('.screen-meta-toggle').not('.screen-meta-active').animate({opacity: 1});
			} else {
				link.parents('.screen-meta-toggle').addClass('screen-meta-active');
				link.css('visibility', 'visible');
				$('a.show-settings').parents('.screen-meta-toggle').not('.screen-meta-active').animate({opacity: 0});
			}
		});
		return false;
	});
	
	var copy = $('#contextual-help-wrap');
	$('.screen-meta-wrap').css({
		'background-color': copy.css('background-color'),
		'border-color': copy.css('border-bottom-color')
	});
	
	var linkcopy = $('#contextual-help-link-wrap');
	$('.screen-meta-toggle').css({
		'background-color': linkcopy.css('background-color'),
		'background-image': linkcopy.css('background-image'),
		'font-family': linkcopy.css('font-family'),
	});
	
});
</script>

<?php
}
add_action('admin_footer', 'screen_meta_output');

}
?>