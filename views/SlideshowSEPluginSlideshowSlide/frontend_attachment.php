<?php

if ($data instanceof stdClass):

	$properties = $data->properties;

	$title = $description = $url = $urlTarget = $alternativeText = $noFollow = $postId = '';

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

	if (isset($properties['noFollow']))
	{
		$noFollow = ' rel="nofollow" ';
	}

	if (isset($properties['postId']))
	{
		$postId = $properties['postId'];
	}

	// Post ID should always be numeric
	if (is_numeric($postId)):

		$anchorTag = $endAnchorTag = $anchorTagAttributes = '';

		if (strlen($url) > 0)
		{
			$anchorTagAttributes =
				'href=' . $url . ' ' .
				(strlen($urlTarget) > 0 ? 'target="' . $urlTarget . '" ' : '') .
				$noFollow;

			$anchorTag    = '<a ' . $anchorTagAttributes . '>';
			$endAnchorTag = '</a>';
		}

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
					<?php echo wp_kses_post($anchorTag); ?>
						<img src="<?php echo esc_attr($imageSrc); ?>" alt="<?php echo esc_attr($alternativeText); ?>" <?php echo ($imageWidth > 0) ? 'width="' . esc_attr($imageWidth) . '"' : ''; ?> <?php echo ($imageHeight > 0) ? 'height="' . esc_attr($imageHeight) . '"' : ''; ?> />
					<?php echo wp_kses_post($endAnchorTag); ?>
					<div class="slideshow_description_box slideshow_transparent">
						<?php echo !empty($title) ? '<' . esc_attr($titleElementTag) . ' class="slideshow_title">' . wp_kses_post($anchorTag) . wp_kses_post($title) . wp_kses_post($endAnchorTag) . '</' . esc_attr($titleElementTag) . '>' : ''; ?>
						<?php echo !empty($description) ? '<' . esc_attr($descriptionElementTag) . ' class="slideshow_description">' . wp_kses_post($anchorTag) . esc_attr($description) . wp_kses_post($endAnchorTag) . '</' . esc_attr($descriptionElementTag) . '>' : ''; ?>
					</div>
				</div>

			<?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>
<?php endif; ?>