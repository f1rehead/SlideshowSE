<?php

if ($data instanceof stdClass):

	$properties = $data->properties;

	$title = $description = $url = $urlTarget = $alternativeText = $postId = '';

	$titleElementTag = $descriptionElementTag = SlideshowSEPluginSlideInserter::getElementTag();

	if (isset($properties['title']))
	{
		$title = $properties['title'];
	}

	if (isset($properties['titleElementTagID']))
	{
		$titleElementTag = SlideshowSEPluginSlideInserter::getElementTag($properties['titleElementTagID']);
	}

	if (isset($properties['description']))
	{
		$description = trim(SlideshowSEPluginSecurity::htmlspecialchars_allow_exceptions($properties['description']));
	}

	if (isset($properties['descriptionElementTagID']))
	{
		$descriptionElementTag = SlideshowSEPluginSlideInserter::getElementTag($properties['descriptionElementTagID']);
	}

	if (isset($properties['url']))
	{
		$url = $properties['url'];
	}

	if (isset($properties['urlTarget']))
	{
		$urlTarget = $properties['urlTarget'];
	}

	if (isset($properties['alternativeText']))
	{
		$alternativeText = $properties['alternativeText'];
	}

	$nofollow = !empty($properties['noFollow']);

	if (isset($properties['postId']))
	{
		$postId = $properties['postId'];
	}

	$linkHref = '';

	if (is_string($url) && strlen($url) > 0)
	{
		$linkHref = SlideshowSEPluginSecurity::sanitize_slide_destination_url($url);
	}

	$linkTarget = SlideshowSEPluginSecurity::sanitize_slide_link_target(
		is_string($urlTarget) ? $urlTarget : ''
	);

	$linkRel = array();

	if ($linkTarget === '_blank')
	{
		$linkRel[] = 'noopener';
		$linkRel[] = 'noreferrer';
	}

	if ($nofollow)
	{
		$linkRel[] = 'nofollow';
	}

	$linkAttrs = '';

	if ($linkHref !== '')
	{
		$linkAttrs = 'href="' . $linkHref . '"';

		if ($linkTarget !== '')
		{
			$linkAttrs .= ' target="' . esc_attr($linkTarget) . '"';
		}

		if (count($linkRel) > 0)
		{
			$linkAttrs .= ' rel="' . esc_attr(implode(' ', $linkRel)) . '"';
		}
	}

	// Post ID should always be numeric
	if (is_numeric($postId)):

		// Get post from post id. Post should be able to load
		$attachment = get_post($postId);
		if (!empty($attachment)):

			// If no alternative text is set, get the alt from the original image
			if (empty($alternativeText))
			{
				$alternativeText = $title;

				if (empty($alternativeText))
				{
					$alternativeText = $attachment->post_title;
				}

				if (empty($alternativeText))
				{
					$alternativeText = $attachment->post_content;
				}
			}

			// Prepare image
			$image          = wp_get_attachment_image_src($attachment->ID, 'full');
			$imageSrc       = '';
			$imageWidth     = 0;
			$imageHeight    = 0;
			$imageAvailable = true;

			if (!is_array($image) ||
				!$image ||
				!isset($image[0]))
			{
				if (!empty($attachment->guid))
				{
					$imageSrc = $attachment->guid;
				}
				else
				{
					$imageAvailable = false;
				}
			}
			else
			{
				$imageSrc = $image[0];

				if (isset($image[1], $image[2]))
				{
					$imageWidth  = $image[1];
					$imageHeight = $image[2];
				}
			}

			// If image is available
			if ($imageAvailable): ?>

				<div class="slideshow_slide slideshow_slide_image">
					<?php if ($linkAttrs !== '') : ?>
					<a <?php echo $linkAttrs; ?>>
						<img src="<?php echo esc_url($imageSrc); ?>" alt="<?php echo esc_attr($alternativeText); ?>" <?php echo ($imageWidth > 0) ? 'width="' . esc_attr($imageWidth) . '"' : ''; ?> <?php echo ($imageHeight > 0) ? 'height="' . esc_attr($imageHeight) . '"' : ''; ?> />
					</a>
					<?php else : ?>
						<img src="<?php echo esc_url($imageSrc); ?>" alt="<?php echo esc_attr($alternativeText); ?>" <?php echo ($imageWidth > 0) ? 'width="' . esc_attr($imageWidth) . '"' : ''; ?> <?php echo ($imageHeight > 0) ? 'height="' . esc_attr($imageHeight) . '"' : ''; ?> />
					<?php endif; ?>
					<div class="slideshow_description_box slideshow_transparent">
						<?php if (!empty($title)) : ?>
						<<?php echo esc_attr($titleElementTag); ?> class="slideshow_title"><?php if ($linkAttrs !== '') : ?><a <?php echo $linkAttrs; ?>><?php echo wp_kses_post($title); ?></a><?php else : ?><?php echo wp_kses_post($title); ?><?php endif; ?></<?php echo esc_attr($titleElementTag); ?>>
						<?php endif; ?>
						<?php if (!empty($description)) : ?>
						<<?php echo esc_attr($descriptionElementTag); ?> class="slideshow_description"><?php if ($linkAttrs !== '') : ?><a <?php echo $linkAttrs; ?>><?php echo esc_html($description); ?></a><?php else : ?><?php echo esc_html($description); ?><?php endif; ?></<?php echo esc_attr($descriptionElementTag); ?>>
						<?php endif; ?>
					</div>
				</div>

			<?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>
<?php endif; ?>
