<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build a compact first-slide preview for the block editor dropdown confirmation UI.
 *
 * @since 2.7.2
 * @param int $slideshow_id Slideshow post ID.
 * @return array Preview data with a `type` key: attachment|text|video|empty.
 */
function f1rehead_slideshow_get_block_preview( $slideshow_id ) {
	$slideshow_id = (int) $slideshow_id;
	$empty        = array( 'type' => 'empty' );

	if ( $slideshow_id < 1 ) {
		return $empty;
	}

	$slides = SlideshowSEPluginSlideshowSettingsHandler::getSlides( $slideshow_id, false );
	if ( ! is_array( $slides ) || count( $slides ) < 1 ) {
		return $empty;
	}

	$slide = $slides[0];
	if ( ! is_array( $slide ) || empty( $slide['type'] ) ) {
		return $empty;
	}

	$type = $slide['type'];

	if ( 'attachment' === $type ) {
		$attachment_id = isset( $slide['postId'] ) ? (int) $slide['postId'] : 0;
		if ( $attachment_id < 1 ) {
			return $empty;
		}

		$image = wp_get_attachment_image_src( $attachment_id, 'large' );
		if ( ! is_array( $image ) || empty( $image[0] ) ) {
			return $empty;
		}

		$alt = '';
		if ( ! empty( $slide['alternativeText'] ) && is_string( $slide['alternativeText'] ) ) {
			$alt = $slide['alternativeText'];
		} elseif ( ! empty( $slide['title'] ) && is_string( $slide['title'] ) ) {
			$alt = $slide['title'];
		}

		$preview = array(
			'type'     => 'attachment',
			'imageUrl' => $image[0],
			'alt'      => $alt,
		);
		if ( ! empty( $image[1] ) && ! empty( $image[2] ) ) {
			$preview['width']  = (int) $image[1];
			$preview['height'] = (int) $image[2];
		}

		return $preview;
	}

	if ( 'text' === $type ) {
		$title = isset( $slide['title'] ) && is_string( $slide['title'] ) ? $slide['title'] : '';
		$description = '';
		if ( isset( $slide['description'] ) && is_string( $slide['description'] ) && $slide['description'] !== '' ) {
			$description = wp_trim_words( wp_strip_all_tags( $slide['description'] ), 24, '…' );
		}

		$background = '';
		$text_color = '';
		if ( ! empty( $slide['color'] ) ) {
			$background = SlideshowSEPluginSecurity::sanitize_slide_hex_color( $slide['color'] );
		}
		if ( ! empty( $slide['textColor'] ) ) {
			$text_color = SlideshowSEPluginSecurity::sanitize_slide_hex_color( $slide['textColor'] );
		}

		if ( $title === '' && $description === '' ) {
			return array(
				'type'            => 'text',
				'title'           => '',
				'description'     => '',
				'backgroundColor' => $background,
				'textColor'       => $text_color,
				'label'           => __( 'Text slide', 'slideshow-se' ),
			);
		}

		return array(
			'type'            => 'text',
			'title'           => $title,
			'description'     => $description,
			'backgroundColor' => $background,
			'textColor'       => $text_color,
		);
	}

	if ( 'video' === $type ) {
		$video_id = isset( $slide['videoId'] ) && is_string( $slide['videoId'] ) ? $slide['videoId'] : '';
		$image_url = '';

		if ( $video_id !== '' ) {
			$id_position = stripos( $video_id, 'v=' );
			if ( false !== $id_position ) {
				$video_id = substr( $video_id, $id_position + 2 );
				$parts    = explode( '&', $video_id );
				$video_id = isset( $parts[0] ) ? $parts[0] : '';
			}

			// Bare YouTube ids are typically 11 chars; accept a conservative pattern.
			if ( is_string( $video_id ) && preg_match( '/^[A-Za-z0-9_-]{6,32}$/', $video_id ) ) {
				$image_url = 'https://img.youtube.com/vi/' . rawurlencode( $video_id ) . '/hqdefault.jpg';
			}
		}

		$preview = array(
			'type'  => 'video',
			'label' => __( 'Video slide', 'slideshow-se' ),
		);
		if ( $image_url !== '' ) {
			$preview['imageUrl'] = $image_url;
			$preview['alt']      = __( 'Video thumbnail', 'slideshow-se' );
		}

		return $preview;
	}

	return $empty;
}

/**
 * Render callback for the dynamic block. All this does is call the deploy function
 * of the SlideshowSEPlugin class.
 *
 * @since 2.5.0
 * @param mixed $attributes
 */
function f1rehead_slideshow_render_slideshow_block( $attributes ) {
	$raw = isset( $attributes['selectedSlideshow'] ) ? $attributes['selectedSlideshow'] : '';

	if ( $raw === '' || $raw === null || ! is_numeric( $raw ) || (int) $raw < 1 ) {
		return '<p class="f1rehead-slideshow-block-empty wp-block-f1rehead-slideshow">' . esc_html__( 'Select a slideshow.', 'slideshow-se' ) . '</p>';
	}

	ob_start();
	SlideshowSEPlugin::deploy( (int) $raw );
	return ob_get_clean();
}
