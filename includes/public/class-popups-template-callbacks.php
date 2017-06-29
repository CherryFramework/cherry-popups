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
	 * Current popup id.
	 *
	 * @since 1.0.0
	 * @var   array
	 */
	public $popup_id = null;

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
	public function clear_meta() {
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

		$html = '<h3 %1$s>%4$s</h3>';

		$settings = array(
			'visible'      => true,
			'length'       => $attr['number_of_words'],
			'trimmed_type' => 'word',
			'ending'       => '&hellip;',
			'html'         => $html,
			'class'        => '',
			'title'        => '',
			'echo'         => false,
		);

		/**
		 * Filter post title settings.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		$settings = apply_filters( 'cherry-popup-title-settings', $settings );

		$html = '<div class="cherry-popup-title">';
			$html .= cherry_popups_init()->cherry_utility->attributes->get_title( $settings, 'post', $this->popup_id );
		$html .= '</div>';

		return $html;
	}

	/**
	 * Get post content.
	 *
	 * @since 1.0.0
	 */
	public function get_content( $attr = array() ) {
		$post_data = get_post( $this->popup_id );

		$default_attr = array(
			'number_of_words' => -1,
		);

		$attr = wp_parse_args( $attr, $default_attr );

		ob_start();
		?><div class="cherry-popup-content"><?php

		$content = $post_data->post_content;
		$content = apply_filters( 'the_content', $content );
		$content = str_replace( ']]>', ']]&gt;', $content );
		echo $content;
		?></div><?php
		$content = ob_get_contents();

		ob_end_clean();

		return $content;
	}

	/**
	 * Get subscribe form.
	 *
	 * @since 1.0.0
	 */
	public function get_subscribe_form( $attr = array() ) {
		$default_attr = array(
			'submit_text'      => esc_html__( 'Subscribe', 'cherry-projects' ),
			'placeholder_text' => esc_html__( 'Your email', 'cherry-projects' ),
		);

		$attr = wp_parse_args( $attr, $default_attr );

		$html = '<div class="cherry-popup-subscribe">';
			$html .= '<form method="POST" action="#" class="cherry-popup-subscribe__form">';
				$html .= '<div class="cherry-popup-subscribe__message"><span></span></div>';
				$html .= '<div class="cherry-popup-subscribe__input-group">';
					$html .= '<input class="cherry-popup-subscribe__input" type="email" name="subscribe-mail" value="" placeholder="' . $attr['placeholder_text'] . '">';
					$html .= '<div class="cherry-popup-subscribe__submit">' . $attr['submit_text'] . '</div>';
				$html .= '</div>';
			$html .= '</form>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Get subscribe form.
	 *
	 * @since 1.0.0
	 */
	public function get_login_form( $attr = array() ) {
		$default_attr = array(
			'submit_text'          => esc_html__( 'Login in', 'cherry-projects' ),
			'user_placeholder'     => esc_html__( 'User', 'cherry-projects' ),
			'password_placeholder' => esc_html__( 'Password', 'cherry-projects' ),
			'sign_up_message'      => esc_html__( 'Don\'t have an account? Click here to sign up.', 'cherry-projects' ),
		);

		$attr = wp_parse_args( $attr, $default_attr );

		$html = '<div class="cherry-popup-login">';
			$html .= '<form method="POST" action="#" class="cherry-popup-login__form">';
				$html .= '<div class="cherry-popup-login__message"><span></span></div>';
				$html .= '<div class="cherry-popup-login__input-group">';
					$html .= '<input class="cherry-popup-login__user_input" type="text" name="login-user" value="" placeholder="' . $attr['user_placeholder'] . '">';
					$html .= '<input class="cherry-popup-login__password_input" type="password" name="login-password" value="" placeholder="' . $attr['password_placeholder'] . '">';
					$html .= '<input class="cherry-popup-login__remember" type="checkbox" name="login-remember" value="">';
					$html .= '<div class="cherry-popup-login__submit">' . $attr['submit_text'] . '</div>';
				$html .= '</div>';
			$html .= '</form>';
		$html .= '</div>';

		return $html;
	}

}
