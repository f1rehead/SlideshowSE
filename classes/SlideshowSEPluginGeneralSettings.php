<?php
/**
 * SlideshowSEPluginGeneralSettings provides a sub menu page for the slideshow post type. The general settings page is
 * the page that holds most of the slideshow's overall settings, such as user capabilities and slideshow defaults.
 *
 * @since 2.1.22
 * @author Stefan Boonstra
 */
class SlideshowSEPluginGeneralSettings
{
	/** @var bool $isCurrentPage Flag that represents whether or not the general settings page is the current page */
	static $isCurrentPage = false;

	/** @var string $settingsGroup Settings group */
	static $settingsGroup = 'slideshow-jquery-image-gallery-general-settings';

	/** @var string $stylesheetLocation Stylesheet location setting */
	static $stylesheetLocation = 'slideshow-jquery-image-gallery-stylesheet-location';

	/** @var string $enableLazyLoading Lazy loading setting */
	static $enableLazyLoading = 'slideshow-jquery-image-gallery-enable-lazy-loading';

	/** @var array $capabilities User capability settings */
	static $capabilities = array(
		'addSlideshows'    => 'slideshow-jquery-image-gallery-add-slideshows',
		'editSlideshows'   => 'slideshow-jquery-image-gallery-edit-slideshows',
		'deleteSlideshows' => 'slideshow-jquery-image-gallery-delete-slideshows'
	);

	/** @var string $defaultSettings */
	static $defaultSettings = 'slideshow-jquery-image-gallery-default-settings';
	/** @var string $defaultStyleSettings */
	static $defaultStyleSettings = 'slideshow-jquery-image-gallery-default-style-settings';

	/** @var string $customStyles List of pointers to custom style options */
	static $customStyles = 'slideshow-jquery-image-gallery-custom-styles';

	/**
	 * Initializes the slideshow post type's general settings.
	 *
	 * @since 2.1.22
	 */
	static function init()
	{
		// Only initialize in admin
		if (!is_admin())
		{
			return;
		}

		if (isset($_GET['post_type']) &&
			$_GET['post_type'] == 'slideshow' &&
			isset($_GET['page']) &&
			$_GET['page'] == 'general_settings')
		{
			self::$isCurrentPage = true;
		}

		// Register settings
		add_action('admin_init', array(__CLASS__, 'registerSettings'));
		//add_action('rest_api_init', 'registerSettings');

		// Add sub menu
		add_action('admin_menu', array(__CLASS__, 'addSubMenuPage'));

		// Localize
		add_action('admin_enqueue_scripts', array(__CLASS__, 'localizeScript'), 11);
	}

	/**
	 * Adds a sub menu page to the slideshow post type menu.
	 *
	 * @since 2.1.22
	 */
	static function addSubMenuPage()
	{
		// Return if the slideshow post type does not exist
		if(!post_type_exists(SlideshowSEPluginPostType::$postType))
		{
			return;
		}

		// Add sub menu
		add_submenu_page(
			'edit.php?post_type=' . SlideshowSEPluginPostType::$postType,
			__('General Settings', 'slideshow-se'),
			__('General Settings', 'slideshow-se'),
			'manage_options',
			'general_settings',
			array(__CLASS__, 'generalSettings')
		);
	}

	/**
	 * Shows the general settings page.
	 *
	 * @since 2.1.22
	 */
	static function generalSettings()
	{
		SlideshowSEPluginMain::outputView(__CLASS__ . DIRECTORY_SEPARATOR . 'general-settings.php');
	}

	/**
	 * Registers required settings into the WordPress settings API.
	 * Only performed when actually on the general settings page.
	 *
	 * @since 2.1.22
	 */
	static function registerSettings()
	{
		// Register settings only when the user is going through the options.php page
		$urlParts = explode('/', $_SERVER['PHP_SELF']);

		if (array_pop($urlParts) != 'options.php')
		{
			return;
		}

		// Register general settings
		register_setting(self::$settingsGroup, self::$stylesheetLocation);
		register_setting(self::$settingsGroup, self::$enableLazyLoading);

		// Register user capability settings, saving capabilities only has to be called once.
		register_setting(self::$settingsGroup, self::$capabilities['addSlideshows']);
		register_setting(self::$settingsGroup, self::$capabilities['editSlideshows']);
		register_setting(self::$settingsGroup, self::$capabilities['deleteSlideshows'], array(__CLASS__, 'saveCapabilities'));

		// Register default slideshow settings
		//register_setting(self::$settingsGroup, self::$defaultSettings, array(__CLASS__, 'saveDefaultSettings'));
		register_setting(self::$settingsGroup, self::$defaultSettings);
		register_setting(self::$settingsGroup, self::$defaultStyleSettings);

		// Register custom style settings
		register_setting(self::$settingsGroup, self::$customStyles, array(__CLASS__, 'saveCustomStyles'));
	}

