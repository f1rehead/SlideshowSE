<?php

if ($data instanceof stdClass) :

	// General settings
	$stylesheetLocation = SlideshowSEPluginGeneralSettings::getStylesheetLocation();
	$enableLazyLoading  = SlideshowSEPluginGeneralSettings::getEnableLazyLoading();

	// Roles
	global $wp_roles;

	// Capabilities
	$capabilities = array(
		SlideshowSEPluginGeneralSettings::$capabilities['addSlideshows']    => __('Add slideshows', 'slideshow-se'),
		SlideshowSEPluginGeneralSettings::$capabilities['editSlideshows']   => __('Edit slideshows', 'slideshow-se'),
		SlideshowSEPluginGeneralSettings::$capabilities['deleteSlideshows'] => __('Delete slideshows', 'slideshow-se')
	);

	?>

	<div class="general-settings-tab feature-filter">

		<h4><?php _e('User Capabilities', 'slideshow-se'); ?></h4>

		<p><?php _e('Select the user roles that will able to perform certain actions.', 'slideshow-se');  ?></p>

		<table>

			<?php foreach($capabilities as $capability => $capabilityName): ?>

			<tr valign="top">
				<th><?php echo $capabilityName; ?></th>
				<td>
					<?php

					if(isset($wp_roles->roles) && is_array($wp_roles->roles)):
						foreach($wp_roles->roles as $roleSlug => $values):

							$disabled = ($roleSlug == 'administrator') ? 'disabled="disabled"' : '';
							$checked = ((isset($values['capabilities']) && array_key_exists($capability, $values['capabilities']) && $values['capabilities'][$capability] == true) || $roleSlug == 'administrator') ? 'checked="checked"' : '';
							$name = (isset($values['name'])) ? htmlspecialchars($values['name']) : __('Untitled role', 'slideshow-se');

							?>

							<input
								type="checkbox"
								name="<?php echo htmlspecialchars($capability); ?>[<?php echo htmlspecialchars($roleSlug); ?>]"
								id="<?php echo htmlspecialchars($capability . '_' . $roleSlug); ?>"
								<?php echo $disabled; ?>
								<?php echo $checked; ?>
							/>
							<label for="<?php echo htmlspecialchars($capability . '_' . $roleSlug); ?>"><?php echo $name; ?></label>
							<br />

							<?php endforeach; ?>
						<?php endif; ?>

				</td>
			</tr>

			<?php endforeach; ?>

		</table>
	</div>

	<div class="general-settings-tab feature-filter">

		<h4><?php _e('Settings', 'slideshow-se'); ?></h4>

		<table>
			<tr>
				<td><?php _e('Stylesheet location', 'slideshow-se'); ?></td>
				<td>
					<select name="<?php echo SlideshowSEPluginGeneralSettings::$stylesheetLocation; ?>">
						<option value="head" <?php selected('head', $stylesheetLocation); ?>>Head (<?php _e('top', 'slideshow-se'); ?>)</option>
						<option value="footer" <?php selected('footer', $stylesheetLocation); ?>>Footer (<?php _e('bottom', 'slideshow-se'); ?>)</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><?php _e('Enable lazy loading', 'slideshow-se'); ?></td>
				<td>
					<input type="radio" name="<?php echo SlideshowSEPluginGeneralSettings::$enableLazyLoading; ?>" <?php checked(true, $enableLazyLoading); ?> value="true" /> <?php _e('Yes', 'slideshow-se'); ?>
					<input type="radio" name="<?php echo SlideshowSEPluginGeneralSettings::$enableLazyLoading; ?>" <?php checked(false, $enableLazyLoading); ?> value="false" /> <?php _e('No', 'slideshow-se'); ?>
				</td>
			</tr>
		</table>

	</div>
<?php endif; ?>