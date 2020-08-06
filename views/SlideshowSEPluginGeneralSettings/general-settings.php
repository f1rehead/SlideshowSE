<?php
if ($data instanceof stdClass) :

	// Path to the General Settings' views folder
	$generalSettingsViewsPath = SlideshowSEPluginMain::getPluginPath() . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'SlideshowSEPluginGeneralSettings' . DIRECTORY_SEPARATOR;

	?>

	<div class="wrap">
		<form method="post" action="options.php">
			<?php settings_fields(SlideshowSEPluginGeneralSettings::$settingsGroup); ?>

			<div class="icon32" style="background: url('<?php echo SlideshowSEPluginMain::getPluginUrl() . '/images/SlideshowSEPluginPostType/adminIcon32.png'; ?>');"></div>
			<h2 class="nav-tab-wrapper">
				<a href="#general-settings-tab" class="nav-tab nav-tab-active"><?php _e('General Settings', 'slideshow-se'); ?></a>
				<a href="#default-slideshow-settings-tab" class="nav-tab"><?php _e('Default Slideshow Settings', 'slideshow-se'); ?></a>
				<a href="#custom-styles-tab" class="nav-tab"><?php _e('Custom Styles', 'slideshow-se'); ?></a>

				<?php submit_button(null, 'primary', null, false, 'style="float: right;"'); ?>
			</h2>

			<?php

			// General Settings
			SlideshowSEPluginMain::outputView('SlideshowSEPluginGeneralSettings' . DIRECTORY_SEPARATOR . 'general-settings-tab.php');

			// Default slideshow settings
			SlideshowSEPluginMain::outputView('SlideshowSEPluginGeneralSettings' . DIRECTORY_SEPARATOR . 'default-slideshow-settings-tab.php');

			// Custom styles
			SlideshowSEPluginMain::outputView('SlideshowSEPluginGeneralSettings' . DIRECTORY_SEPARATOR . 'custom-styles-tab.php');

			?>

			<p>
				<?php submit_button(null, 'primary', null, false); ?>
			</p>
		</form>
	</div>
<?php endif; ?>