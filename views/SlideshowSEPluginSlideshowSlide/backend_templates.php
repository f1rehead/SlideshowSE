<?php if ($data instanceof stdClass) : ?>
	<div class="text-slide-template" style="display: none;">
		<div class="widefat sortable-slides-list-item postbox">

			<div class="handlediv" title="<?php esc_attr_e('Click to toggle'); ?>"><br></div>

			<div class="hndle">
				<div class="slide-icon text-slide-icon"></div>
				<div class="slide-title">
					<?php esc_attr_e('Text slide', 'slideshow-se'); ?>
				</div>
				<div class="clear"></div>
			</div>

			<div class="inside">

				<div class="slideshow-group">

					<div class="slideshow-left slideshow-label"><?php esc_attr_e('Title', 'slideshow-se'); ?></div>
					<div class="slideshow-right">
						<select class="titleElementTagID">
							<?php foreach (SlideshowSEPluginSlideInserter::getElementTags() as $elementTagID => $elementTag): ?>
								<option value="<?php echo esc_attr($elementTagID); ?>"><?php echo esc_attr($elementTag); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="clear"></div>
					<input type="text" class="title" style="width: 100%;" />

				</div>

				<div class="slideshow-group">

					<div class="slideshow-left slideshow-label"><?php esc_attr_e('Description', 'slideshow-se'); ?></div>
					<div class="slideshow-right">
						<select class="descriptionElementTagID">
							<?php foreach (SlideshowSEPluginSlideInserter::getElementTags() as $elementTagID => $elementTag): ?>
								<option value="<?php echo esc_attr($elementTagID); ?>"><?php echo esc_attr($elementTag); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div clear="clear"></div>
					<textarea class="description" cols="" rows="7" style="width: 100%;"></textarea>

				</div>

				<div class="slideshow-group">

					<div class="slideshow-label"><?php esc_attr_e('Text color', 'slideshow-se'); ?></div>
					<input type="text" class="textColor" value="000000" />

					<div class="slideshow-label"><?php esc_attr_e('Background color', 'slideshow-se'); ?></div>
					<input type="text" class="color" value="FFFFFF" />
					<div style="font-style: italic;"><?php esc_attr_e('(Leave empty for a transparent background)', 'slideshow-se'); ?></div>

				</div>

				<div class="slideshow-group">

					<div class="slideshow-label"><?php esc_attr_e('URL', 'slideshow-se'); ?></div>
					<input type="text" class="url" value="" style="width: 100%;" />

					<div class="slideshow-label slideshow-left"><?php esc_attr_e('Open URL in', 'slideshow-se'); ?></div>
					<select class="urlTarget slideshow-right">
						<option value="_self"><?php esc_attr_e('Same window', 'slideshow-se'); ?></option>
						<option value="_blank"><?php esc_attr_e('New window', 'slideshow-se'); ?></option>
					</select>
					<div class="clear"></div>

					<div class="slideshow-label slideshow-left"><?php esc_attr_e('Don\'t let search engines follow link', 'slideshow-se'); ?></div>
		            <input type="checkbox" class="noFollow slideshow-right" />
					<div class="clear"></div>

		        </div>

				<div class="slideshow-group slideshow-delete-slide">
					<span><?php esc_attr_e('Delete slide', 'slideshow-se'); ?></span>
				</div>

				<input type="hidden" class="type" value="text" />

			</div>

		</div>
	</div>

	<div class="video-slide-template" style="display: none;">
		<div class="widefat sortable-slides-list-item postbox">

			<div class="handlediv" title="<?php esc_attr_e('Click to toggle'); ?>"><br></div>

			<div class="hndle">
				<div class="slide-icon video-slide-icon"></div>
				<div class="slide-title">
					<?php esc_attr_e('Video slide', 'slideshow-se'); ?>
				</div>
				<div class="clear"></div>
			</div>

			<div class="inside">

				<div class="slideshow-group">

					<div class="slideshow-label"><?php esc_attr_e('Youtube Video ID', 'slideshow-se'); ?></div>
					<input type="text" class="videoId" style="width: 100%;" />

				</div>

				<div class="slideshow-group">

					<div class="slideshow-label"><?php esc_attr_e('Show related videos', 'slideshow-se'); ?></div>
					<label><input type="radio" class="showRelatedVideos" value="true"><?php esc_attr_e('Yes', 'slideshow-se'); ?></label>
					<label><input type="radio" class="showRelatedVideos" value="false" checked="checked"><?php esc_attr_e('No', 'slideshow-se'); ?></label>

				</div>

				<div class="slideshow-group slideshow-delete-slide">
					<span><?php esc_attr_e('Delete slide', 'slideshow-se'); ?></span>
				</div>

				<input type="hidden" class="type" value="video" />

			</div>

		</div>
	</div>

	<div class="embed-slide-template" style="display: none;">
		<div class="widefat sortable-slides-list-item postbox">

			<div class="handlediv" title="<?php esc_attr_e('Click to toggle'); ?>"><br></div>

			<div class="hndle">
				<div class="slide-icon embed-slide-icon"></div>
				<div class="slide-title">
					<?php esc_attr_e('Embedded Media slide', 'slideshow-se'); ?>
				</div>
				<div class="clear"></div>
			</div>

			<div class="inside">

				<div class="slideshow-group">

					<div class="slideshow-label"><?php esc_attr_e('Media URL to embed', 'slideshow-se'); ?></div>
					<input type="text" class="mediaURL" style="width: 100%;" />

				</div>

				<div class="slideshow-group">

				</div>

				<div class="slideshow-group slideshow-delete-slide">
					<span><?php esc_attr_e('Delete slide', 'slideshow-se'); ?></span>
				</div>

				<input type="hidden" class="type" value="embed" />

			</div>

		</div>
	</div>

	<div class="image-slide-template" style="display: none;">
		<div class="widefat sortable-slides-list-item postbox">

			<div class="handlediv" title="<?php esc_attr_e('Click to toggle'); ?>"><br></div>

			<div class="hndle">
				<div class="slide-icon image-slide-icon"></div>
				<div class="slide-title">
					<?php esc_attr_e('Image slide', 'slideshow-se'); ?>
				</div>
				<div class="clear"></div>
			</div>

			<div class="inside">

				<div class="slideshow-group">

					<img width="80" height="60" src="" class="attachment attachment-80x60" alt="" title="" style="float: none; margin: 0; padding: 0;" />

				</div>

				<div class="slideshow-group">

					<div class="slideshow-left slideshow-label"><?php esc_attr_e('Title', 'slideshow-se'); ?></div>
					<div class="slideshow-right">
						<select class="titleElementTagID">
							<?php foreach (SlideshowSEPluginSlideInserter::getElementTags() as $elementTagID => $elementTag): ?>
								<option value="<?php echo esc_attr($elementTagID); ?>"><?php echo esc_attr($elementTag); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="clear"></div>
					<input type="text" class="title" style="width: 100%;" />

				</div>

				<div class="slideshow-group">

					<div class="slideshow-left slideshow-label"><?php esc_attr_e('Description', 'slideshow-se'); ?></div>
					<div class="slideshow-right">
						<select class="descriptionElementTagID">
							<?php foreach (SlideshowSEPluginSlideInserter::getElementTags() as $elementTagID => $elementTag): ?>
								<option value="<?php echo esc_attr($elementTagID); ?>"><?php echo esc_attr($elementTag); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="clear"></div>
					<textarea class="description" rows="3" cols="" style="width: 100%;"></textarea><br />

				</div>

				<div class="slideshow-group">

					<div class="slideshow-label"><?php esc_attr_e('URL', 'slideshow-se'); ?></div>
					<input type="text" class="url" value="" style="width: 100%;" /><br />

					<div class="slideshow-label slideshow-left"><?php esc_attr_e('Open URL in', 'slideshow-se'); ?></div>
					<select class="urlTarget slideshow-right">
						<option value="_self"><?php esc_attr_e('Same window', 'slideshow-se'); ?></option>
						<option value="_blank"><?php esc_attr_e('New window', 'slideshow-se'); ?></option>
					</select>
					<div class="clear"></div>

					<div class="slideshow-label slideshow-left"><?php esc_attr_e('Don\'t let search engines follow link', 'slideshow-se'); ?></div>
		            <input type="checkbox" class="noFollow slideshow-right" />

		        </div>

				<div class="slideshow-group">

					<div class="slideshow-label"><?php esc_attr_e('Alternative text', 'slideshow-se'); ?></div>
					<input type="text" class="alternativeText" style="width: 100%;" />

				</div>

				<div class="slideshow-group slideshow-delete-slide">
					<span><?php esc_attr_e('Delete slide', 'slideshow-se'); ?></span>
				</div>

				<input type="hidden" class="type" value="attachment" />
				<input type="hidden" class="postId" value="" />

			</div>

		</div>
	</div>
<?php endif; ?>