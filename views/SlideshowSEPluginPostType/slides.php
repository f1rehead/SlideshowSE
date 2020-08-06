<?php if ($data instanceof stdClass) : ?>

	<p style="text-align: center; font-style: italic"><?php _e('Insert', 'slideshow-se'); ?>:</p>
	<p style="text-align: center;">
		<input type="button" class="button slideshow-insert-image-slide" value="<?php _e('Image slide', 'slideshow-se'); ?>" />
		<input type="button" class="button slideshow-insert-text-slide" value="<?php _e('Text slide', 'slideshow-se'); ?>" />
		<input type="button" class="button slideshow-insert-video-slide" value="<?php _e('Video slide', 'slideshow-se'); ?>" />
	</p>

	<p style="text-align: center;">
		<a href="#" class="open-slides-button"><?php _e('Open all', 'slideshow-se'); ?></a> |
		<a href="#" class="close-slides-button"><?php _e('Close all', 'slideshow-se'); ?></a>
	</p>

	<?php if (count($data->slides) <= 0) : ?>
		<p><?php _e('Add slides to this slideshow by using one of the buttons above.', 'slideshow-se'); ?></p>
	<?php endif; ?>

	<div class="sortable-slides-list">

		<?php

		if (is_array($data->slides))
		{
			$i = 0;

			foreach ($data->slides as $slide)
			{
				$data             = new stdClass();
				$data->name       = SlideshowSEPluginSlideshowSettingsHandler::$slidesKey . '[' . $i . ']';
				$data->properties = $slide;

				SlideshowSEPluginMain::outputView('SlideshowSEPluginSlideshowSlide' . DIRECTORY_SEPARATOR . 'backend_' . $slide['type'] . '.php', $data);

				$i++;
			}
		}

		?>

	</div>

	<?php SlideshowSEPluginMain::outputView('SlideshowSEPluginSlideshowSlide' . DIRECTORY_SEPARATOR . 'backend_templates.php'); ?>

<?php endif; ?>