	/**
	 * Localizes the general settings script. Needs to be called on the 'admin_enqueue_scripts' hook.
	 */
	static function localizeScript()
	{
		if (!self::$isCurrentPage)
		{
			return;
		}

		// Localize general settings script
		wp_localize_script(
			'slideshow-jquery-image-gallery-backend-script',
			'slideshow_jquery_image_gallery_backend_script_generalSettings',
			array(
				'data'         => array('customStylesKey' => self::$customStyles),
				'localization' => array(
					'newCustomizationPrefix' => __('New', 'slideshow-se'),
					'confirmDeleteMessage'   => __('Are you sure you want to delete this custom style?', 'slideshow-se')
				)
			)
		);
	}

	/**
	 * Returns the stylesheet location, or 'footer' when no stylesheet position has been defined yet.
	 *
	 * @since 2.2.12
	 * @return string $stylesheetLocation
	 */
	public static function getStylesheetLocation()
	{
		return get_option(SlideshowSEPluginGeneralSettings::$stylesheetLocation, 'footer');
	}

	/**
	 * Returns the lazy loading setting, which is disabled (false) by default.
	 *
	 * @since 2.3.0
	 * @return boolean $enableLazyLoading
	 */
	public static function getEnableLazyLoading()
	{
		return get_option(self::$enableLazyLoading, false) === "true";
	}

	/**
	 * Returns an array of stylesheets with its keys and respective names.
	 *
	 * Gets the version number for each stylesheet when $withVersion is set to true.
	 *
	 * When the $separateDefaultFromCustom boolean is set to true, the default stylesheets will be returned separately
	 * from the custom stylesheets.
	 *
	 * The data returned with both parameters set to 'false' will look like the following:
	 *
	 * [$stylesheetKey => $stylesheetName]
	 *
	 * With both parameters set to 'true' the returned data will be formed like this:
	 *
	 * [
	 *  default => [$stylesheetKey => [name => $stylesheetName, version => $versionNumber]],
	 *  custom => [$stylesheetKey => [name => $stylesheetName, version => $versionNumber]]
	 * ]
	 *
	 * @since 2.1.23
	 * @param boolean $withVersion (optional, defaults to false)
	 * @param boolean $separateDefaultFromCustom (optional, defaults to false)
	 * @return array $stylesheets
	 */
	static function getStylesheets($withVersion = false, $separateDefaultFromCustom = false)
	{
		// Default styles
		$defaultStyles = array(
			'style-light.css' => __('Light', 'slideshow-se'),
			'style-dark.css'  => __('Dark', 'slideshow-se')
		);

		// Loop through default stylesheets
		$stylesheetsFilePath = SlideshowSEPluginMain::getPluginPath() . DIRECTORY_SEPARATOR . 'style' . DIRECTORY_SEPARATOR . 'SlideshowSEPlugin';

		foreach ($defaultStyles as $fileName => $name)
		{
			// Check if stylesheet exists on server, don't offer it when it does not exist.
			if (!file_exists($stylesheetsFilePath . DIRECTORY_SEPARATOR . $fileName))
			{
				unset($defaultStyles[$fileName]);

				continue;
			}

			// Add version if $withVersion is true
			if($withVersion)
			{
				$defaultStyles[$fileName] = array('name' => $name, 'version' => SlideshowSEPluginMain::$version);
			}
		}

		// Get custom styles
		$customStyles = get_option(SlideshowSEPluginGeneralSettings::$customStyles, array());

		// Add version to the custom styles if $withVersion is true
		if ($withVersion)
		{
			foreach ($customStyles as $customStylesKey => $customStylesName)
			{
				$customStylesVersion = get_option($customStylesKey . '_version', false);

				if (!$customStylesVersion)
				{
					$customStylesVersion = time();
				}

				$customStyles[$customStylesKey] = array('name' => $customStylesName, 'version' => $customStylesVersion);
			}
		}

		// Return
		if ($separateDefaultFromCustom)
		{
			return array(
				'default' => $defaultStyles,
				'custom' => $customStyles
			);
		}

		return array_merge(
			$defaultStyles,
			$customStyles
		);
	}

