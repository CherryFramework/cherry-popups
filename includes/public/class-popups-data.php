<?php
/**
 * Cherry PopUps Data
 *
 * @package   Cherry_Popups
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2014 Cherry Team
 */

/**
 * Class for Popups data.
 *
 * @since 1.0.0
 */
class Cherry_Popups_Data {

	/**
	 * Default options array
	 *
	 * @var array
	 */
	public $default_options = array();

	/**
	 * Current options array
	 *
	 * @var array
	 */
	public $options = array();

	/**
	 * Post query object.
	 *
	 * @var null
	 */
	private $posts_query = null;

	/**
	 * Current popup meta data
	 *
	 * @var null
	 */
	public $popup_settings = null;

	/**
	 * Sets up our actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $options = array() ) {
		$this->default_options = array(
			'id'       => null,
			'use'      => false,
			'template' => 'default-theme-popup.tmpl',
			'echo'     => true,
		);

		/**
		 * Filter the array of default options.
		 *
		 * @since 1.0.0
		 * @param array options.
		 */
		$this->default_options = apply_filters( 'cherry_popups_default_options', $this->default_options );

		$this->options = wp_parse_args( $options, $this->default_options );

		$auto_height = $this->get_popup_meta_field( 'cherry-popup-auto-height', 'auto' );

		$this->popup_settings = array(
			'id'                   => $this->options['id'],
			'use'                  => $this->options['use'],
			'layout-type'          => $this->get_popup_meta_field( 'cherry-layout-type', 'center' ),
			'show-hide-animation'  => $this->get_popup_meta_field( 'cherry-show-hide-animation', 'simple-fade' ),
			'base-theme'           => $this->get_popup_meta_field( 'cherry-popup-base-theme', 'theme-1' ),
			'width'                => $this->get_popup_meta_field( 'cherry-popup-width', 600 ),
			'height'               => ! filter_var( $auto_height, FILTER_VALIDATE_BOOLEAN ) ? $this->get_popup_meta_field( 'cherry-popup-height', 600 ) : 'auto',
			'container-color'      => $this->get_popup_meta_field( 'cherry-container-color', '#fff' ),
			'container-opacity'    => $this->get_popup_meta_field( 'cherry-overlay-opacity', 100 ),
			'overlay-type'         => $this->get_popup_meta_field( 'cherry-overlay-type', 'default' ),
			'overlay-color'        => $this->get_popup_meta_field( 'cherry-overlay-color', '#000' ),
			'overlay-opacity'      => $this->get_popup_meta_field( 'cherry-overlay-opacity', 50 ),
			'overlay-image'        => $this->get_popup_meta_field( 'cherry-overlay-image', '' ),
			'overlay-close-area'   => $this->get_popup_meta_field( 'cherry-overlay-close-area', 'true' ),
			'open-appear-event'    => $this->get_popup_meta_field( 'cherry-popup-open-appear-event', 'page-load' ),
			'load-open-delay'      => $this->get_popup_meta_field( 'cherry-page-load-open-delay', 1 ),
			'inactive-time'        => $this->get_popup_meta_field( 'cherry-user-inactive-time', 1 ),
			'page-scrolling-value' => $this->get_popup_meta_field( 'cherry-page-scrolling-value', 5 ),
			'close-appear-event'   => $this->get_popup_meta_field( 'cherry-popup-close-appear-event', 'outside-viewport' ),
			'template'             => $this->options['template'],
		);

