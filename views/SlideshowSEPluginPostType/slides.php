<?php if ($data instanceof stdClass) : ?>

	<p style="text-align: center; font-style: italic"><?php esc_attr_e('Insert', 'slideshow-se'); ?>:</p>
	<p style="text-align: center;">
		<input type="button" class="button slideshow-insert-image-slide" value="<?php esc_attr_e('Image slide', 'slideshow-se'); ?>" />
		<input type="button" class="button slideshow-insert-text-slide" value="<?php esc_attr_e('Text slide', 'slideshow-se'); ?>" />
		<input type="button" class="button slideshow-insert-video-slide" value="<?php esc_attr_e('Video slide', 'slideshow-se'); ?>" />
	</p>

	<p style="text-align: center;">
		<a href="#" class="open-slides-button"><?php esc_attr_e('Open all', 'slideshow-se'); ?></a> |
		<a href="#" class="close-slides-button"><?php esc_attr_e('Close all', 'slideshow-se'); ?></a>
	</p>

	<?php if (count($data->slides) <= 0) : ?>
		<p><?php esc_attr_e('Add slides to this slideshow by using one of the buttons above.', 'slideshow-se'); ?></p>
	<?php endif; ?>

	<div class="sortable-slides-list">

		<?php

		if (is_array($data->slides))
		{
			$i = 0;
			// error_log("VAR I ".$i);

			foreach ($data->slides as $slide)
			{
				$data             = new stdClass();
				$data->name       = SlideshowSEPluginSlideshowSettingsHandler::$slidesKey . '[' . $i . ']';
				$data->properties = $slide;

				// This switch statement works to prevent local file injection issues
				// by not providing any way for attackers to modify the file name.
				$stype = "attachment";
				switch ($slide['type']) {
					case "text":
						$stype = "text";
						break;
					case "templates":
						$stype = "templates";
						break;
					case "video":
						$stype = "video";
						break;
					default:
						break;
					}

				SlideshowSEPluginMain::outputView('SlideshowSEPluginSlideshowSlide' . DIRECTORY_SEPARATOR . 'backend_' . esc_attr($stype) . '.php', $data);

				$i++;
			}
		}

		?>

	</div>

	<?php SlideshowSEPluginMain::outputView('SlideshowSEPluginSlideshowSlide' . DIRECTORY_SEPARATOR . 'backend_templates.php'); ?>

<?php endif; ?>