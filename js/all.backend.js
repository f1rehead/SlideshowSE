/**
 * Slideshow backend script
 *
 * @author Stefan Boonstra
 * @version 2.2.12
 */
import './backend/backend-namespace.js';
import './backend/generalSettings.js';
import './backend/generalSettings.navigation.js';
import './backend/generalSettings.customStyles.js';
import './backend/editSlideshow.js';
import './backend/editSlideshow.slideManager.js';
import './backend/shortcode.js';

(function () {
	var $ = jQuery,
		self = window.slideshow_jquery_image_gallery_backend_script;

	self.isBackendInitialized = false;

	/**
	 * Triggers slideshowBackendReady on the document so backend modules can start.
	 */
	self.init = function () {
		if (self.isBackendInitialized) {
			return;
		}

		self.isBackendInitialized = true;

		$(document).trigger('slideshowBackendReady');
	};

	$(document).ready(self.init);

	$(window).on('load', self.init);
})();
