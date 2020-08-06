<?php if ($data instanceof stdClass) : ?>

	<a
		href="#TB_inline?width=450&inlineId=insertSlideshowShortcode"
		class="button thickbox"
		title="<?php _e('Insert a Slideshow', 'slideshow-se'); ?>"
	    style="padding-left: .4em;"
	>
		<img
			src="<?php echo SlideshowSEPluginMain::getPluginUrl() . '/images/SlideshowSEPluginPostType/adminIcon.png'; ?>"
			alt="<?php _e('Insert a Slideshow', 'slideshow-se'); ?>"
		    style="vertical-align: text-top;"
		/>
		<?php _e('Insert Slideshow', 'slideshow-se'); ?>
	</a>

	<div id="insertSlideshowShortcode" style="display: none;">

		<h3 style="padding: 10px 0; color: #5a5a5a;">
			<?php _e('Insert a Slideshow', 'slideshow-se'); ?>
		</h3>

		<div style="border: 1px solid #ddd; padding: 10px; color: #5a5a5a;">

			<?php if($data->slideshows instanceof WP_Query && count($data->slideshows->get_posts()) > 0): ?>
			<table>
				<tr>

					<td><?php _e('Select a slideshow', 'slideshow-se'); ?></td>
					<td>
						<select id="insertSlideshowShortcodeSlideshowSelect">

							<?php foreach($data->slideshows->get_posts() as $slideshow): ?>

							<?php if(!is_numeric($slideshow->ID)) continue; ?>

							<option value="<?php echo $slideshow->ID; ?>">
								<?php echo (!empty($slideshow->post_title)) ? htmlspecialchars($slideshow->post_title) : __('Untitled slideshow', 'slideshow-se'); ?>
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
							value="<?php _e('Insert Slideshow', 'slideshow-se'); ?>"
						/>
						<input
							type="button"
							class="button insertSlideshowShortcodeCancelButton"
						    value="<?php _e('Cancel', 'slideshow-se'); ?>"
						/>
					</td>

				</tr>
			</table>

			<?php else: ?>

			<p>
				<?php echo sprintf(
					__('It seems you haven\'t created any slideshows yet. %1$sYou can create a slideshow here!%2$s', 'slideshow-se'),
					'<a href="' . admin_url('post-new.php?post_type=' . SlideshowSEPluginPostType::$postType) . '" target="_blank">',
					'</a>'
				); ?>
			</p>

			<?php endif; ?>

	    </div>
	</div>
<?php endif; ?>