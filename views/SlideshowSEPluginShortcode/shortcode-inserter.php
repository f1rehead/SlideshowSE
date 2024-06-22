<?php if ($data instanceof stdClass) : ?>

	<a
		href="#TB_inline?width=450&inlineId=insertSlideshowShortcode"
		class="button thickbox"
		title="<?php esc_attr_e('Insert a Slideshow', 'slideshow-se'); ?>"
	    style="padding-left: .4em;"
	>
		<img
			src="<?php echo esc_url(SlideshowSEPluginMain::getPluginUrl() . '/images/SlideshowSEPluginPostType/adminIcon.png'); ?>"
			alt="<?php esc_attr_e('Insert a Slideshow', 'slideshow-se'); ?>"
		    style="vertical-align: text-top;"
		/>
		<?php esc_attr_e('Insert Slideshow', 'slideshow-se'); ?>
	</a>

	<div id="insertSlideshowShortcode" style="display: none;">

		<h3 style="padding: 10px 0; color: #5a5a5a;">
			<?php esc_attr_e('Insert a Slideshow', 'slideshow-se'); ?>
		</h3>

		<div style="border: 1px solid #ddd; padding: 10px; color: #5a5a5a;">

			<?php if($data->slideshows instanceof WP_Query && count($data->slideshows->get_posts()) > 0): ?>
			<table>
				<tr>

					<td><?php esc_attr_e('Select a slideshow', 'slideshow-se'); ?></td>
					<td>
						<select id="insertSlideshowShortcodeSlideshowSelect">

							<?php foreach($data->slideshows->get_posts() as $slideshow): ?>

							<?php if(!is_numeric($slideshow->ID)) continue; ?>

							<option value="<?php echo esc_textarea($slideshow->ID); ?>">
								<?php echo (!empty($slideshow->post_title)) ? esc_attr($slideshow->post_title) : esc_attr_e('Untitled slideshow', 'slideshow-se'); ?>
							</option>

							<?php endforeach; ?>

						</select>
					</td>

				</tr>
				<tr>

					<td>
						<input
							type="button"
							class="button-primary insertSlideshowShortcodeSlideshowInsertButton"
							value="<?php esc_attr_e('Insert Slideshow', 'slideshow-se'); ?>"
						/>
						<input
							type="button"
							class="button insertSlideshowShortcodeCancelButton"
						    value="<?php esc_attr_e('Cancel', 'slideshow-se'); ?>"
						/>
					</td>

				</tr>
			</table>

			<?php else: ?>

			<p>
				<?php echo esc_url(sprintf(
					/* translators: %s: URL to create a new slideshow */
					__('It seems you haven\'t created any slideshows yet. %1$sYou can create a slideshow here!%2$s', 'slideshow-se'),
					'<a href="' . admin_url('post-new.php?post_type=' . SlideshowSEPluginPostType::$postType) . '" target="_blank">',
					'</a>'
				)); ?>
			</p>

			<?php endif; ?>

	    </div>
	</div>
<?php endif; ?>