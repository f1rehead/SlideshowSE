<?php

/**
 * Render callback for the dynamic block. All this does is call the deploy function
 * of the SlideshowPlugin class.
 * 
 * @since 2.5.0
 * @param mixed $attributes
*/
function boonstra_slideshow_render_slideshow_block ( $attributes ) {
	ob_start(); // start buffering to avoid the already-sent-headers error
	SlideshowPlugin::deploy($attributes['selectedSlideshow']);
	return ob_get_clean();
}
