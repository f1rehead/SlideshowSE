<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ($data instanceof stdClass) :

	$properties = $data->properties;

	$title = $description = $textColor = $color = $url = $urlTarget = '';

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
		$description = $properties['description'];
	}

	if (isset($properties['descriptionElementTagID']))
	{
		$descriptionElementTag = SlideshowSEPluginSlideInserter::getElementTag($properties['descriptionElementTagID']);
	}

	if (isset($properties['textColor']))
	{
		$textColor = SlideshowSEPluginSecurity::sanitize_slide_hex_color($properties['textColor']);
	}

	if (isset($properties['color']))
	{
		$color = SlideshowSEPluginSecurity::sanitize_slide_hex_color($properties['color']);
	}

	if (isset($properties['url']))
	{
		$url = $properties['url'];
	}

	if (isset($properties['urlTarget']))
	{
		$urlTarget = $properties['urlTarget'];
	}

	$nofollow = !empty($properties['noFollow']);

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

		if ($textColor !== '')
		{
			$linkAttrs .= ' style="' . esc_attr('color: ' . $textColor . ';') . '"';
		}
	}

	$bgStyle = $color !== '' ? 'background-color: ' . $color . ';' : '';
	$titleColorStyle = $textColor !== '' ? 'color: ' . $textColor . ';' : '';

	// HTML from TinyMCE: rely on <p>/<br>. Plain text with \n: convert to <br> (avoid white-space:pre-line on
	// block wrappers — it makes whitespace between tags look like double spacing).
	$description_html = '';
	if ( is_string( $description ) && strlen( $description ) > 0 ) {
		if ( preg_match( '/<[a-z][^>]*>/i', $description ) ) {
			$description_html = wp_kses_post( $description );
		} else {
			$description_html = wp_kses_post( nl2br( esc_html( $description ), false ) );
		}
	}
	?>

	<div class="slideshow_slide slideshow_slide_text" style="<?php echo esc_attr($bgStyle); ?>">
		<?php if (strlen($title) > 0) : ?>
		<<?php echo esc_attr($titleElementTag); ?> class="slideshow_title" style="<?php echo esc_attr($titleColorStyle); ?>">
			<?php if ($linkAttrs !== '') : ?>
				<a <?php echo wp_kses_post($linkAttrs); ?>><?php echo esc_html($title); ?></a>
			<?php else : ?>
				<?php echo esc_html($title); ?>
			<?php endif; ?>
		</<?php echo esc_attr($titleElementTag); ?>>
		<?php endif; ?>

		<?php if (strlen($description) > 0) : ?>
		<<?php echo esc_attr($descriptionElementTag); ?> class="slideshow_description" style="<?php echo esc_attr($titleColorStyle); ?>">
			<?php if ($linkAttrs !== '') : ?>
				<a <?php echo wp_kses_post($linkAttrs); ?>><?php echo wp_kses_post($description_html); ?></a>
			<?php else : ?>
				<?php echo wp_kses_post($description_html); ?>
			<?php endif; ?>
		</<?php echo esc_attr($descriptionElementTag); ?>>
		<?php endif; ?>

		<?php if ($linkAttrs !== '') : ?>
		<a <?php echo wp_kses_post($linkAttrs); ?> class="slideshow_background_anchor"></a>
		<?php endif; ?>
	</div>

<?php endif; ?>
