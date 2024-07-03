<?php if ($data instanceof stdClass) : ?>
	<table>
		<?php $groups = array(); ?>
		<?php if(count($data->settings) > 0): ?>
		<?php foreach($data->settings as $key => $value): ?>

		<?php if( !isset($value, $value['type'], $value['default'], $value['description']) || !is_array($value)) continue; ?>

		<?php if(!empty($value['group']) && !isset($groups[$value['group']])): $groups[$value['group']] = true; ?>
		<tr>
			<td colspan="3" style="border-bottom: 1px solid #e5e5e5; text-align: center;">
				<span style="display: inline-block; position: relative; top: 14px; padding: 0 12px; background: #fff;">
					<?php echo esc_textarea($value['group']); ?> <?php esc_attr_e('settings', 'slideshow-se'); ?>
				</span>
			</td>
		</tr>
		<tr>
			<td colspan="3"></td>
		</tr>
		<?php endif; ?>
		<tr
			<?php echo !empty($value['group'])? esc_attr('class="group-' . strtolower(str_replace(' ', '-', $value['group'])) . '"'): ''; ?>
			<?php echo !empty($value['dependsOn'])? 'style="display:none;"': ''; ?>
		>
			<td><?php echo wp_kses_post($value['description']); ?></td>
			<td><?php echo wp_kses(SlideshowSEPluginSlideshowSettingsHandler::getInputField(SlideshowSEPluginSlideshowSettingsHandler::$settingsKey, $key, $value), SlideShowSEPluginMain::getAllowedTags()); ?></td>
			<td><?php esc_attr_e('Default', 'slideshow-se'); ?>: &#39;<?php echo (isset($value['options']))? esc_attr($value['options'][$value['default']]): esc_attr($value['default']); ?>&#39;</td>
		</tr>

		<?php endforeach; ?>
		<?php endif; ?>
	</table>
<?php endif; ?>