	/**
	 * Saves capabilities, called by a callback from a registered capability setting
	 *
	 * @since 2.1.23
	 * @param String $capability
	 * @return String $capability
	 */
	static function saveCapabilities($capability)
	{
		// Verify nonce
		if (!wp_verify_nonce($_POST['_wpnonce'], self::$settingsGroup . '-options'))
		{
			return $capability;
		}

		// Roles
		global $wp_roles;

		// Loop through available user roles
		foreach ($wp_roles->roles as $roleSlug => $roleValues)
		{
			// Continue when the capabilities are either not set or are no array
			if (!is_array($roleValues) ||
				!isset($roleValues['capabilities']) ||
				!is_array($roleValues['capabilities']))
			{
				continue;
			}

			// Get role
			$role = get_role($roleSlug);

			// Continue when role is not set
			if ($role == null)
			{
				continue;
			}

			// Loop through available capabilities
			foreach (self::$capabilities as $capabilitySlug)
			{
				// If $roleSlug is present in $_POST's capability, add the capability to the role, otherwise remove the capability from the role.
				if ((isset($_POST[$capabilitySlug]) && is_array($_POST[$capabilitySlug]) && array_key_exists($roleSlug, $_POST[$capabilitySlug])) ||
					$roleSlug == 'administrator')
				{
					$role->add_cap($capabilitySlug);
				}
				else
				{
					$role->remove_cap($capabilitySlug);
				}
			}
		}

		return $capability;
	}