		$this->generate_dynamic_styles();

	}

	/**
	 * Render PopUp
	 *
	 * @return string html string
	 */
	public function render_popup() {
		$this->enqueue_styles();
		$this->enqueue_scripts();


		// Item template.
		$template = $this->get_template_by_name( $this->options['template'], 'cherry-popup' );

		$macros = '/%%.+?%%/';
		$callbacks = $this->setup_template_data( $this->options );
		$callbacks->popup_id = $this->options['id'];

		$container_class = sprintf( 'cherry-popup cherry-popup-wrapper cherry-popup-%1$s %2$s-animation overlay-%3$s-type layout-type-%4$s', $this->options['id'], $this->popup_settings['show-hide-animation'], $this->popup_settings['overlay-type'], $this->popup_settings['layout-type'] );

		$popup_settings_encode = json_encode( $this->popup_settings );

		$html = sprintf( '<div class="%1$s" data-popup-settings=\'%2$s\'>', $container_class, $popup_settings_encode );
			$html .= '<div class="cherry-popup-overlay"></div>';
			$html .= '<div class="cherry-popup-container">';
				$html .= sprintf( '<div class="cherry-popup-show-again-check"><div class="marker"><span class="dashicons dashicons-yes"></span></div><span class="label">%1$s</span></div>', esc_html__( 'Don\'t show again' , 'cherry-popups' ) );
				$html .= '<div class="cherry-popup-container__inner">';
					$html .= '<div class="cherry-popup-close-button"><span class="dashicons dashicons-no"></span></div>';
					$template_content = preg_replace_callback( $macros, array( $this, 'replace_callback' ), $template );
					$html .= $template_content;
				$html .= '</div>';
			$html .= '</div>';
		// Close wrapper.
		$html .= '</div>';

		if ( ! filter_var( $this->options['echo'], FILTER_VALIDATE_BOOLEAN ) ) {
			return $html;
		}

		echo $html;
	}

	/**
	 * Generate dynamic CSS styles for popup instance.
	 *
	 * @return void
	 */
	public function generate_dynamic_styles(){

		switch ( $this->popup_settings['layout-type'] ) {
			case 'center-fullwidth':
			case 'bottom':
				$width = '100%';
				break;
			case 'center':
				$width = $this->popup_settings['width'] . 'px';
				break;
		}

		cherry_popups()->dynamic_css->add_style(
			sprintf( '.cherry-popup-%1$s .cherry-popup-container', $this->options['id'] ),
			array(
				'width'  => $width,
				'height' => ( 'auto' === $this->popup_settings['height'] ) ? $this->popup_settings['height'] : $this->popup_settings['height'] . 'px',
			)
		);

		switch ( $this->popup_settings['overlay-type'] ) {
			case 'fill-color':
				$rgb_array = $this->hex_to_rgb( $this->popup_settings['overlay-color'] );
				$opacity = intval( $this->popup_settings['overlay-opacity'] ) / 100;

				cherry_popups()->dynamic_css->add_style(
					sprintf( '.cherry-popup-%1$s .cherry-popup-overlay', $this->options['id'] ),
					array(
						'background-color' => sprintf( 'rgba(%1$s,%2$s,%3$s,%4$s);', $rgb_array[0], $rgb_array[1], $rgb_array[2], $opacity ),
					)
				);
				break;
			case 'image':
				if ( ! empty( $this->popup_settings['overlay-image'] ) ) {
					$image_data = wp_get_attachment_image_src( $this->popup_settings['overlay-image'], 'full' );
				}

				cherry_popups()->dynamic_css->add_style(
					sprintf( '.cherry-popup-%1$s .cherry-popup-overlay', $this->options['id'] ),
					array(
						'background-image' => sprintf( 'url(%1$s);', $image_data[0] ),
					)
				);
				break;
		}
	}

	/**
	 * Get meta field data.
	 *
	 * @param  string  $name    Field name.
	 * @param  boolean $default Default value.
	 * @return mixed
	 */
	private function get_popup_meta_field( $name = '', $default = false ) {

		$data = get_post_meta( $this->options['id'], $name, true );

		if ( empty( $data ) ){
			return $default;
		}

		return $data;
	}

	/**
	 * Prepare template data to replace.
	 *
	 * @since 1.0.0
	 * @param array $atts Output attributes.
	 */
	function setup_template_data( $atts ) {
		require_once( CHERRY_POPUPS_DIR . 'includes/public/class-popups-template-callbacks.php' );

		$callbacks = new Cherry_Popups_Template_Callbacks( $atts );

		$data = array(
			'title'   => array( $callbacks, 'get_title' ),
			'content' => array( $callbacks, 'get_content' ),
		);

		/**
		 * Filters item data.
		 *
		 * @since 1.0.0
		 * @param array $data Item data.
		 * @param array $atts Attributes.
		 */
		$this->post_data = apply_filters( 'cherry_popups_data_callbacks', $data, $atts );

		return $callbacks;
	}

	/**
	 * Retrieve a *.tmpl file content.
	 *
	 * @since  1.0.0
	 * @param  string $template  File name.
	 * @param  string $shortcode Shortcode name.
	 * @return string
	 */
	public function get_template_by_name( $template, $shortcode ) {
		$file       = '';
		$default    = CHERRY_POPUPS_DIR . 'templates/shortcodes/' . $shortcode . '/default-theme-popup.tmpl';
		$upload_dir = wp_upload_dir();
		$upload_dir = trailingslashit( $upload_dir['basedir'] );
		$subdir     = 'templates/shortcodes/' . $shortcode . '/' . $template;

		/**
		 * Filters a default fallback-template.
		 *
		 * @since 1.0.0
		 * @param string $content.
		 */
		$content = apply_filters( 'cherry_popups_fallback_template', '%%TITLE%%' );

		if ( file_exists( $upload_dir . $subdir ) ) {
			$file = $upload_dir . $subdir;
		} elseif ( $theme_template = locate_template( array( 'cherry-popups/' . $template ) ) ) {
			$file = $theme_template;
		} elseif ( file_exists( CHERRY_POPUPS_DIR . $subdir ) ) {
			$file = CHERRY_POPUPS_DIR . $subdir;
		} else {
			$file = $default;
		}

		$file = wp_normalize_path( $file );

		if ( ! empty( $file ) ) {
			$content = self::get_contents( $file );
		}

		return $content;
	}

	/**
	 * Read template (static).
	 *
	 * @since  1.0.0
	 * @return bool|WP_Error|string - false on failure, stored text on success.
	 */
	public static function get_contents( $template ) {

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			include_once( ABSPATH . '/wp-admin/includes/file.php' );
		}

		WP_Filesystem();
		global $wp_filesystem;

		// Check for existence.
		if ( ! $wp_filesystem->exists( $template ) ) {
			return false;
		}

		// Read the file.
		$content = $wp_filesystem->get_contents( $template );

		if ( ! $content ) {
			// Return error object.
			return new WP_Error( 'reading_error', 'Error when reading file' );
		}

		return $content;
	}

	/**
	 * Callback to replace macros with data.
	 *
	 * @since 1.0.0
	 * @param array $matches Founded macros.
	 */
	public function replace_callback( $matches ) {

		if ( ! is_array( $matches ) ) {
			return;
		}

		if ( empty( $matches ) ) {
			return;
		}

		$item   = trim( $matches[0], '%%' );
		$arr    = explode( ' ', $item, 2 );
		$macros = strtolower( $arr[0] );
		$attr   = isset( $arr[1] ) ? shortcode_parse_atts( $arr[1] ) : array();

		$callback = $this->post_data[ $macros ];

		if ( ! is_callable( $callback ) || ! isset( $this->post_data[ $macros ] ) ) {
			return;
		}

		if ( ! empty( $attr ) ) {

			// Call a WordPress function.
			return call_user_func( $callback, $attr );
		}

		return call_user_func( $callback );
	}

	/**
	 * Hex to rgb converter.
	 *
	 * @param  string $hex Hex color.
	 * @return array
	 */
	public function hex_to_rgb( $hex ) {
		$hex = str_replace( '#', '', $hex );

		if ( 3 == strlen( $hex ) ) {
			$r = hexdec( substr( $hex, 0, 1 ).substr( $hex, 0, 1 ) );
			$g = hexdec( substr( $hex, 1, 1 ).substr( $hex, 1, 1 ) );
			$b = hexdec( substr( $hex, 2, 1 ).substr( $hex, 2, 1 ) );
		} else {
			$r = hexdec( substr( $hex, 0, 2 ) );
			$g = hexdec( substr( $hex, 2, 2 ) );
			$b = hexdec( substr( $hex, 4, 2 ) );
		}
		$rgb = array( $r, $g, $b );

		return $rgb;
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'cherry-popups-styles', trailingslashit( CHERRY_POPUPS_URI ) . 'assets/css/min/cherry-popups-styles.min.css', array(), CHERRY_POPUPS_VERSION );
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'cherry-popups-plugin', trailingslashit( CHERRY_POPUPS_URI ) . 'assets/js/cherry-popups-plugin.js', array( 'jquery' ), CHERRY_POPUPS_VERSION, true );
		wp_enqueue_script( 'cherry-popups-scripts', trailingslashit( CHERRY_POPUPS_URI ) . 'assets/js/cherry-popups-scripts.js', array( 'jquery', 'cherry-popups-plugin' ), CHERRY_POPUPS_VERSION, true );
	}

}
