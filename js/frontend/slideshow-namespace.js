/**
 * Ensures the global namespace exists before slideshow submodule bundles run.
 * ES module imports are hoisted/evaluated before the main frontend IIFE, so this file must load first.
 */
window.slideshow_jquery_image_gallery_script =
	window.slideshow_jquery_image_gallery_script || {};
