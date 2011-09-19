<?php
/**
 * Competition Researcher Module
 * 
 * @since 1.2
 */

if (class_exists('SU_Module')) {

class SU_CompetitionQueries extends SU_Module {

	function get_module_title() { return __('Competition Researcher', 'seo-ultimate'); }
	function get_menu_title() { return __('Comp. Researcher', 'seo-ultimate'); }

	function admin_page_contents() {
		echo '<p>';
		_e('The Competition Researcher provides you with easy access to various search engine tools which you can use to research multiple search queries or URLs.', 'seo-ultimate');
		echo "</p>\n";
?>
<form method="get" action="http://www.seodesignsolutions.com/blog/ultimate-seo-toolkit/result.php" target="_blank">
<h3><?php _e('Step 1: Choose Your Research Tool', 'seo-ultimate'); ?></h3>
<?php
		
		$methods = array(
			__('Keywords', 'seo-ultimate') => array( __('Normal Search', 'seo-ultimate') => __('Find out how many pages contain the words in each query', 'seo-ultimate')
													,__('Phrase Match', 'seo-ultimate') => __('Find out how many &#8220;actual&#8221; pages are competing for each query', 'seo-ultimate')
													,__('Allinanchor', 'seo-ultimate') => __('Find out which sites have the most links for each query', 'seo-ultimate')
													,__('Allintitle', 'seo-ultimate') => __('Find out which sites have the highest relevance in the title for each query', 'seo-ultimate')
													,__('Allintext', 'seo-ultimate') => __('Find out which sites have the most relevant content/text on their pages', 'seo-ultimate')
													,__('Allinurl', 'seo-ultimate') => __('Find out which sites have the most relevant naming conventions for each keyword', 'seo-ultimate')
													),
			__('URLs', 'seo-ultimate') => array( __('Site', 'seo-ultimate') => __('Find out how many pages are indexed for each domain', 'seo-ultimate')
												,__('Inbound Links', 'seo-ultimate') => __('Find out how many sites link to the domains', 'seo-ultimate')
												,__('Outbound Links', 'seo-ultimate') => __('Find out how many sites the domains link to', 'seo-ultimate')
												)
		);
		
		$nominimal = array(__('Inbound Links', 'seo-ultimate'), __('Outbound Links', 'seo-ultimate'));
		
		$first=true; $i=0;
		foreach ($methods as $type => $tools) {
			foreach ($tools as $title => $desc) {
				$value = strtolower(str_replace(array(' ', '-'), '', $title));

				if ($desc) $desc = " &ndash; <i>$desc</i>";
				
				if (in_array($title, $nominimal)) $showminimal='false'; else $showminimal='true';

				if ($first) { $checked=" checked='checked'"; $first=false; } else $checked='';
				echo "<label><input type='radio' name='method' value='$value' id='method$i' onclick='javascript:su_competition_queries_show_step2(\"$type\", $showminimal)'$checked /> ".
					"<span class='title'>$title</span>$desc</label><br />\n";
				
				$i++;
			}
		}
?>
<h3><?php _e('Step 2: Enter the <span id="methodtype">Keywords</span> To Research', 'seo-ultimate'); ?></h3>
<div><textarea id="queries" name="queries" rows="10" cols="60"></textarea></div>
<div><em><?php _e('(Type in one per line)', 'seo-ultimate'); ?></em></div>

<h3><?php _e('Step 3: Set Options and Submit', 'seo-ultimate'); ?></h3>
<div>
	<label><input type="checkbox" name="r100" value="1" /> <?php _e('Show 100 results per page', 'seo-ultimate'); ?></label><br />
	<label id="minimal-checkbox"><input type="checkbox" name="minimal" value="1" /> <?php
		_e('Use Google&#8217;s minimal mode', 'seo-ultimate'); ?></label><br /><br />
</div>
<input type="hidden" name="mixing" id="mixing" value="0" />
<input type="hidden" name="showback" id="showback" value="0" />
<input type="hidden" name="client" id="client" value="su-<?php echo SU_VERSION; ?>" />

<div id="submit"><input type="submit" value="<?php _e('Submit', 'seo-ultimate'); ?>" class="button-primary" /></div>
</form>

<!--Load the blog's homepage so that it shows up as a purple link in Google's minimal mode-->
<iframe src="<?php bloginfo('url') ?>" style="width: 0; height: 0; display: none; visibility: hidden;"></iframe>
<?php
	}
}

}
?>