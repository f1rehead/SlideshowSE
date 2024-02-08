<?php

if ($data instanceof stdClass) :

	$properties = $data->properties;

	$title = $description = $textColor = $color = $url = $urlTarget = $noFollow = '';

	$titleElementTag = $descriptionElementTag = SlideshowSEPluginSlideInserter::getElementTag();

	if (isset($properties['title']))
	{
		$title = trim(SlideshowSEPluginSecurity::htmlspecialchars_allow_exceptions($properties['title']));
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

	if (isset($properties['textColor']))
	{
		$textColor = $properties['textColor'];

		if (substr($textColor, 0, 1) != '#')
		{
			$textColor = '#' . $textColor;
		}

		$textColor = esc_attr($textColor);
	}

	if (isset($properties['color']))
	{
		$color = $properties['color'];

		if (substr($color, 0, 1) != '#')
		{
			$color = '#' . $color;
		}

		$color = esc_attr($color);
	}

	if (isset($properties['url']))
	{
		$url = esc_attr($properties['url']);
	}

	if (isset($properties['urlTarget']))
	{
		$urlTarget = esc_attr($properties['urlTarget']);
	}

	if (isset($properties['noFollow']))
	{
		$noFollow = 'rel="nofollow"';
	}

	$anchorTag = $endAnchorTag = $anchorTagAttributes = '';

	if (strlen($url) > 0)
	{
		$anchorTagAttributes =
			'href=' . $url . ' ' .
			(strlen($urlTarget) > 0 ? 'target="' . $urlTarget . '" ' : '') .
			(strlen($textColor) > 0 ? 'style="color: ' . $textColor . '" ' : '') .
			$noFollow;

		$anchorTag    = '<a ' . esc_attr($anchorTagAttributes) . '>';
		$endAnchorTag = '</a>';
	}

	?>

	<div class="slideshow_slide slideshow_slide_text" style="<?php echo esc_attr(strlen($color)) > 0 ? 'background-color: ' . esc_attr($color) . ';' : '' ?>">
		<?php if(strlen($title) > 0): ?>
		<<?php echo esc_attr($titleElementTag); ?> class="slideshow_title" style="<?php echo esc_attr(strlen($textColor)) > 0 ? 'color: ' . esc_attr($textColor) . ';' : ''; ?>">
			<?php echo $anchorTag; ?>
				<?php echo $title; ?>
			<?php echo $endAnchorTag; ?>
		</<?php echo esc_attr($titleElementTag); ?>>
		<?php endif; ?>

		<?php if(strlen($description) > 0): ?>
		<<?php echo esc_attr($descriptionElementTag); ?> class="slideshow_description" style="<?php echo esc_attr(strlen($textColor)) > 0 ? 'color: ' . esc_attr($textColor) . ';' : ''; ?>">
			<?php $anchorTag; ?>
			<?php echo $description; ?>
			<?php echo $endAnchorTag; ?>
		</<?php echo esc_attr($descriptionElementTag); ?>>
		<?php endif; ?>

		<a <?php echo esc_attr($anchorTagAttributes) ?> class="slideshow_background_anchor"></a>
	</div>

<?php endif; ?>