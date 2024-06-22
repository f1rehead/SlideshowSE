<?php if ($data instanceof stdClass) : ?>
	<table>
		<?php if(count($data->settings) > 0): $i = 0; ?>

		<?php foreach($data->settings as $key => $value): ?>

		<?php if( !isset($value, $value['type'], $value['default'], $value['description']) || !is_array($value)) continue;

			// HTML tags to allow in the wp_kses call
			$allowedTags = array(
				'select' => array(
					'name' => array(),
					'class' => array(),
				),
				'option' => array(
					'value' => array(),
					'selected' => array()
				)
			)
		?>
		<tr <?php if(isset($value['dependsOn'])) echo 'style="display:none;"'; ?>>
			<td><?php echo esc_textarea($value['description']); ?></td>
			<td><?php echo wp_kses(SlideshowSEPluginSlideshowSettingsHandler::getInputField(SlideshowSEPluginSlideshowSettingsHandler::$styleSettingsKey, $key, $value), $allowedTags); ?></td>
			<td><?php esc_attr_e('Default', 'slideshow-se'); ?>: &#39;<?php echo (isset($value['options']))? esc_attr($value['options'][$value['default']]): esc_attr($value['default']); ?>&#39;</td>
		</tr>

		<?php endforeach; ?>

		<?php endif; ?>
	</table>

	<p>
		<?php
		/* translators: %s: URL to create or customize a style */
			echo wp_kses_post(sprintf(__(
					'Custom styles can be created and customized %1$shere%2$s.',
					'slideshow-se'
				),
				'<a href="' . admin_url() . '/edit.php?post_type=slideshow&page=general_settings#custom-styles" target="_blank">',
				'</a>'
			));
		?>
	</p>
<?php endif; ?>