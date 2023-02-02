<?php

if ($data instanceof stdClass) :

	$properties = $data->properties;

	// The attachment should always be there
	$attachment = get_post($properties['postId']);

	if (isset($attachment)):

		$name = htmlspecialchars($data->name);

		$title = $titleElementTagID = $description = $descriptionElementTagID = $url = $target = $alternativeText = '';

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

		if (isset($properties['url']))
		{
			$url = $properties['url'];
		}

		if (isset($properties['urlTarget']))
		{
			$target = $properties['urlTarget'];
		}

	    if (isset($properties['noFollow']))
	    {
	        $noFollow = true;
	    }

		if (isset($properties['alternativeText']))
		{
			$alternativeText = htmlspecialchars($properties['alternativeText']);
		}
		else
		{
			$alternativeText = $title;
		}

		// Prepare image
		$image        = wp_get_attachment_image_src($attachment->ID);
		$imageSrc     = '';
		$displaySlide = true;

		if (!is_array($image) ||
			!$image)
		{
			if (!empty($attachment->guid))
			{
				$imageSrc = $attachment->guid;
			}
			else
			{
				$displaySlide = false;
			}
		}
		else
		{
			$imageSrc = $image[0];
		}

		if (!$imageSrc ||
			empty($imageSrc))
		{
			$imageSrc = SlideshowSEPluginMain::getPluginUrl() . '/images/' . __CLASS__ . '/no-img.png';
		}

		$editUrl = admin_url() . '/media.php?attachment_id=' . $attachment->ID . '&amp;action=edit';

		if ($displaySlide): ?>

			<div id="" class="widefat sortable-slides-list-item postbox">

				<div class="handlediv" title="<?php _e('Click to toggle'); ?>"><br></div>

				<div class="hndle">
					<div class="slide-icon image-slide-icon"></div>
					<div class="slide-title">
						<?php if (strlen($title) > 0) : ?>

							<?php echo esc_textarea($title); ?>

						<?php else : ?>

							<?php _e('Image slide', 'slideshow-se'); ?>

						<?php endif; ?>
					</div>
					<div class="clear"></div>
				</div>

				<div class="inside">

					<div class="slideshow-group">

						<a href="<?php echo esc_attr($editUrl); ?>" title="<?php _e('Edit', 'slideshow-se'); ?> &#34;<?php echo esc_textarea($attachment->post_title); ?>&#34;">
							<img width="80" height="60" src="<?php echo esc_attr($imageSrc); ?>" class="attachment-80x60" alt="<?php echo esc_attr($attachment->post_title); ?>" title="<?php echo esc_attr($attachment->post_title); ?>" />
						</a>

					</div>

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
						<input type="text" name="<?php echo esc_attr($name); ?>[title]" value="<?php echo esc_attr($title); ?>" style="width: 100%;" />

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
						<textarea name="<?php echo esc_attr($name); ?>[description]" rows="3" cols="" style="width: 100%;"><?php echo esc_textarea($description); ?></textarea>

					</div>

					<div class="slideshow-group">

						<div class="slideshow-label"><?php _e('URL', 'slideshow-se'); ?></div>
						<input type="text" name="<?php echo esc_attr($name); ?>[url]" value="<?php echo esc_attr($url); ?>" style="width: 100%;" />

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

					<div class="slideshow-group">

						<div class="slideshow-label"><?php _e('Alternative text', 'slideshow-se'); ?></div>
						<input type="text" name="<?php echo esc_attr($name); ?>[alternativeText]" value="<?php echo esc_attr($alternativeText); ?>" style="width: 100%;" />

					</div>

					<div class="slideshow-group slideshow-delete-slide">
						<span><?php _e('Delete slide', 'slideshow-se'); ?></span>
					</div>

					<input type="hidden" name="<?php echo esc_attr($name); ?>[type]" value="attachment" />
					<input type="hidden" name="<?php echo esc_attr($name); ?>[postId]" value="<?php echo esc_attr($attachment->ID); ?>" />

				</div>

			</div>

		<?php endif; ?>
	<?php endif; ?>
<?php endif; ?>
