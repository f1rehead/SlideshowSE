<?php if ($data instanceof stdClass) : ?>

	<p>
		<label for="<?php echo esc_attr($data->widget->get_field_id('title')); ?>"><?php esc_attr_e('Title', 'slideshow-se'); ?></label>
		<input class="widefat" id="<?php echo esc_attr($data->widget->get_field_id('title')); ?>" name="<?php echo esc_attr($data->widget->get_field_name('title')); ?>" value="<?php echo esc_attr($data->instance['title']); ?>" style="width:100%" />
	</p>

	<p>
		<label for="<?php echo esc_attr($data->widget->get_field_id('slideshowId')); ?>"><?php esc_attr_e('Slideshow', 'slideshow-se'); ?></label>
		<select class="widefat" id="<?php echo esc_attr($data->widget->get_field_id('slideshowId')); ?>" name="<?php echo esc_attr($data->widget->get_field_name('slideshowId')); ?>" value="<?php echo esc_attr(is_numeric($data->instance['slideshowId']))? esc_attr($data->instance['slideshowId']) : ''; ?>" style="width:100%">
			<option value="-1" <?php selected($data->instance['slideshowId'], -1); ?>><?php esc_attr_e('Random Slideshow', 'slideshow-se'); ?></option>
			<?php if(count($data->slideshows) > 0): ?>
			<?php foreach($data->slideshows as $slideshow): ?>
				<option value="<?php echo esc_attr($slideshow->ID) ?>" <?php selected($data->instance['slideshowId'], $slideshow->ID); ?>><?php echo !empty($slideshow->post_title) ? esc_attr($slideshow->post_title) : esc_attr_e('Untitled slideshow', 'slideshow-se'); ?></option>
			<?php endforeach; ?>
			<?php endif; ?>
		</select>
	</p>

<?php endif; ?>