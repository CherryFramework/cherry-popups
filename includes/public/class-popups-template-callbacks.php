<?php
/**
 * Define callback functions for templater.
 *
 * @package   Cherry_Team
 * @author    Cherry Team
 * @license   GPL-3.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2012 - 2015, Cherry Team
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Callbacks for Projects shortcode templater.
 *
 * @since 1.0.0
 */
class Cherry_Popups_Template_Callbacks {

	/**
	 * Shortcode attributes array.
	 *
	 * @var array
	 */
	public $atts = array();

	/**
	 * Current post meta.
	 *
	 * @since 1.0.0
	 * @var   array
	 */
	public $post_meta = null;

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 * @param array $atts Set of attributes.
	 */
	public function __construct( $atts ) {
		$this->atts = $atts;
	}

	/**
	 * Get post meta.
	 *
	 * @since 1.1.0
	 */
	public function get_meta() {
		if ( null === $this->post_meta ) {
			global $post;
			$this->post_meta = get_post_meta( $post->ID, '', true );
		}

		return $this->post_meta;
	}

	/**
	 * Clear post data after loop iteration.
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function clear_data() {
		$this->post_meta = null;
	}

	/**
	 * Get post title.
	 *
	 * @since 1.0.0
	 */
	public function get_title( $attr = array() ) {

		$default_attr = array( 'number_of_words' => 10 );

		$attr = wp_parse_args( $attr, $default_attr );

		$html = '<h3 %1$s><a href="%2$s" %3$s rel="bookmark">%4$s</a></h3>';

		if ( is_single() ) {
			$html = '<h3 %1$s>%4$s</h3>';
		}

		$settings = array(
			'visible'		=> true,
			'length'		=> $attr['number_of_words'],
			'trimmed_type'	=> 'word',
			'ending'		=> '&hellip;',
			'html'			=> $html,
			'class'			=> '',
			'title'			=> '',
			'echo'			=> false,
		);

		/**
		 * Filter post title settings.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		$settings = apply_filters( 'cherry-popup-title-settings', $settings );

		$title = cherry_popups_init()->cherry_utility->attributes->get_title( $settings );
		//$title = 'lorem';

		return $title;
	}
}
