<?php if ($data instanceof stdClass) : ?>

	<p><?php esc_attr_e('To use this slideshow in your website either add this piece of shortcode to your posts or pages', 'slideshow-se'); ?>:</p>
	<p style="font-style: italic;"><?php echo esc_attr($data->shortCode); ?></p>

	<?php if(current_user_can('edit_themes')): ?>
	<p><?php esc_attr_e('Or add this piece of code to where ever in your website you want to place the slideshow', 'slideshow-se'); ?>:</p>
	<p style="font-style: italic;"><?php echo esc_attr($data->snippet); ?></p>
	<?php endif; ?>

	<p><?php 
	/* translators: %s: URL to the widget page */
	echo wp_kses_post(sprintf(__(
			'Or go to the %1$swidgets page%2$s and show the slideshow as a widget.',
			'slideshow-se'
		),
		'<a href="' . get_admin_url(null, 'widgets.php') . '" target="_blank">',
		'</a>'
	));
	?></p>
	<p><?php esc_attr_e('Or choose this slideshow in the Slideshow block using the Gutenberg editor', 'slideshow-se'); ?>:</p>
	<p style="font-style: italic; font-weight: 600"><?php echo esc_textarea($data->postTitle); ?></p>


<?php endif; ?>