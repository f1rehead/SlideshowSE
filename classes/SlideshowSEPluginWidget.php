<?php
/**
 * Class SlideshowSEPluginWidget allows showing one of your slideshows in your widget area.
 *
 * @since 1.2.0
 * @author: Stefan Boonstra
 */
class SlideshowSEPluginWidget extends WP_Widget
{
	/** @var string $widgetName */
	static $widgetName = 'Slideshow';

	/**
	 * Initializes the widget
	 *
	 * @since 1.2.0
	 */
	function __construct()
	{
		// Settings
		$options = array(
			'classname'   => 'SlideshowSEWidget',
			'description' => __('Enables you to show your slideshows in the widget area of your website.', 'slideshow-se')
		);

		// Create the widget.
		parent::__construct(
			'SlideshowSEWidget',
			__('Slideshow Widget', 'slideshow-se'),
			$options
		);
	}

	/**
	 * The widget as shown to the user.
	 *
	 * @since 1.2.0
	 * @param mixed array $args
	 * @param mixed array $instance
	 */
	function widget($args, $instance)
	{
		// Get slideshowId
		$slideshowId = '';
		if (isset($instance['slideshowId']))
		{
			$slideshowId = $instance['slideshowId'];
		}

		// Get title
		$title = '';
		if (isset($instance['title']))
		{
			$title = $instance['title'];
		}

		// Prepare slideshow for output to website.
		$output = SlideshowSEPlugin::prepare($slideshowId);

		$beforeWidget = $afterWidget = $beforeTitle = $afterTitle = '';
		if (isset($args['before_widget']))
		{
			$beforeWidget = $args['before_widget'];
		}

		if (isset($args['after_widget']))
		{
			$afterWidget = $args['after_widget'];
		}

		if (isset($args['before_title']))
		{
			$beforeTitle = $args['before_title'];
		}

		if (isset($args['after_title']))
		{
			$afterTitle = $args['after_title'];
		}

		// Output widget
		//echo esc_html($beforeWidget) . (!empty($title) ? esc_html($beforeTitle) . esc_html($title) . esc_html($afterTitle) : '') . esc_html($output) . esc_html($afterWidget);
		echo wp_kses_post($beforeWidget . (!empty($title) ? $beforeTitle . $title . $afterTitle : '') . $output . $afterWidget);
	}

	/**
	 * The form shown on the admins widget page. Here settings can be changed.
	 *
	 * @since 1.2.0
	 * @param mixed array $instance
	 * @return string
	 */
	function form($instance)
	{
		// Defaults
		$defaults = array(
			'title'       => 'slideshow-se',
			'slideshowId' => -1
		);

		// Merge database settings with defaults
		$instance = wp_parse_args((array) $instance, $defaults);

		// Get slideshows
		$slideshows = get_posts(array(
			'numberposts' => -1,
			'offset'      => 0,
			'post_type'   => SlideshowSEPluginPostType::$postType
		));

		$data              = new stdClass();
		$data->widget      = $this;
		$data->instance   = $instance;
		$data->slideshows = $slideshows;

		// Include form
		SlideshowSEPluginMain::outputView(__CLASS__ . DIRECTORY_SEPARATOR . 'form.php', $data);
	}

	/**
	 * Updates widget's settings.
	 *
	 * @since 1.2.0
	 * @param mixed array $newInstance
	 * @param mixed array $instance
	 * @return mixed array $instance
	 */
	function update($newInstance, $instance)
	{
		// Update title
		if (isset($newInstance['title']))
		{
			$instance['title'] = $newInstance['title'];
		}

		// Update slideshowId
		if (isset($newInstance['slideshowId']) &&
			!empty($newInstance['slideshowId']))
		{
			$instance['slideshowId'] = $newInstance['slideshowId'];
		}

		// Save
		return $instance;
	}

	/**
	 * Registers this widget (should be called upon widget_init action hook)
	 *
	 * @since 1.2.0
	 */
	static function registerWidget()
	{
		register_widget(__CLASS__);
	}
}