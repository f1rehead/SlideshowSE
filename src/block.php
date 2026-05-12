<?php

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
