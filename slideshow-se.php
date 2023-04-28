<?php
/*
 Plugin Name: Slideshow SE
 Plugin URI: http://wordpress.org/extend/plugins/slideshow-se/
 Description: The slideshow plugin is easily deployable on your website. Add any image that has already been uploaded to add to your slideshow, add text slides, or even add a video. Options and styles are customizable for every single slideshow on your website.
 Version: 2.5.10
 Requires at least: 5.0
 Tested up to: 6.2
 Requires PHP: 5.0
 Author: John West
 License: GPLv2
 Text Domain: slideshow-se
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class SlideshowSEPluginMain fires up the application on plugin load and provides some
 * methods for the other classes to use like the auto-includer and the
 * base path/url returning method.
 *
 * @since 1.0.0
 * @author Stefan Boonstra
 */
class SlideshowSEPluginMain
{
	/** @var string $version */
	static $version = '2.5.10';

	/**
	 * Bootstraps the application by assigning the right functions to
	 * the right action hooks.
	 *
	 * @since 1.0.0
	 */
	static function bootStrap()
	{
		self::autoInclude();

		// Initialize localization on init
		add_action('init', array(__CLASS__, 'localize'));
		// Initialize the Gutenberg block
		add_action( 'init', 'f1rehead_slideshow_block_init' );

		// Enqueue hooks
		add_action('wp_enqueue_scripts'   , array(__CLASS__, 'enqueueFrontendScripts'));
		add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueueBackendScripts'));

		// Ajax requests
		SlideshowSEPluginAJAX::init();

		// Register slideshow post type
		SlideshowSEPluginPostType::init();

		// Add general settings page
		SlideshowSEPluginGeneralSettings::init();

		// Initialize stylesheet builder
		SlideshowSEPluginSlideshowStylesheet::init();

		// Deploy slideshow on do_action('slideshow_deploy'); hook.
		add_action('slideshow_deploy', array('SlideshowSEPlugin', 'deploy'));

		// Initialize shortcode
		SlideshowSEPluginShortcode::init();

		// Register widget
		add_action('widgets_init', array('SlideshowSEPluginWidget', 'registerWidget'));

		// Initialize plugin updater
		SlideshowSEPluginInstaller::init();
	}

	/**
	 * Enqueues frontend scripts and styles.
	 *
	 * Should always be called on the wp_enqueue_scripts hook.
	 *
	 * @since 2.3.0
	 */
	static function enqueueFrontendScripts()
	{
		// Enqueue slideshow script if lazy loading is enabled
		if (SlideshowSEPluginGeneralSettings::getEnableLazyLoading())
		{
			wp_enqueue_script(
				'slideshow-jquery-image-gallery-script',
				self::getPluginUrl() . '/js/min/all.frontend.min.js',
				array('jquery'),
				self::$version
			);

			wp_localize_script(
				'slideshow-jquery-image-gallery-script',
				'slideshow_jquery_image_gallery_script_adminURL',
				admin_url()
			);
		}
	}

	/**
	 * Enqueues backend scripts and styles.
	 *
	 * Should always be called on the admin_enqueue_scrips hook.
	 *
	 * @since 2.2.12
	 */
	static function enqueueBackendScripts()
	{
		// Function get_current_screen() should be defined, as this method is expected to fire at 'admin_enqueue_scripts'
		if (!function_exists('get_current_screen'))
		{
			return;
		}

		$currentScreen = get_current_screen();

		// Enqueue 3.5 uploader
		if ($currentScreen->post_type === 'slideshow' &&
			function_exists('wp_enqueue_media'))
		{
			wp_enqueue_media();
		}

		wp_enqueue_script(
			'slideshow-se-jquery-image-gallery-backend-script',
			self::getPluginUrl() . '/js/min/all.backend.min.js',
			array(
				'jquery',
				'jquery-ui-sortable',
				'wp-color-picker'
			),
			SlideshowSEPluginMain::$version
		);

		wp_enqueue_style(
			'slideshow-se-jquery-image-gallery-backend-style',
			self::getPluginUrl() . '/css/all.backend.css',
			array(
				'wp-color-picker'
			),
			SlideshowSEPluginMain::$version
		);
	}

	/**
	 * Translates the plugin
	 *
	 * @since 1.0.0
	 */
	static function localize()
	{
		load_plugin_textdomain(
			'slideshow-se',
			false,
			dirname(plugin_basename(__FILE__)) . '/languages/'
		);
	}

