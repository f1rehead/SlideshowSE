<?php

/**
 * Render callback for the dynamic block. All this does is call the deploy function
 * of the SlideshowSEPlugin class.
 * 
 * @since 2.5.0
 * @param mixed $attributes
*/
function f1rehead_slideshow_render_slideshow_block ( $attributes ) {
	ob_start(); // start buffering to avoid the already-sent-headers error
	$slideshow = null;
	if (isset($attributes['selectedSlideshow'])) {
		$slideshow = $attributes['selectedSlideshow'];
	}
	SlideshowSEPlugin::deploy($slideshow);
	return ob_get_clean();
}
