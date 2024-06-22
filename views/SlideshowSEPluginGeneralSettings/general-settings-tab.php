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

		<h4><?php esc_attr_e('User Capabilities', 'slideshow-se'); ?></h4>

		<p><?php esc_attr_e('Select the user roles that will able to perform certain actions.', 'slideshow-se');  ?></p>

		<table>

			<?php foreach($capabilities as $capability => $capabilityName): ?>

			<tr valign="top">
				<th><?php echo esc_textarea($capabilityName); ?></th>
				<td>
					<?php

					if(isset($wp_roles->roles) && is_array($wp_roles->roles)):
						foreach($wp_roles->roles as $roleSlug => $values):

							$disabled = ($roleSlug == 'administrator') ? 'disabled="disabled"' : '';
							$checked = ((isset($values['capabilities']) && array_key_exists($capability, $values['capabilities']) && $values['capabilities'][$capability] == true) || $roleSlug == 'administrator') ? 'checked="checked"' : '';
							$name = (isset($values['name'])) ? $values['name'] : __('Untitled role', 'slideshow-se');

							?>

							<input
								type="checkbox"
								name="<?php echo esc_attr($capability); ?>[<?php echo esc_attr($roleSlug); ?>]"
								id="<?php echo esc_attr($capability . '_' . $roleSlug); ?>"
								<?php echo esc_attr($disabled); ?>
								<?php echo esc_attr($checked); ?>
							/>
							<label for="<?php echo esc_attr($capability . '_' . $roleSlug); ?>"><?php echo esc_attr($name); ?></label>
							<br />

							<?php endforeach; ?>
						<?php endif; ?>

				</td>
			</tr>

			<?php endforeach; ?>

		</table>
	</div>

	<div class="general-settings-tab feature-filter">

		<h4><?php esc_attr_e('Settings', 'slideshow-se'); ?></h4>

		<table>
			<tr>
				<td><?php esc_attr_e('Stylesheet location', 'slideshow-se'); ?></td>
				<td>
					<select name="<?php echo esc_attr(SlideshowSEPluginGeneralSettings::$stylesheetLocation); ?>">
						<option value="head" <?php selected('head', $stylesheetLocation); ?>>Head (<?php esc_attr_e('top', 'slideshow-se'); ?>)</option>
						<option value="footer" <?php selected('footer', $stylesheetLocation); ?>>Footer (<?php esc_attr_e('bottom', 'slideshow-se'); ?>)</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><?php esc_attr_e('Enable lazy loading', 'slideshow-se'); ?></td>
				<td>
					<input type="radio" name="<?php echo esc_attr(SlideshowSEPluginGeneralSettings::$enableLazyLoading); ?>" <?php checked(true, $enableLazyLoading); ?> value="true" /> <?php esc_attr_e('Yes', 'slideshow-se'); ?>
					<input type="radio" name="<?php echo esc_attr(SlideshowSEPluginGeneralSettings::$enableLazyLoading); ?>" <?php checked(false, $enableLazyLoading); ?> value="false" /> <?php esc_attr_e('No', 'slideshow-se'); ?>
				</td>
			</tr>
		</table>

	</div>
<?php endif; ?>