	/**
	 * Validate the changed settings, called by a callback from a registered setting.
	 *
	 * @since 2.5.6
	 * @param array $defaultStyles
	 * @return array $newDefaultStyles
	 */
	function saveDefaultSettings( $defaultStyles )
	{

		$animations = array(
			'slide',
			'slideRight',
			'slideUp',
			'slideDown',
			'crossFade',
			'directFade',
			'fade',
			'random'
		);

		$behaviours = array(
			'natural',
			'crop',
			'stretch'
		);

		// Verify nonce
		//$nonce = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : '';

		if (isset($_POST['_wpnonce']) && !wp_verify_nonce($nonce, self::$settingsGroup . '-options'))
		{
			return $defaultStyles;
		}

		//animation
		(in_array($defaultStyles['animation'], $animations)) ? $newDefaultStyles['animation'] = $defaultStyles['animation'] : $newDefaultStyles['animation'] = "slide"; 

		$newDefaultStyles['slideSpeed'] = filter_var($defaultStyles['slideSpeed'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		$newDefaultStyles['descriptionSpeed'] = filter_var($defaultStyles['descriptionSpeed'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		$newDefaultStyles['intervalSpeed'] = filter_var($defaultStyles['intervalSpeed'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		$newDefaultStyles['slidesPerView'] = filter_var($defaultStyles['slidesPerView'], FILTER_SANITIZE_NUMBER_INT);
		$newDefaultStyles['maxWidth'] = filter_var($defaultStyles['maxWidth'], FILTER_SANITIZE_NUMBER_INT);
		$newDefaultStyles['aspectRatio'] = preg_replace("/[^0-9\:]+/i", "", $defaultStyles['aspectRatio']);
		$newDefaultStyles['height'] = filter_var($defaultStyles['height'], FILTER_SANITIZE_NUMBER_INT);

		//imageBehaviour
		(in_array($defaultStyles['imageBehaviour'], $behaviours)) ? $newDefaultStyles['imageBehaviour'] = $defaultStyles['imageBehaviour'] : $newDefaultStyles['imageBehaviour'] = "natural"; 

		(filter_var($defaultStyles['preserveSlideshowDimensions'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === NULL) ? $newDefaultStyles['preserveSlideshowDimensions'] = TRUE : $newDefaultStyles['preserveSlideshowDimensions'] = $defaultStyles['preserveSlideshowDimensions']; 
		(filter_var($defaultStyles['enableResponsiveness'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === NULL) ? $newDefaultStyles['enableResponsiveness'] = TRUE : $newDefaultStyles['enableResponsiveness'] = $defaultStyles['enableResponsiveness']; 
		(filter_var($defaultStyles['showDescription'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === NULL) ? $newDefaultStyles['showDescription'] = TRUE : $newDefaultStyles['showDescription'] = $defaultStyles['showDescription']; 
		(filter_var($defaultStyles['hideDescription'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === NULL) ? $newDefaultStyles['hideDescription'] = TRUE : $newDefaultStyles['hideDescription'] = $defaultStyles['hideDescription']; 
		(filter_var($defaultStyles['play'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === NULL) ? $newDefaultStyles['play'] = TRUE : $newDefaultStyles['play'] = $defaultStyles['play']; 
		(filter_var($defaultStyles['loop'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === NULL) ? $newDefaultStyles['loop'] = TRUE : $newDefaultStyles['loop'] = $defaultStyles['loop']; 
		(filter_var($defaultStyles['pauseOnHover'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === NULL) ? $newDefaultStyles['pauseOnHover'] = TRUE : $newDefaultStyles['pauseOnHover'] = $defaultStyles['pauseOnHover']; 
		(filter_var($defaultStyles['controllable'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === NULL) ? $newDefaultStyles['controllable'] = TRUE : $newDefaultStyles['controllable'] = $defaultStyles['controllable']; 
		(filter_var($defaultStyles['hideNavigationButtons'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === NULL) ? $newDefaultStyles['hideNavigationButtons'] = TRUE : $newDefaultStyles['hideNavigationButtons'] = $defaultStyles['hideNavigationButtons']; 
		(filter_var($defaultStyles['showPagination'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === NULL) ? $newDefaultStyles['showPagination'] = TRUE : $newDefaultStyles['showPagination'] = $defaultStyles['showPagination']; 
		(filter_var($defaultStyles['hidePagination'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === NULL) ? $newDefaultStyles['hidePagination'] = TRUE : $newDefaultStyles['hidePagination'] = $defaultStyles['hidePagination']; 
		(filter_var($defaultStyles['controlPanel'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === NULL) ? $newDefaultStyles['controlPanel'] = TRUE : $newDefaultStyles['controlPanel'] = $defaultStyles['controlPanel']; 
		(filter_var($defaultStyles['hideControlPanel'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === NULL) ? $newDefaultStyles['hideControlPanel'] = TRUE : $newDefaultStyles['hideControlPanel'] = $defaultStyles['hideControlPanel']; 
		(filter_var($defaultStyles['waitUntilLoaded'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === NULL) ? $newDefaultStyles['waitUntilLoaded'] = TRUE : $newDefaultStyles['waitUntilLoaded'] = $defaultStyles['waitUntilLoaded']; 
		(filter_var($defaultStyles['showLoadingIcon'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === NULL) ? $newDefaultStyles['showLoadingIcon'] = TRUE : $newDefaultStyles['showLoadingIcon'] = $defaultStyles['showLoadingIcon']; 
		(filter_var($defaultStyles['random'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === NULL) ? $newDefaultStyles['random'] = TRUE : $newDefaultStyles['random'] = $defaultStyles['random']; 
		(filter_var($defaultStyles['avoidFilter'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === NULL) ? $newDefaultStyles['avoidFilter'] = TRUE : $newDefaultStyles['avoidFilter'] = $defaultStyles['avoidFilter']; 

		return $newDefaultStyles;
	}

	/**
	 * Saves custom styles, called by a callback from a registered custom styles setting
	 *
	 * @since 2.1.23
	 * @param array $customStyles
	 * @return array $newCustomStyles
	 */
	static function saveCustomStyles($customStyles)
	{
		// Verify nonce
		if (!wp_verify_nonce($_POST['_wpnonce'], self::$settingsGroup . '-options'))
		{
			return $customStyles;
		}

		// Remove custom styles that have been deleted
		$oldCustomStyles = get_option(self::$customStyles, array());

		if (is_array($oldCustomStyles))
		{
			foreach ($oldCustomStyles as $oldCustomStyleKey => $oldCustomStyleValue)
			{
				// Delete option from database if it no longer exists
				if (!array_key_exists($oldCustomStyleKey, $customStyles))
				{
					delete_option($oldCustomStyleKey);
				}
			}
		}

		// Loop through new custom styles
		$newCustomStyles = array();

		if (is_array($customStyles))
		{
			foreach ($customStyles as $customStyleKey => $customStyleValue)
			{
				// Put custom style key and name into the $newCustomStyle array
				$newCustomStyles[$customStyleKey] = isset($customStyleValue['title']) ? $customStyleValue['title'] : __('Untitled', 'slideshow-se');

				// Get style
				$newStyle = isset($customStyleValue['style']) ? $customStyleValue['style'] : '';

				// Create or update new custom style
				$oldStyle = get_option($customStyleKey, false);

				if ($oldStyle)
				{
					// Check if style has changed
					if ($oldStyle !== $newStyle)
					{
						update_option($customStyleKey, $newStyle);
						update_option($customStyleKey . '_version', time());
					}
				}
				else
				{
					// The custom style itself shouldn't be auto-loaded, it's never used within WordPress
					add_option($customStyleKey, $newStyle, '', 'no');
					add_option($customStyleKey . '_version', time());
				}
			}
		}

		// Return
		return $newCustomStyles;
	}

}