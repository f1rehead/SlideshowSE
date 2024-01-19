<?php

if ($data instanceof stdClass) :

	$properties = $data->properties;

	$name = htmlspecialchars($data->name);

	$title = $titleElementTagID = $description = $descriptionElementTagID = $textColor = $color = $url = $target = '';

	$noFollow = false;

	if (isset($properties['title']))
	{
		$title = SlideshowSEPluginSecurity::htmlspecialchars_allow_exceptions($properties['title']);
	}

	if (isset($properties['titleElementTagID']))
	{
		$titleElementTagID = $properties['titleElementTagID'];
	}

	if (isset($properties['description']))
	{
		$description = SlideshowSEPluginSecurity::htmlspecialchars_allow_exceptions($properties['description']);
	}

	if (isset($properties['descriptionElementTagID']))
	{
		$descriptionElementTagID = $properties['descriptionElementTagID'];
	}

	if (isset($properties['textColor']))
	{
		$textColor = htmlspecialchars($properties['textColor']);
	}

	if (isset($properties['color']))
	{
		$color = htmlspecialchars($properties['color']);
	}

	if (isset($properties['url']))
	{
		$url = htmlspecialchars($properties['url']);
	}

	if (isset($properties['urlTarget']))
	{
		$target = $properties['urlTarget'];
	}

	if (isset($properties['noFollow']))
	{
	    $noFollow = true;
	}

	?>

	<div class="widefat sortable-slides-list-item postbox">

		<div class="handlediv" title="<?php _e('Click to toggle'); ?>"><br></div>

		<div class="hndle">
			<div class="slide-icon text-slide-icon"></div>
			<div class="slide-title">
				<?php if (strlen($title) > 0) : ?>

					<?php echo $title; ?>

				<?php else : ?>

					<?php _e('Text slide', 'slideshow-se'); ?>

				<?php endif; ?>
			</div>
			<div class="clear"></div>
		</div>

		<div class="inside">

			<div class="slideshow-group">

				<div class="slideshow-left slideshow-label"><?php _e('Title', 'slideshow-se'); ?></div>
				<div class="slideshow-right">
					<select name="<?php echo esc_attr($name); ?>[titleElementTagID]">
						<?php foreach (SlideshowSEPluginSlideInserter::getElementTags() as $elementTagID => $elementTag): ?>
							<option value="<?php echo esc_attr($elementTagID); ?>" <?php selected($titleElementTagID, $elementTagID); ?>><?php echo esc_textarea($elementTag); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="clear"></div>
				<input type="text" name="<?php echo esc_attr($name); ?>[title]" value="<?php echo $title; ?>" style="width: 100%;" /><br />

			</div>

			<div class="slideshow-group">

				<div class="slideshow-left slideshow-label"><?php _e('Description', 'slideshow-se'); ?></div>
				<div class="slideshow-right">
					<select name="<?php echo esc_attr($name); ?>[descriptionElementTagID]">
						<?php foreach (SlideshowSEPluginSlideInserter::getElementTags() as $elementTagID => $elementTag): ?>
							<option value="<?php echo esc_attr($elementTagID); ?>" <?php selected($descriptionElementTagID, $elementTagID); ?>><?php echo esc_textarea($elementTag); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div clear="clear"></div>
				<textarea name="<?php echo esc_attr($name); ?>[description]" rows="7" cols="" style="width: 100%;"><?php echo $description; ?></textarea><br />

			</div>

			<div class="slideshow-group">

				<div class="slideshow-label"><?php _e('Text color', 'slideshow-se'); ?></div>
				<input type="text" name="<?php echo esc_attr($name); ?>[textColor]" value="<?php echo esc_textarea($textColor); ?>" class="wp-color-picker-field" />

				<div class="slideshow-label"><?php _e('Background color', 'slideshow-se'); ?></div>
				<input type="text" name="<?php echo esc_attr($name); ?>[color]" value="<?php echo esc_textarea($color); ?>" class="wp-color-picker-field" />
				<div style="font-style: italic;"><?php _e('(Leave empty for a transparent background)', 'slideshow-se'); ?></div>

			</div>

			<div class="slideshow-group">

				<div class="slideshow-label"><?php _e('URL', 'slideshow-se'); ?></div>
				<input type="text" name="<?php echo esc_attr($name); ?>[url]" value="<?php echo esc_textarea($url); ?>" style="width: 100%;" />

				<div class="slideshow-label slideshow-left"><?php _e('Open URL in', 'slideshow-se'); ?></div>
				<select name="<?php echo esc_attr($name); ?>[urlTarget]" class="slideshow-right">
					<option value="_self" <?php selected('_self', $target); ?>><?php _e('Same window', 'slideshow-se'); ?></option>
					<option value="_blank" <?php selected('_blank', $target); ?>><?php _e('New window', 'slideshow-se'); ?></option>
				</select>
				<div class="clear"></div>

				<div class="slideshow-label slideshow-left"><?php _e('Don\'t let search engines follow link', 'slideshow-se'); ?></div>
				<input type="checkbox" name="<?php echo esc_attr($name); ?>[noFollow]" value="" <?php checked($noFollow); ?> class="slideshow-right" />
				<div class="clear"></div>

			</div>

			<div class="slideshow-group slideshow-delete-slide">
				<span><?php _e('Delete slide', 'slideshow-se'); ?></span>
			</div>

			<input type="hidden" name="<?php echo esc_attr($name); ?>[type]" value="text" />

		</div>

	</div>
<?php endif; ?>