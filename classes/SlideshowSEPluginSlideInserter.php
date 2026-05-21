<?php
/**
 * Class SlideshowSEPluginSlideInserter
 *
 * @since 2.0.0
 * @author Stefan Boonstra
 */
class SlideshowSEPluginSlideInserter
{
	/**
	 * Returns a list of element tags, without special characters.
	 *
	 * @since 2.2.20
	 * @return array $elementTags
	 */
	static function getElementTags()
	{
		return array(
			0 => 'div',
			1 => 'p',
			2 => 'h1',
			3 => 'h2',
			4 => 'h3',
			5 => 'h4',
			6 => 'h5',
			7 => 'h6',
		);
	}

	/**
	 * Get a specific element tag by its ID. If no ID is passed, the first value in the element tags array will be
	 * returned.
	 *
	 * @since 2.2.20
	 * @param int $id
	 * @return array $elementTags
	 */
	static function getElementTag($id = null)
	{
		$elementTags = self::getElementTags();

		if (isset($elementTags[$id]))
		{
			return $elementTags[$id];
		}

		return reset($elementTags);
	}

	/**
	 * Editor ID used in the hidden text-slide template (cloned by JS before wp.editor.initialize).
	 *
	 * @since 2.7.0
	 * @return string
	 */
	static function getTextSlideTemplateEditorId()
	{
		return 'slideshow-text-description-template';
	}

	/**
	 * TinyMCE toolbar settings shared by PHP wp_editor() and JS wp.editor.initialize().
	 *
	 * @since 2.7.0
	 * @return array<string, string>
	 */
	static function getDescriptionEditorTinyMceSettings()
	{
		return array(
			'toolbar1' => 'formatselect | bold italic underline | bullist numlist | blockquote | alignleft aligncenter alignright | link | wp_add_media toggle fullscreen',
			'toolbar2' => 'strikethrough hr | forecolor backcolor | pastetext removeformat charmap | subscript superscript | outdent indent | undo redo | wp_help',
			'toolbar3' => '',
		);
	}

	/**
	 * wp_editor() arguments for a text slide description field.
	 *
	 * @since 2.7.0
	 * @param string $textarea_name Form field name.
	 * @return array<string, mixed>
	 */
	static function getDescriptionEditorArgs($textarea_name)
	{
		return array(
			'tinymce'       => self::getDescriptionEditorTinyMceSettings(),
			'media_buttons' => false,
			'textarea_name' => $textarea_name,
			'quicktags'     => true,
			'textarea_rows' => 7,
		);
	}

	/**
	 * Settings for wp.editor.initialize() when inserting a text slide from the template.
	 *
	 * @since 2.7.0
	 * @return array<string, mixed>
	 */
	static function getDescriptionEditorJsSettings()
	{
		return array(
			'tinymce'      => self::getDescriptionEditorTinyMceSettings(),
			'quicktags'    => true,
			'mediaButtons' => false,
		);
	}

	/**
	 * Builds a unique editor element ID from a slide field name (e.g. slides[0]).
	 *
	 * @since 2.7.0
	 * @param string $slide_name Slide field prefix.
	 * @return string
	 */
	static function getDescriptionEditorIdFromSlideName($slide_name)
	{
		return 'slideshow-description-' . preg_replace('/[\W_]+/u', '', $slide_name);
	}

	/**
	 * Ensures editor scripts/styles are loaded on slideshow edit screens.
	 *
	 * @since 2.7.0
	 */
	static function enqueueDescriptionEditorAssets()
	{
		static $enqueued = false;

		if ($enqueued || !function_exists('wp_enqueue_editor'))
		{
			return;
		}

		wp_enqueue_editor();
		$enqueued = true;
	}

	/**
	 * Outputs wp_editor() for an existing text slide description.
	 *
	 * @since 2.7.0
	 * @param string $content       Current description HTML.
	 * @param string $editor_id     Unique editor element ID.
	 * @param string $textarea_name Form field name.
	 */
	static function renderDescriptionEditor($content, $editor_id, $textarea_name)
	{
		self::enqueueDescriptionEditorAssets();

		wp_editor(
			wp_kses_post($content),
			$editor_id,
			self::getDescriptionEditorArgs($textarea_name)
		);
	}

	/**
	 * Enqueues styles and scripts necessary for the media upload button.
	 *
	 * @since 2.2.12
	 */
	static function localizeScript()
	{
		// Return if function doesn't exist
		if (!function_exists('get_current_screen'))
		{
			return;
		}

        // Return when not on a slideshow edit page
        $currentScreen = get_current_screen();

        if ($currentScreen->post_type != SlideshowSEPluginPostType::$postType)
        {
            return;
        }

		self::enqueueDescriptionEditorAssets();

		wp_localize_script(
			'slideshow-se-jquery-image-gallery-backend-script',
			'slideshow_jquery_image_gallery_backend_script_editSlideshow',
			array(
				'data' => array(),
				'localization' => array(
					'confirm'       => __('Are you sure you want to delete this slide?', 'slideshow-se'),
					'uploaderTitle' => __('Insert image slide', 'slideshow-se')
				),
				'descriptionEditor' => self::getDescriptionEditorJsSettings(),
				'templateDescriptionEditorId' => self::getTextSlideTemplateEditorId(),
			)
		);
	}
}