<?php

if ($data instanceof stdClass) :

	$properties = $data->properties;

	$name = $data->name;

	$videoId           = '';
	$showRelatedVideos = 'false';

	if (isset($properties['videoId']))
	{
		$videoId = $properties['videoId'];
	}

	if (isset($properties['showRelatedVideos']) &&
		$properties['showRelatedVideos'] === 'true')
	{
		$showRelatedVideos = 'true';
	}

	?>

	<div class="widefat sortable-slides-list-item postbox">

		<div class="handlediv" title="<?php esc_attr_e('Click to toggle'); ?>"><br></div>

		<div class="hndle">
			<div class="slide-icon video-slide-icon"></div>
			<div class="slide-title">
				<?php esc_attr_e('Video slide', 'slideshow-se'); ?>
			</div>
			<div class="clear"></div>
		</div>

		<div class="inside">

			<div class="slideshow-group">

				<div class="slideshow-label"><?php esc_attr_e('Youtube Video ID', 'slideshow-se'); ?></div>
				<input type="text" name="<?php echo esc_attr($name); ?>[videoId]" value="<?php echo esc_attr($videoId); ?>" style="width: 100%;" />

			</div>

			<div class="slideshow-group">

				<div class="slideshow-label"><?php esc_attr_e('Show related videos', 'slideshow-se'); ?></div>
				<label><input type="radio" name="<?php echo esc_attr($name); ?>[showRelatedVideos]" value="true" <?php checked('true', $showRelatedVideos); ?>><?php esc_attr_e('Yes', 'slideshow-se'); ?></label>
				<label><input type="radio" name="<?php echo esc_attr($name); ?>[showRelatedVideos]" value="false" <?php checked('false', $showRelatedVideos); ?>><?php esc_attr_e('No', 'slideshow-se'); ?></label>

			</div>

			<div class="slideshow-group slideshow-delete-slide">
				<span><?php esc_attr_e('Delete slide', 'slideshow-se'); ?></span>
			</div>

			<input type="hidden" name="<?php echo esc_attr($name); ?>[type]" value="video" />

		</div>

	</div>
<?php endif; ?>