<?php
/**
 * SlideshowSEPluginSlideshowStylesheet handles the loading of the slideshow's stylesheets.
 *
 * @since 2.2.8
 * @author Stefan Boonstra
 */
class SlideshowSEPluginSlideshowStylesheet
{
	/** @var bool $allStylesheetsRegistered */
	public static $allStylesheetsRegistered = false;

	/**
	 * Initializes the SlideshowSEPluginSlideshowStylesheet class
	 *
	 * @since 2.2.12
	 */
	public static function init()
	{
		add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueueFrontendStylesheets'));
	}

	/**
	 * Enqueue stylesheet
	 */
	public static function enqueueFrontendStylesheets()
	{
		if (SlideshowSEPluginGeneralSettings::getStylesheetLocation() === 'head')
		{
			// Register functional stylesheet
			wp_enqueue_style(
				'slideshow-jquery-image-gallery-stylesheet_functional',
				SlideshowSEPluginMain::getPluginUrl() . '/style/SlideshowSEPlugin/functional.css',
				array(),
				SlideshowSEPluginMain::$version
			);

			// Get default and custom stylesheets
			$stylesheets        = SlideshowSEPluginGeneralSettings::getStylesheets(true, true);
			$defaultStylesheets = $stylesheets['default'];
			$customStylesheets  = $stylesheets['custom'];

			// Clean the '.css' extension from the default stylesheets
			foreach ($defaultStylesheets as $defaultStylesheetKey => $defaultStylesheetValue)
			{
				$newDefaultStylesheetKey = str_replace('.css', '', $defaultStylesheetKey);

				$defaultStylesheets[$newDefaultStylesheetKey] = $defaultStylesheetValue;

				if ($defaultStylesheetKey !== $newDefaultStylesheetKey)
				{
					unset($defaultStylesheets[$defaultStylesheetKey]);
				}
			}

			// Enqueue stylesheets
			foreach (array_merge($defaultStylesheets, $customStylesheets) as $stylesheetKey => $stylesheetValue)
			{
				wp_enqueue_style(
					'slideshow-jquery-image-gallery-ajax-stylesheet_' . $stylesheetKey,
					admin_url('admin-ajax.php?action=slideshow_jquery_image_gallery_load_stylesheet&style=' . $stylesheetKey, 'admin'),
					array(),
					$stylesheetValue['version']
				);
			}

			self::$allStylesheetsRegistered = true;
		}
	}

	/**
	 * Enqueues a stylesheet based on the stylesheet's name. This can either be a default stylesheet or a custom one.
	 * If the name parameter is left unset, the default stylesheet will be used.
	 *
	 * Returns the name and version number of the stylesheet that's been enqueued, as this can be different from the
	 * name passed. This can be this case if a stylesheet does not exist and a default stylesheet is enqueued.
	 *
	 * @param string $name (optional, defaults to null)
	 * @return array [$name, $version]
	 */
	public static function enqueueStylesheet($name = null)
	{
		$enqueueDynamicStylesheet = true;
		$version                  = SlideshowSEPluginMain::$version;

		if (isset($name))
		{
			// Try to get the custom style's version
			$customStyle        = get_option($name, false);
			$customStyleVersion = false;

			if ($customStyle)
			{
				$customStyleVersion = get_option($name . '_version', false);
			}

			// Style name and version
			if ($customStyle && $customStyleVersion)
			{
				$version = $customStyleVersion;
			}
			else
			{
				$enqueueDynamicStylesheet = false;
				$name                     = str_replace('.css', '', $name);
			}
		}
		else
		{
			$enqueueDynamicStylesheet = false;
			$name                     = 'style-light';
		}

		// Enqueue stylesheet
		if (SlideshowSEPluginGeneralSettings::getStylesheetLocation() == 'footer')
		{
			add_filter('style_loader_tag', array(__CLASS__, 'filterStyleLoaderTag'), 10, 3);

			if ($enqueueDynamicStylesheet) {
				wp_enqueue_style(
					'slideshow-jquery-image-gallery-ajax-stylesheet_' . $name,
					admin_url('admin-ajax.php?action=slideshow_jquery_image_gallery_load_stylesheet&style=' . $name, 'admin'),
					array(),
					$version,
					false
				);
			} else {
				wp_enqueue_style(
					'slideshow-jquery-image-gallery-stylesheet_' . $name,
					SlideshowSEPluginMain::getPluginUrl() . '/css/' . $name . '.css',
					array(),
					$version,
					false
				);
			}
		}

		return array($name, $version);
	}

