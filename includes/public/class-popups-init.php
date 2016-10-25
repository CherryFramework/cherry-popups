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
	 * [$errors description]
	 * @var array
	 */
	public $sys_messages = array();

	/**
	 * MailChimp API server
	 *
	 * @var string
	 */
	private $api_server = 'https://%s.api.mailchimp.com/2.0/';

	/**
	 * Sets up needed actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Page popup initialization
		add_action( 'wp_footer', array( $this, 'page_popup_init' ) );

		add_action( 'after_setup_theme', array( $this, 'set_cherry_utility' ), 10 );
	}

	/**
	 * Set cherry utility object
	 *
	 * @return void
	 */
	public function set_cherry_utility() {
		cherry_popups()->get_core()->init_module( 'cherry-utility' );
		$this->cherry_utility = cherry_popups()->get_core()->modules['cherry-utility']->utility;

		$this->sys_messages = array(
			'invalid_mail'      => esc_html__( 'Please, provide valid mail', 'cherry-popups' ),
			'mailchimp'         => esc_html__( 'Please, set up MailChimp API key and List ID', 'cherry-popups' ),
			'internal'          => esc_html__( 'Internal error. Please, try again later', 'cherry-popups' ),
			'server_error'      => esc_html__( 'Server error. Please, try again later', 'cherry-popups' ),
			'mailchimp_success' => esc_html__( 'Success', 'cherry-popups' ),
		);

		cherry_popups()->get_core()->init_module(
			'cherry-handler',
			array(
				'id'           => 'cherry_subscribe_form_ajax',
				'action'       => 'cherry_subscribe_form_ajax',
				'is_public'    => true,
				'callback'     => array( $this , 'cherry_subscribe_form_ajax' ),
			)
		);
	}

	/**
	 * Proccesing subscribe form ajax
	 *
	 * @return void
	 */
	public function cherry_subscribe_form_ajax() {
		$data = ( ! empty( $_POST['data'] ) ) ? $_POST['data'] : false;

		if ( ! $data ) {
			wp_send_json_error( array( 'type' => 'error', 'message' => $this->sys_messages['server_error'] ) );
		}

		$mail = $data['mail'];

		if ( empty( $mail ) || ! is_email( $mail ) ) {
			wp_send_json( array( 'type' => 'error', 'message' => $this->sys_messages['invalid_mail'] ) );
		}

		$args = array(
			'email' => array(
				'email' => $mail,
			),
			'double_optin' => false,
		);


		$response = $this->api_call( 'lists/subscribe', $args );

		if ( false === $response ) {
			wp_send_json( array( 'type' => 'error', 'message' => $this->sys_messages['mailchimp'] ) );
		}

		$response = json_decode( $response, true );

		if ( empty( $response ) ) {
			wp_send_json( array( 'type' => 'error', 'message' => $this->sys_messages['internal'] ) );
		}

		if ( isset( $response['status'] ) && 'error' == $response['status'] ) {
			wp_send_json( array( 'type' => 'error', 'message' => esc_html( $response['error'] ) ) );
		}

		wp_send_json( array( 'type' => 'success', 'message' =>  $this->sys_messages['mailchimp_success'] ) );
	}

	/**
	 * Make remote request to mailchimp API
	 *
	 * @param  string $method API method to call.
	 * @param  array  $args   API call arguments.
	 * @return array|bool
	 */
	public function api_call( $method, $args = array() ) {

		if ( ! $method ) {
			return false;
		}

		$api_key = cherry_popups()->get_option( 'mailchimp-api-key', '' );
		$list_id = cherry_popups()->get_option( 'mailchimp-list-id', '' );

		if ( ! $api_key || ! $list_id ) {
			return false;
		}

		$key_data = explode( '-', $api_key );

		if ( empty( $key_data ) || ! isset( $key_data[1] ) ) {
			return false;
		}

		$this->api_server = sprintf( $this->api_server, $key_data[1] );

		$url      = esc_url( trailingslashit( $this->api_server . $method ) );
		$defaults = array( 'apikey' => $api_key, 'id' => $list_id );
		$data     = json_encode( array_merge( $defaults, $args ) );

		$request = wp_remote_post( $url, array( 'body' => $data ) );

		return wp_remote_retrieve_body( $request );
	}

	/**
	 * Page popup initialization
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function page_popup_init() {

		$enable_popups = cherry_popups()->get_option( 'enable-popups', 'true' );
		$enable_popups_mobile = cherry_popups()->get_option( 'mobile-enable-popups', 'true' );
		$enable_logged_user = cherry_popups()->get_option( 'enable-logged-users', 'false' );

		// Check if global popups enable.
		if ( 'false' === $enable_popups ) {
			return false;
		}

		// Check if global modile popups enable.
		if ( 'false' === $enable_popups_mobile && is_mobile() ) {
			return false;
		}

		// Check if global disable logged user popups enable.
		if ( 'false' === $enable_logged_user && is_user_logged_in() ) {
			return false;
		}

		$page_id = get_the_ID();

		$open_page_popup_id = get_post_meta( $page_id, 'cherry-open-page-popup', true );
		$close_page_popup_id = get_post_meta( $page_id, 'cherry-close-page-popup', true );

		$this->render_open_popup( $open_page_popup_id );

		$this->render_close_popup( $close_page_popup_id );

	}

	/**
	 * Render open popup
	 *
	 * @param  string $popup_id Popup id
	 * @return void
	 */
	public function render_open_popup( $popup_id = 'disable' ) {
		$default_open_popup_id = cherry_popups()->get_option( 'default-open-page-popup', 'disable' );
		$open_page_popup_display = cherry_popups()->get_option( 'open-page-popup-display', array() );

		if ( 'disable' !== $popup_id && $this->is_static() ) {
			$this->render_popup( $popup_id, 'open-page' );
			return false;
		}

		if ( 'disable' == $default_open_popup_id ) {
			return false;
		}

		if ( empty( $open_page_popup_display ) ) {
			return false;
		}

		if ( 'true' === $open_page_popup_display['home'] && is_home() ) {
			$this->render_popup( $default_open_popup_id, 'open-page' );
		}

		if ( 'true' === $open_page_popup_display['pages'] && $this->is_static() ) {
			$this->render_popup( $default_open_popup_id, 'open-page' );
		}

		if ( 'true' === $open_page_popup_display['posts'] && is_single() ) {
			$this->render_popup( $default_open_popup_id, 'open-page' );
		}

		if ( 'true' === $open_page_popup_display['other'] && ( is_archive() || is_tax() ) ) {
			$this->render_popup( $default_open_popup_id, 'open-page' );
		}

		return false;
	}

	/**
	 * Render close popup
	 *
	 * @param  string $popup_id Popup id.
	 * @return void
	 */
	public function render_close_popup( $popup_id = 'disable' ) {
		$default_close_popup_id = cherry_popups()->get_option( 'default-close-page-popup', 'disable' );
		$close_page_popup_display = cherry_popups()->get_option( 'close-page-popup-display', array() );

		if ( 'disable' !== $popup_id && $this->is_static() ) {
			$this->render_popup( $popup_id, 'close-page' );

			return false;
		}

		if ( 'disable' == $default_close_popup_id ) {
			return false;
		}

		if ( empty( $close_page_popup_display ) ) {
			return false;
		}

		if ( 'true' === $close_page_popup_display['home'] && is_home() ) {
			$this->render_popup( $default_close_popup_id, 'close-page' );
		}

		if ( 'true' === $close_page_popup_display['pages'] && $this->is_static() ) {
			$this->render_popup( $default_close_popup_id, 'close-page' );
		}

		if ( 'true' === $close_page_popup_display['posts'] && is_single() ) {
			$this->render_popup( $default_close_popup_id, 'close-page' );
		}

		if ( 'true' === $close_page_popup_display['other'] && ( is_archive() || is_tax() ) ) {
			$this->render_popup( $default_close_popup_id, 'close-page' );
		}

		return false;
	}

	/**
	 * Render popup instance
	 *
	 * @param  string $popup_id   Popup id.
	 * @param  string $popup_type Popup type.
	 * @return void|false
	 */
	public function render_popup( $popup_id = 'disable', $popup_type = 'open-page' ) {

		if ( 'disable' !== $popup_id ) {
			$close_popup = new Cherry_Popups_Data(
				array(
					'id'  => $popup_id,
					'use' => $popup_type,
				)
			);
			$close_popup->render_popup();
		} else {
			return false;
		}
	}

	/**
	 * Static page check
	 *
	 * @return boolean
	 */
	public function is_static() {
		if ( is_page() && ! is_home() ) {
			return true;
		}

		return false;
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
