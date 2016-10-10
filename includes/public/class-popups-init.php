<?php
/**
 * Cherry Popups init
 *
 * @package   Cherry_Popups
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2014 Cherry Team
 */

/**
 * Initialization Class.
 *
 * @since 1.0.0
 */
class Cherry_Popups_Init {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Cherry utility init
	 *
	 * @var null
	 */
	public $cherry_utility = null;

	/**
	 *
	 */
	public $dynamic_styles = array();

	/**
	 * Sets up needed actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Page popup initialization
		add_action( 'wp_footer', array( $this, 'page_popup_init') );

		add_action( 'after_setup_theme', array( $this, 'set_cherry_utility' ), 10 );
	}

	/**
	 * Page popup initialization
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function page_popup_init() {
		$page_id = get_the_ID();

		$this->page_meta = get_post_meta( $page_id, '', true );
		$open_page_popup_id = get_post_meta( $page_id, 'cherry-open-page-popup', true );
		$close_page_popup_id = get_post_meta( $page_id, 'cherry-close-page-popup', true );

		if ( 'disable' !== $open_page_popup_id ) {
			$open_popup = new Cherry_Popups_Data(
				array(
					'id'  => $open_page_popup_id,
					'use' => 'open-page',
				)
			);
			$open_popup->render_popup();
		}

		if ( 'disable' !== $close_page_popup_id ) {
			$close_popup = new Cherry_Popups_Data(
				array(
					'id'  => $close_page_popup_id,
					'use' => 'close-page',
				)
			);
			$close_popup->render_popup();
		}
	}

	/**
	 * Set cherry utility object
	 *
	 * @return void
	 */
	public function set_cherry_utility() {
		cherry_popups()->get_core()->init_module( 'cherry-utility' );
		$this->cherry_utility = cherry_popups()->get_core()->modules['cherry-utility']->utility;
	}

	/**
	 * [set_style description]
	 * @param array $style_rule [description]
	 */
	public function register_style( $style_rule = array() ) {

		$selector = $style_rule['selector'];
		$declaration = $style_rule['declaration'];

		// New lines are saved as || in CSS Custom settings, remove them
		$declaration = preg_replace( '/(\|\|)/i', '', $declaration );

		if ( array_key_exists( $selector, $this->dynamic_styles ) ) {

			$declaration = wp_parse_args( $this->dynamic_styles[ $selector ], $declaration );
		}

		$this->dynamic_styles[ $selector ] = $declaration;

	}

	/**
	 * [get_dunamic_styles description]
	 * @return [type] [description]
	 */
	public function get_dynamic_styles() {
		$dynamic_styles = '';

		if ( ! is_array( $this->dynamic_styles ) || empty( $this->dynamic_styles ) ) {
			return false;
		}

		foreach ( $this->dynamic_styles as $selector => $declaration ) {
			$dynamic_styles .= sprintf( '%1$s{', $selector ) . "\n";
			foreach ( $declaration as $property => $value ) {
				$dynamic_styles .= "\t" . sprintf( '%1$s: %2$s;', $property, $value ) . "\n";
			}
			$dynamic_styles .= "}\n";
		}

		return $dynamic_styles;
	}

	/**
	 * Get cherry popups query
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public function get_query_popups( $query_args = array() ) {

		$popups = array(
			'disable' => esc_html__( 'Disable', 'cherry-popups' ),
		);

		$default_query_args = apply_filters( 'cherry_popups_default_query_args',
			array(
				'post_type'      => CHERRY_POPUPS_NAME,
				'order'          => 'DESC',
				'orderby'        => 'date',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
			)
		);

		$query_args = wp_parse_args( $query_args, $default_query_args );

		$popups_query = new WP_Query( $query_args );

		if ( is_wp_error( $popups_query ) ) {
			return false;
		}

		// Reset the query.
		wp_reset_postdata();

		return $popups_query;
	}

	/**
	 * Get all avaliable cherry popups
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_avaliable_popups() {

		$popups = array(
			'disable' => esc_html__( 'Disable', 'cherry-popups' ),
		);

		$query = $this->get_query_popups(
			array(
				'order'   => 'ASC',
				'orderby' => 'name',
			)
		);

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) : $query->the_post();
				$post_id = $query->post->ID;
				$post_title = $query->post->post_title;
				$popups[ $post_id ] = $post_title;
			endwhile;
		} else {
			return false;
		}

		// Reset the query.
		wp_reset_postdata();

		return $popups;
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance )
			self::$instance = new self;

		return self::$instance;
	}
}

if ( ! function_exists( 'cherry_popups_init' ) ) {

	/**
	 * Returns instanse of the plugin class.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	function cherry_popups_init() {
		return Cherry_Popups_Init::get_instance();
	}
}

cherry_popups_init();