	/**
	 * Returns url to the base directory of this plugin.
	 *
	 * @since 1.0.0
	 * @return string pluginUrl
	 */
	static function getPluginUrl()
	{
		return plugins_url('', __FILE__);
	}

	/**
	 * Returns path to the base directory of this plugin
	 *
	 * @since 1.0.0
	 * @return string pluginPath
	 */
	static function getPluginPath()
	{
		return dirname(__FILE__);
	}

	/**
	 * Outputs the passed view. It's good practice to pass an object like an stdClass to the $data variable, as it can
	 * be easily checked for validity in the view itself using "instanceof".
	 *
	 * @since 2.3.0
	 * @param string   $view
	 * @param stdClass $data (Optional, defaults to stdClass)
	 */
	static function outputView($view, $data = null)
	{
		if (!($data instanceof stdClass))
		{
			$data = new stdClass();
		}

		$file = self::getPluginPath() . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $view;

		if (file_exists($file))
		{
			include $file;
		}
	}

	/**
	 * Uses self::outputView to render the passed view. Returns the rendered view instead of outputting it.
	 *
	 * @since 2.3.0
	 * @param string   $view
	 * @param stdClass $data (Optional, defaults to null)
	 * @return string
	 */
	static function getView($view, $data = null)
	{
		ob_start();
		self::outputView($view, $data);
		return ob_get_clean();
	}

	/**
	 * This function will load classes automatically on-call.
	 *
	 * @since 1.0.0
	 */
	static function autoInclude()
	{
		if (!function_exists('spl_autoload_register'))
		{
			return;
		}

		function SlideshowSEPluginAutoLoader($name)
		{
			$name = str_replace('\\', DIRECTORY_SEPARATOR, $name);
			$file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . $name . '.php';

			if (is_file($file))
			{
				require_once $file;
			}
		}
		// Don't forget the render callback for the Gutenberg block
		require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'block.php';
		spl_autoload_register('SlideshowSEPluginAutoLoader');
	}
}

/**
 * Registers all block assets so that they can be enqueued through the block editor
 * in the corresponding context.
 *
 * @see https://developer.wordpress.org/block-editor/tutorials/block-tutorial/applying-styles-with-stylesheets/
 */
function f1rehead_slideshow_block_init() {
	$dir = dirname( __FILE__ );

	$script_asset_path = "$dir/block/index.asset.php";
	if ( ! file_exists( $script_asset_path ) ) {
		throw new Error(
			'You need to run `npm start` or `npm run build` for the "f1rehead/slideshow" block first.'
		);
	}
	$index_js     = 'block/index.js';
	$script_asset = require( $script_asset_path );
	wp_register_script(
		'f1rehead-slideshow-block-editor',
		plugins_url( $index_js, __FILE__ ),
		$script_asset['dependencies'],
		$script_asset['version']
	);

	$editor_css = 'block/index.css';
	wp_register_style(
		'f1rehead-slideshow-block-editor',
		plugins_url( $editor_css, __FILE__ ),
		array(),
		filemtime( "$dir/$editor_css" )
	);

	$style_css = 'block/style-index.css';
	wp_register_style(
		'f1rehead-slideshow-block',
		plugins_url( $style_css, __FILE__ ),
		array(),
		filemtime( "$dir/$style_css" )
	);

	// WP Localized globals. Use dynamic PHP stuff in JavaScript via `globals` object.
	wp_localize_script(
		'f1rehead-slideshow-block-editor',
		'globals', // Array containing dynamic data for a JS Global.
		[
			'pluginDirPath' => plugin_dir_path( __DIR__ ),
			'pluginDirUrl'  => plugin_dir_url( __DIR__ ),
			// Add data here to access from `globals` object.
			'slideshows' => get_posts(['posts_per_page' => -1, 'post_type' => 'slideshow']),
		]
	);	
	
	register_block_type( 'f1rehead/slideshow', array(
		'editor_script' => 'f1rehead-slideshow-block-editor',
		'editor_style'  => 'f1rehead-slideshow-block-editor',
		'style'         => 'f1rehead-slideshow-block',
		'render_callback' => 'f1rehead_slideshow_render_slideshow_block',
		) );
	}

/**
 * Activate plugin
 */
SlideShowSEPluginMain::bootStrap();
