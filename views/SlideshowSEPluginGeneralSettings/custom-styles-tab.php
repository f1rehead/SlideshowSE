<?php

if ($data instanceof stdClass) :

	// Get default stylesheets
	$defaultStyles      = array();
	$defaultStylesheets = array(
		'style-light.css' => __('Light', 'slideshow-se'),
		'style-dark.css' => __('Dark', 'slideshow-se')
	);

	$stylesheetsFilePath = SlideshowSEPluginMain::getPluginPath() . DIRECTORY_SEPARATOR . 'style' . DIRECTORY_SEPARATOR . 'SlideshowSEPlugin';

	foreach ($defaultStylesheets as $fileName => $name)
	{
		if (file_exists($stylesheetsFilePath . DIRECTORY_SEPARATOR . $fileName))
		{
			ob_start();

			include $stylesheetsFilePath . DIRECTORY_SEPARATOR . $fileName;

			$defaultStyles[$fileName] = array(
				'name' => $name,
				'style' => ob_get_clean()
			);
		}
	}

	// Get custom styles
	$customStyleValues = array();
	$customStyleKeys   = get_option(SlideshowSEPluginGeneralSettings::$customStyles, array());

	if (is_array($customStyleKeys))
	{
		foreach ($customStyleKeys as $customStyleKey => $customStyleKeyName)
		{
			// Get custom style value from custom style key
			$customStyleValues[$customStyleKey] = get_option($customStyleKey);
		}
	}

	?>

	<div class="custom-styles-tab feature-filter" style="float: left; display: none;">
		<div class="styles-list">

			<p>
				<b><?php esc_attr_e('Default stylesheets', 'slideshow-se'); ?></b>
			</p>

			<ul class="default-styles-list">

				<?php if(is_array($defaultStyles)): ?>
				<?php foreach($defaultStyles as $defaultStyleKey => $defaultStyleValues): ?>

					<?php if(!isset($defaultStyleValues['style']) || empty($defaultStyleValues['style'])) continue; // Continue if style is not set or empty ?>

					<li>
						<span class="style-title"><?php echo (isset($defaultStyleValues['name'])) ? esc_attr($defaultStyleValues['name']) : esc_attr(esc_attr_e('Untitled')); ?></span>
							<span
								class="style-action style-default <?php esc_attr($defaultStyleKey); ?>"
								title="<?php esc_attr_e('Create a new custom style from this style', 'slideshow-se'); ?>"
								>
								<?php esc_attr_e('Customize', 'slideshow-se'); ?> &raquo;
							</span>

						<p style="clear: both;"></p>

						<span class="style-content" style="display: none;"><?php echo esc_attr($defaultStyleValues['style']); ?></span>
					</li>

					<?php endforeach; ?>
				<?php endif; ?>

			</ul>

			<p>
				<b><?php esc_attr_e('Custom stylesheets', 'slideshow-se'); ?></b>
			</p>

			<ul class="custom-styles-list">

				<?php if(is_array($customStyleKeys) && count($customStyleKeys) > 0): ?>
				<?php foreach($customStyleKeys as $customStyleKey => $customStyleKeyName): ?>

					<li>
						<span class="style-title"><?php echo esc_attr($customStyleKeyName); ?></span>

							<span
								class="style-action <?php echo esc_attr($customStyleKey); ?>"
								title="<?php esc_attr_e('Edit this style', 'slideshow-se'); ?>"
								>
								<?php esc_attr_e('Edit', 'slideshow-se'); ?> &raquo;
							</span>

						<span style="float: right;">&#124;</span>

							<span
								class="style-delete <?php echo esc_attr($customStyleKey); ?>"
								title="<?php esc_attr_e('Delete this style', 'slideshow-se'); ?>"
								>
								<?php esc_attr_e('Delete', 'slideshow-se'); ?>
							</span>

						<p style="clear: both;"></p>
					</li>

					<?php endforeach; ?>
				<?php else: ?>

				<li class="no-custom-styles-found">
					<?php esc_attr_e("Click 'Customize' to create a new custom stylesheet."); ?>
				</li>

				<?php endif; ?>

			</ul>

		</div>
	</div>

	<div style="clear: both;"></div>

	<div class="custom-styles-tab feature-filter" style="display: none;">
		<div class="style-editors">

			<p>
				<b><?php esc_attr_e('Custom style editor', 'slideshow-se'); ?></b>
			</p>

			<p class="style-editor">
				<?php esc_attr_e('Select a stylesheet to start customizing it.', 'slideshow-se'); ?>
			</p>

			<?php if(is_array($customStyleValues)): ?>
			<?php foreach($customStyleValues as $customStyleKey => $customStyleValue): ?>

				<div class="style-editor <?php echo esc_attr($customStyleKey); ?>" style="display: none;">

					<p>
						<i><?php esc_attr_e('Title', 'slideshow-se'); ?></i><br />
						<input
							type="text"
							name="<?php echo esc_attr(SlideshowSEPluginGeneralSettings::$customStyles); ?>[<?php echo esc_attr($customStyleKey); ?>][title]"
							value="<?php echo (isset($customStyleKeys[$customStyleKey]) && !empty($customStyleKeys[$customStyleKey])) ? esc_attr($customStyleKeys[$customStyleKey]) : esc_attr_e('Untitled', 'slideshow-se'); ?>"
						/>
					</p>

					<p>
						<i><?php esc_attr_e('Style', 'slideshow-se'); ?></i><br />
						<textarea
							name="<?php echo esc_attr(SlideshowSEPluginGeneralSettings::$customStyles); ?>[<?php echo esc_attr($customStyleKey); ?>][style]"
							rows="25"
							cols=""
						><?php echo isset($customStyleValue) ? esc_attr($customStyleValue) : ''; ?></textarea>
					</p>

				</div>

				<?php endforeach; ?>
			<?php endif; ?>

		</div>

		<div style="clear: both;"></div>

		<div class="custom-style-templates" style="display: none;">

			<li class="custom-styles-list-item">
				<span class="style-title"></span>

						<span
							class="style-action"
							title="<?php esc_attr_e('Edit this style', 'slideshow-se'); ?>"
							>
							<?php esc_attr_e('Edit', 'slideshow-se'); ?> &raquo;
						</span>

				<span style="float: right;">&#124;</span>

						<span
							class="style-delete"
							title="<?php esc_attr_e('Delete this style', 'slideshow-se'); ?>"
							>
							<?php esc_attr_e('Delete', 'slideshow-se'); ?>
						</span>

				<p style="clear: both;"></p>
			</li>

			<div class="style-editor" style="display: none;">

				<p>
					<i><?php esc_attr_e('Title', 'slideshow-se'); ?></i><br />
					<input
						type="text"
						class="new-custom-style-title"
						/>
				</p>

				<p>
					<i><?php esc_attr_e('Style', 'slideshow-se'); ?></i><br />
					<textarea
						class="new-custom-style-content"
						rows="25"
						cols=""
						></textarea>
				</p>

			</div>
		</div>
	</div>
<?php endif; ?>