<?php
/*
 Plugin Name: Slideshow SE
 Plugin URI: http://wordpress.org/extend/plugins/slideshow-se/
 Description: The slideshow plugin is easily deployable on your website. Add any image that has already been uploaded to add to your slideshow, add text slides, or even add a video. Options and styles are customizable for every single slideshow on your website.
 Version: 2.7.1
 Requires at least: 6.3
 Tested up to: 7.0
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
	static $version = '2.7.1';

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
				self::$version,
				false
			);

			// adminURL string was removed in 6bc7f538425a2d399a747746500f363bd786331a
			// not sure why this wasn't removed back then...
			//wp_localize_script(
			//	'slideshow-jquery-image-gallery-script',
			//	'slideshow_jquery_image_gallery_script_adminURL',
			//	admin_url()
			//);
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

		$backend_script_dependencies = array(
			'jquery',
			'jquery-ui-sortable',
			'wp-color-picker',
		);

		// Enqueue 3.5 uploader and editor for slideshow add/edit screens.
		if ($currentScreen->post_type === 'slideshow') {
			if (function_exists('wp_enqueue_media')) {
				wp_enqueue_media();
			}
			if (function_exists('wp_enqueue_editor')) {
				wp_enqueue_editor();
				$backend_script_dependencies[] = 'wp-editor';
			}
		}

		wp_enqueue_script(
			'slideshow-se-jquery-image-gallery-backend-script',
			self::getPluginUrl() . '/js/min/all.backend.min.js',
			$backend_script_dependencies,
			SlideshowSEPluginMain::$version,
			false
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

	/**
	 * This function will return the list of HTML tags allowed in the wp_kses functions.
	 *
	 * @since 2.5.19
	 */

	static function getAllowedTags()
	{
		// HTML tags to allow in the wp_kses calls
		$allowedTags = array(
			'input' => array(
				'type' => array(),
				'name' => array(),
				'class' => array(),
				'value' => array(),
				'checked' => array(),
			),
			'textarea' => array(
				'name' => array(),
				'class' => array(),
				'rows' => array(),
				'cols' => array(),
			),
			'select' => array(
				'name' => array(),
				'class' => array(),
			),
			'option' => array(
				'value' => array(),
				'selected' => array()
			),
			'label' => array(
				'style' => array(),
			)
		);

		return $allowedTags;
	}
};

/**
 * Include the slideshow post type in front-end search without replacing other post types.
 *
 * @since 2.5.21
 * @param WP_Query $query Main query.
 */
function f1rehead_slideshow_include_custom_post_types_in_search_results( $query ) {
	if ( ! $query->is_main_query() || ! $query->is_search() || is_admin() ) {
		return;
	}

	$post_types = $query->get( 'post_type' );

	if ( empty( $post_types ) ) {
		$searchable = get_post_types( array( 'exclude_from_search' => false ), 'names' );
		$searchable['slideshow'] = SlideshowSEPluginPostType::$postType;
		$query->set( 'post_type', array_values( array_unique( $searchable ) ) );
		return;
	}

	if ( is_array( $post_types ) ) {
		if ( ! in_array( SlideshowSEPluginPostType::$postType, $post_types, true ) ) {
			$post_types[] = SlideshowSEPluginPostType::$postType;
			$query->set( 'post_type', array_values( array_unique( $post_types ) ) );
		}
		return;
	}

	if ( is_string( $post_types ) && SlideshowSEPluginPostType::$postType !== $post_types ) {
		$query->set( 'post_type', array( $post_types, SlideshowSEPluginPostType::$postType ) );
	}
}
add_action( 'pre_get_posts', 'f1rehead_slideshow_include_custom_post_types_in_search_results' );

/**
 * Registers all block assets so that they can be enqueued through the block editor
 * in the corresponding context.
 *
 * @see https://developer.wordpress.org/block-editor/tutorials/block-tutorial/applying-styles-with-stylesheets/
 */
function f1rehead_slideshow_block_init() {
	$dir = dirname( __FILE__ );

	$script_asset_path = $dir . '/block/index.asset.php';
	$index_js_path     = $dir . '/block/index.js';
	if ( ! file_exists( $script_asset_path ) || ! file_exists( $index_js_path ) ) {
		_doing_it_wrong(
			__FUNCTION__,
			'Slideshow SE: run `npm install` and `npm run build` in the plugin directory so block assets exist.',
			SlideshowSEPluginMain::$version
		);
		if ( is_admin() && current_user_can( 'activate_plugins' ) ) {
			add_action(
				'admin_notices',
				static function () {
					echo '<div class="notice notice-error"><p>';
					echo esc_html__( 'Slideshow SE: Gutenberg block assets are missing. From the plugin folder, run npm install and npm run build.', 'slideshow-se' );
					echo '</p></div>';
				}
			);
		}
		return;
	}
	$index_js     = 'block/index.js';
	$script_asset = require $script_asset_path;
	wp_register_script(
		'f1rehead-slideshow-block-editor',
		plugins_url( $index_js, __FILE__ ),
		$script_asset['dependencies'],
		$script_asset['version'],
		false
	);

	$block_css      = 'block/index.css';
	$block_css_full = $dir . '/' . $block_css;
	wp_register_style(
		'slideshow-se-editor-functional',
		plugins_url( 'style/SlideshowSEPlugin/functional.css', __FILE__ ),
		array(),
		SlideshowSEPluginMain::$version
	);

	wp_register_style(
		'f1rehead-slideshow-block',
		plugins_url( $block_css, __FILE__ ),
		array( 'slideshow-se-editor-functional' ),
		file_exists( $block_css_full ) ? filemtime( $block_css_full ) : false
	);

	// WP Localized globals. Use dynamic PHP stuff in JavaScript via `globals` object.
	$slideshow_posts = get_posts(
		array(
			'posts_per_page' => -1,
			'post_type'      => 'slideshow',
			'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
	$slideshow_choices = array();
	foreach ( $slideshow_posts as $p ) {
		$settings        = SlideshowSEPluginSlideshowSettingsHandler::getSettings( (int) $p->ID, false );
		$slideshow_height = isset( $settings['height'] )
			? (int) filter_var( (string) $settings['height'], FILTER_SANITIZE_NUMBER_INT )
			: 0;
		if ( $slideshow_height < 1 ) {
			$slideshow_height = 200;
		}
		$slideshow_choices[] = array(
			'ID'         => (int) $p->ID,
			'post_title' => $p->post_title,
			'height'     => $slideshow_height,
		);
	}
	wp_localize_script(
		'f1rehead-slideshow-block-editor',
		'globals',
		array(
			'pluginDirPath' => plugin_dir_path( __FILE__ ),
			'pluginDirUrl'  => plugin_dir_url( __FILE__ ),
			'slideshows'    => $slideshow_choices,
		)
	);
	
	register_block_type(
		'f1rehead/slideshow',
		array(
			'api_version'     => 3,
			'editor_script'   => 'f1rehead-slideshow-block-editor',
			'editor_style'    => 'f1rehead-slideshow-block',
			'style'           => 'f1rehead-slideshow-block',
			'render_callback' => 'f1rehead_slideshow_render_slideshow_block',
			// Must match src/index.js — REST block renderer validates against server registration.
			'attributes'      => array(
				'selectedSlideshow' => array(
					'type'    => 'string',
					'default' => '',
				),
			),
		)
	);
}

/**
 * Activate plugin
 */
SlideshowSEPluginMain::bootStrap();
