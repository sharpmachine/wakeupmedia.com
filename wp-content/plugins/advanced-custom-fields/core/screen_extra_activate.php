<div id="screen-meta-activate-acf-wrap" class="screen-meta-wrap hidden acf">
	<div class="screen-meta-content">
		
		<h5><?php _e("Unlock Special Fields.",'acf'); ?></h5>
		<p><?php _e("Special Fields can be unlocked by purchasing an activation code. Each activation code can be used on multiple sites.",'acf'); ?> <a href="http://plugins.elliotcondon.com/shop/"><?php _e("Visit the Plugin Store",'acf'); ?></a></p>
		<table class="acf_activate widefat">
			<thead>
				<tr>
					<th><?php _e("Field Type",'acf'); ?></th>
					<th><?php _e("Status",'acf'); ?></th>
					<th><?php _e("Activation Code",'acf'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				/*--------------------------------------------------------------------------------------
				*
				*	Repeater Field
				*
				*-------------------------------------------------------------------------------------*/
				?>
				<tr>
					<td><?php _e("Repeater",'acf'); ?></td>
					<td><?php if(array_key_exists('repeater', $this->activated_fields)){
						_e("Active",'acf');
					}
					else
					{
						_e("Inactive",'acf');
					} ?></td>
					<td>
						<form action="" method="post">
							<?php if(array_key_exists('repeater', $this->activated_fields)){
								echo '<span class="activation_code">XXXX-XXXX-XXXX-'.substr($this->activated_fields['repeater'],-4) .'</span>';
								echo '<input type="hidden" name="acf_field_deactivate" value="repeater" />';
								echo '<input type="submit" class="button" value="Deactivate" />';
							}
							else
							{
								echo '<input type="text" name="acf_ac" value="" />';
								echo '<input type="hidden" name="acf_field_activate" value="repeater" />';
								echo '<input type="submit" class="button" value="Activate" />';
							} ?>
						</form>
					</td>
				</tr>
				<?php
				/*--------------------------------------------------------------------------------------
				*
				*	Options Page
				*
				*-------------------------------------------------------------------------------------*/
				?>
				<tr>
					<td><?php _e("Options Page",'acf'); ?></td>
					<td><?php if(array_key_exists('options_page', $this->activated_fields)){
						_e("Active",'acf');
					}
					else
					{
						_e("Inactive",'acf');
					} ?></td>
					<td>
						<form action="" method="post">
							<?php if(array_key_exists('options_page', $this->activated_fields)){
								echo '<span class="activation_code">XXXX-XXXX-XXXX-'.substr($this->activated_fields['options_page'],-4) .'</span>';
								echo '<input type="hidden" name="acf_field_deactivate" value="options_page" />';
								echo '<input type="submit" class="button" value="Deactivate" />';
							}
							else
							{
								echo '<input type="text" name="acf_ac" value="" />';
								echo '<input type="hidden" name="acf_field_activate" value="options_page" />';
								echo '<input type="submit" class="button" value="Activate" />';
							} ?>
						</form>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div id="screen-meta-activate-acf-link-wrap" class="hide-if-no-js screen-meta-toggle acf">
	<a href="#screen-meta-activate-acf" id="screen-meta-activate-acf-link" class="show-settings"><?php _e("Unlock Fields",'acf'); ?></a>
</div>