	/**
	 * Called through WordPress' admin-ajax.php script, registered in the SlideshowSEPluginAJAX class. This function
	 * must not be called on itself.
	 *
	 * Uses the loadStylesheet function to load the stylesheet passed in the URL data. If no stylesheet name is set, all
	 * stylesheets will be loaded.
	 *
	 * Headers are set to allow file caching.
	 *
	 * @since 2.2.11
	 */
	public static function loadStylesheetByAJAX()
	{
		$styleName = isset($_GET['style']) ? sanitize_text_field(wp_unslash($_GET['style'])) : '';

		if ($styleName === '')
		{
			return;
		}

		$stylesheet = self::getStylesheet($styleName);

		if (false === $stylesheet)
		{
			status_header(404);
			nocache_headers();
			header('Content-Type: text/css; charset=UTF-8');
			echo '/* Slideshow SE: unknown stylesheet */';
			die;
		}

		// Exit if headers have already been sent
		if (headers_sent())
		{
			return;
		}

		// Set header to CSS. Cache for a year (as WordPress does)
		header('Content-Type: text/css; charset=UTF-8');
		header('Expires: ' . gmdate("D, d M Y H:i:s", time() + 31556926) . ' GMT');
		header('Pragma: cache');
		header("Cache-Control: public, max-age=31556926");

		// Raw CSS from disk or from a trusted admin-only option; not HTML.
		echo "\n" . wp_kses_post($stylesheet);

		die;
	}

	/**
	 * Gets the stylesheet with the parsed style name, then returns it.
	 *
	 * @since 2.2.8
	 * @param string $styleName Requested style key or bundled file token.
	 * @return string|false CSS string on success (may be empty), false if unknown or unsafe.
	 */
	public static function getStylesheet($styleName)
	{
		if (!is_string($styleName) || $styleName === '')
		{
			return false;
		}

		// Get custom style keys (option names); only those may load arbitrary CSS from options.
		$customStyleKeys = array_keys((array) get_option(SlideshowSEPluginGeneralSettings::$customStyles, array()));

		// Match $styleName against custom style keys
		if (in_array($styleName, $customStyleKeys, true))
		{
			$stylesheet = get_option($styleName, '');

			if (!is_string($stylesheet))
			{
				$stylesheet = '';
			}
		}
		else
		{
			// Bundled files: basename-style token only (no path segments).
			$fileToken = $styleName;

			if (strlen($fileToken) > 4 && substr($fileToken, -4) === '.css')
			{
				$fileToken = substr($fileToken, 0, -4);
			}

			if (1 !== preg_match('/^[A-Za-z0-9_-]+$/', $fileToken))
			{
				return false;
			}

			$styleDir = realpath(
				SlideshowSEPluginMain::getPluginPath() . DIRECTORY_SEPARATOR . 'style' . DIRECTORY_SEPARATOR . 'SlideshowSEPlugin'
			);

			if ($styleDir === false)
			{
				return false;
			}

			$styleDirNorm = wp_normalize_path($styleDir);

			$stylesheetFile = realpath($styleDir . DIRECTORY_SEPARATOR . $fileToken . '.css');
			$fileNorm        = ($stylesheetFile !== false) ? wp_normalize_path($stylesheetFile) : false;

			if ($fileNorm === false ||
				strpos($fileNorm, $styleDirNorm) !== 0)
			{
				$stylesheetFile = realpath($styleDir . DIRECTORY_SEPARATOR . 'style-light.css');
				$fileNorm       = ($stylesheetFile !== false) ? wp_normalize_path($stylesheetFile) : false;
			}

			if ($fileNorm === false ||
				strpos($fileNorm, $styleDirNorm) !== 0)
			{
				return false;
			}

			$stylesheet = file_get_contents($stylesheetFile);

			if ($stylesheet === false)
			{
				return false;
			}
		}

		// Replace the URL placeholders with actual URLs and add a unique identifier to separate stylesheets
		$stylesheet = str_replace('%plugin-url%', SlideshowSEPluginMain::getPluginUrl(), $stylesheet);
		$stylesheet = str_replace('%site-url%', get_bloginfo('url'), $stylesheet);
		$stylesheet = str_replace('%stylesheet-url%', get_stylesheet_directory_uri(), $stylesheet);
		$stylesheet = str_replace('%template-url%', get_template_directory_uri(), $stylesheet);
		$stylesheet = str_replace('.slideshow_container', '.slideshow_container_' . $styleName, $stylesheet);

		return $stylesheet;
	}

	/**
	 * Called on "style_loader_tag" filter. Adds property="stylesheet" to HTML tag to allow scripts to be loaded in the
	 * website's footer.
	 *
	 * @since 2.3.2
	 * @param $tag
	 * @param $handle
	 * @param $href
	 * @return String $tag
	 */
	public static function filterStyleLoaderTag($tag, $handle, $href)
	{
		if (strpos($tag, "property") !== false)
		{
			return $tag;
		}

		if (preg_match("/slideshow-jquery-image-gallery-(ajax-)?stylesheet_/", $handle))
		{
			$position = strpos($tag, 'link') + 4;

			if ($position !== false)
			{
				$tag = substr($tag, 0, $position) . ' property="stylesheet" ' . substr($tag, $position);
			}
		}

		return $tag;
	}
}
