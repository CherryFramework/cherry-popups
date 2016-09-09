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
	 * Sets up our actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->set_default_options();

	}

	/**
	 * Get defaults data options
	 *
	 * @return void
	 */
	public function set_default_options() {
		$this->default_options = array(
			//'standard-post-template' => cherry_projects()->get_option( 'standard-post-template', 'standard-post-template.tmpl' ),
			//'image-post-template'    => cherry_projects()->get_option( 'image-post-template', 'image-post-template.tmpl' ),
			//'gallery-post-template'  => cherry_projects()->get_option( 'gallery-post-template', 'gallery-post-template.tmpl' ),
			//'audio-post-template'    => cherry_projects()->get_option( 'audio-post-template', 'audio-post-template.tmpl' ),
			//'video-post-template'    => cherry_projects()->get_option( 'video-post-template', 'video-post-template.tmpl' ),
			'id'                     => null,
			'echo'                   => true,
		);

		/**
		 * Filter the array of default options.
		 *
		 * @since 1.0.0
		 * @param array options.
		 */
		$this->default_options = apply_filters( 'cherry_popups_default_options', $this->default_options );
	}

	/**
	 * Render PopUps
	 *
	 * @return string html string
	 */
	public function render_popups( $options = array() ) {
		$this->enqueue_styles();
		$this->enqueue_scripts();

		$this->options = wp_parse_args( $options, $this->default_options );


		$html = '<div class="cherry-projects-wrapper">';

		// Close wrapper.
		$html .= '</div>';

		if ( ! filter_var( $this->options['echo'], FILTER_VALIDATE_BOOLEAN ) ) {
			return $html;
		}

		echo $html;

	}

	/**
	 * Prepare template data to replace.
	 *
	 * @since 1.0.0
	 * @param array $atts Output attributes.
	 */
	function setup_template_data( $atts ) {
		require_once( CHERRY_PROJECTS_DIR . 'includes/public/class-cherry-popups-template-callbacks.php' );

		$callbacks = new Cherry_Popups_Template_Callbacks( $atts );

		$data = array(
			'title' => array( $callbacks, 'get_title' ),
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
		$default    = CHERRY_POPUPS_DIR . 'templates/shortcodes/' . $shortcode . '/default.tmpl';
		$upload_dir = wp_upload_dir();
		$upload_dir = trailingslashit( $upload_dir['basedir'] );
		$subdir     = 'templates/shortcodes/' . $shortcode . '/' . $template;

		/**
		 * Filters a default fallback-template.
		 *
		 * @since 1.0.0
		 * @param string $content.
		 */
		$content = apply_filters( 'cherry_popups_fallback_template', '<div class="inner-wrapper"></div>' );

		if ( file_exists( $upload_dir . $subdir ) ) {
			$file = $upload_dir . $subdir;
		} elseif ( $theme_template = locate_template( array( 'cherry-popups/' . $template ) ) ) {
			$file = $theme_template;
		} elseif ( file_exists( CHERRY_POPUPS_DIR . $subdir ) ) {
			$file = CHERRY_POPUPS_DIR . $subdir;
		} else {
			$file = $default;
		}

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
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'dashicons' );
		//wp_enqueue_style( 'magnific-popup', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/css/magnific-popup.css', array(), '1.1.0' );
		//wp_enqueue_style( 'cherry-projects-styles', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/css/styles.css', array(), CHERRY_PROJECTS_VERSION );
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		//wp_enqueue_script( 'waypoints', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/js/jquery.waypoints.min.js', array( 'jquery' ), CHERRY_PROJECTS_VERSION, true );
		//wp_enqueue_script( 'imagesloaded', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/js/imagesloaded.pkgd.min.js', array( 'jquery' ), CHERRY_PROJECTS_VERSION, true );
		//wp_enqueue_script( 'magnific-popup', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/js/jquery.magnific-popup.min.js', array( 'jquery' ), '1.1.0', true );
		//wp_enqueue_script( 'cherry-projects-plugin', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/js/cherry-projects-plugin.js', array( 'jquery' ), CHERRY_PROJECTS_VERSION, true );
		//wp_enqueue_script( 'cherry-projects-scripts', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/js/cherry-projects-scripts.js', array( 'jquery' ), CHERRY_PROJECTS_VERSION, true );

		// Ajax js object portfolio_type_ajax.
		//wp_localize_script( 'cherry-projects-scripts', 'cherryProjectsObjects', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}

}