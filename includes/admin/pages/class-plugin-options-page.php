<?php
/**
 * Sets up the plugin option page.
 *
 * @package    Blank_Plugin
 * @subpackage Admin
 * @author     Cherry Team
 * @license    GPL-3.0+
 * @copyright  2002-2016, Cherry Team
 */

// If class `Blank_Plugin_Options_Page` doesn't exists yet.
if ( ! class_exists( 'Blank_Plugin_Options_Page' ) ) {

	/**
	 * Blank_Plugin_Options_Page class.
	 */
	class Blank_Plugin_Options_Page {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var object
		 */
		private static $instance = null;

		/**
		 * Instance of the class Cherry_Interface_Builder.
		 *
		 * @since 1.0.0
		 * @var object
		 */
		private $builder = null;

		/**
		 * Class constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function __construct() {
			$this->builder = cherry_popups()->get_core()->modules['cherry-interface-builder'];
			$this->render_page();
		}

		/**
		 * Render plugin options page.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function render_page() {


			$this->builder->register_section(
				array(
					'popups_options_section' => array(
						'type'        => 'section',
						'scroll'      => false,
						'title'       => esc_html__( 'Cherry PopUps Settings', 'cherry-popups' ),
						'description' => esc_html__( 'General cherry popUps settings', 'cherry-popups' ),
					),
				)
			);

			$this->builder->register_settings(
				array(
					'option_page_content' => array(
						'type'   => 'settings',
						'parent' => 'popups_options_section',
					),
					'option_page_footer' => array(
						'type'   => 'settings',
						'parent' => 'popups_options_section',
					),
				)
			);

			$this->builder->register_html(
				array(
					'footer_html' => array(
						'type'   => 'html',
						'parent' => 'option_page_footer',
						'class'  => 'cherry-control form-button',
						'html'   => '<div id="cherry-projects-save-options" class="custom-button save-button"><span>' . esc_html__( 'Save', 'blank-plugin' ) . '</span></div><div id="cherry-projects-restore-options" class="custom-button restore-button"><span>' . esc_html__( 'Restore', 'blank-plugin' ) . '</span></div>',
					),
				)
			);

			$this->builder->register_form(
				array(
					'popups_options_form' => array(
						'type'   => 'form',
						'parent' => 'popups_options_section',
					),
				)
			);

			$this->builder->register_component(
				array(
					'tab_vertical' => array(
						'type'   => 'component-tab-vertical',
						'parent' => 'option_page_content',
					),
				)
			);

			$this->builder->register_settings(
				array(
					'general_tab' => array(
						'parent'			=> 'tab_vertical',
						'title'				=> esc_html__( 'General settings', 'cherry-popups' ),
						'description'		=> esc_html__( 'General plugin settings', 'cherry-popups' ),
					),
					'open_page_tab' => array(
						'parent'			=> 'tab_vertical',
						'title'				=> esc_html__( 'Open page settings', 'cherry-popups' ),
						'description'		=> esc_html__( 'Open page default popups settings', 'cherry-popups' ),
					),
					'close_page_tab' => array(
						'parent'			=> 'tab_vertical',
						'title'				=> esc_html__( 'Close page settings', 'cherry-popups' ),
						'description'		=> esc_html__( 'Third tab description.', 'cherry-popups' ),
					),
					'mailing_options' => array(
						'parent'			=> 'tab_vertical',
						'title'				=> esc_html__( 'Mailing List Manager', 'cherry-popups' ),
						'description'		=> esc_html__( 'Fourth tab description.', 'cherry-popups' ),
					),
				)
			);

			$this->builder->register_control(
				array(
					'test_button' => array(
						'type'         => 'button',
						'parent'       => 'option_page_footer',
						'style'=> '',
						'view_wrapping' => false,
					),
					'test_button_2' => array(
						'type'         => 'button',
						'parent'       => 'option_page_footer',
						'style'=> 'success',
						'class' => 'custom-class',
						'view_wrapping' => false,
					),
					'test_button_3' => array(
						'type'         => 'button',
						'parent'       => 'option_page_footer',
						'style'=> 'normal',
						'view_wrapping' => false,
					),
					'test_button_4' => array(
						'type'         => 'button',
						'parent'       => 'option_page_footer',
						'style'=> 'primary',
						'view_wrapping' => false,
					),
					'test_button_5' => array(
						'type'         => 'button',
						'parent'       => 'option_page_footer',
						'style'=> 'danger',
						'view_wrapping' => false,
					),
					'test_button_6' => array(
						'type'         => 'button',
						'parent'       => 'option_page_footer',
						'style'=> 'warning',
						'view_wrapping' => false,
					),
					'enable_popups' => array(
						'type'         => 'switcher',
						'parent'       => 'general_tab',
						'title'        => esc_html__( 'Enable popups', 'cherry-popups' ),
						'description'  => esc_html__( 'Enable / Disable popups at once on all pages', 'cherry-popups' ),
						'value'        => 'true',
						'toggle'       => array(
							'true_toggle'  => 'Enable',
							'false_toggle' => 'Disable',
						),
						'style'        => 'normal',
						'class'        => '',
						'label'        => '',
					),
					'mobile_enable_popups' => array(
						'type'         => 'switcher',
						'parent'       => 'general_tab',
						'title'        => esc_html__( 'Enable Plugin on Mobile Devices', 'cherry-popups' ),
						'description'  => esc_html__( 'Enable / Disable popups on mobile devices at once on all pages', 'cherry-popups' ),
						'value'        => 'true',
						'toggle'       => array(
							'true_toggle'  => 'Enable',
							'false_toggle' => 'Disable',
						),
						'style'        => 'normal',
						'class'        => '',
						'label'        => '',
					),
					'disable_logged_users' => array(
						'type'         => 'switcher',
						'parent'       => 'general_tab',
						'title'        => esc_html__( 'Disable for logged users', 'cherry-popups' ),
						'description'  => esc_html__( 'All popup will not be displayed for logged users', 'cherry-popups' ),
						'value'        => 'false',
						'toggle'       => array(
							'true_toggle'  => 'Enable',
							'false_toggle' => 'Disable',
						),
						'style'        => 'normal',
						'class'        => '',
						'label'        => '',
					),
					'default_open_page_popup' => array(
						'type'        => 'select',
						'parent'      => 'open_page_tab',
						'title'       => esc_html__( 'Default Open Page Popup', 'cherry-popups' ),
						'description' => esc_html__( 'Assign one of the popup that is displayed when you open the page.', 'cherry-popups' ),
						'multiple'    => false,
						'filter'      => true,
						'value'       => 'disable',
						'options'     => array(
							'disable' => esc_html__( 'Disable', 'cherry-popups' ),
							'popup-1' => 'Popup 1',
							'popup-2' => 'Popup 2',
							'popup-3' => 'Popup 3',
							'popup-4' => 'Popup 4',
						),
						'placeholder' => 'Select',
						'label'       => '',
						'class'       => '',
					),
					'open_page_popup_display' => array(
						'type'			=> 'checkbox',
						'parent'		=> 'open_page_tab',
						'title'			=> esc_html__( 'Open page popup display in:', 'cherry-popups' ),
						'description'	=> esc_html__( 'Displaing Open page popup in site pages', 'cherry-popups' ),
						'class'			=> '',
						'value'			=> array(),
						'options'		=> array(
							'home'  => esc_html__( 'Home', 'cherry-popups' ),
							'pages' => esc_html__( 'Pages', 'cherry-popups' ),
							'posts' => esc_html__( 'Posts', 'cherry-popups' ),
							'other' => esc_html__( 'Categories, Archive and other', 'cherry-popups' ),
						),
					),
					'default_close_page_popup' => array(
						'type'        => 'select',
						'parent'      => 'close_page_tab',
						'title'       => esc_html__( 'Default Close Page Popup', 'cherry-popups' ),
						'description' => esc_html__( 'Assign one of the popup that is displayed when you close the page', 'cherry-popups' ),
						'multiple'    => false,
						'filter'      => true,
						'value'       => 'disable',
						'options'     => array(
							'disable' => esc_html__( 'Disable', 'cherry-popups' ),
							'popup-1' => 'Popup 1',
							'popup-2' => 'Popup 2',
							'popup-3' => 'Popup 3',
							'popup-4' => 'Popup 4',
						),
						'placeholder' => 'Select',
						'label'       => '',
						'class'       => '',
					),
					'close_page_popup_display' => array(
						'type'			=> 'checkbox',
						'parent'		=> 'close_page_tab',
						'title'			=> esc_html__( 'Close page popup display in:', 'cherry-popups' ),
						'description'	=> esc_html__( 'Displaing Close page popup in site pages', 'cherry-popups' ),
						'class'			=> '',
						'value'			=> array(),
						'options'		=> array(
							'home'  => esc_html__( 'Home', 'cherry-popups' ),
							'pages' => esc_html__( 'Pages', 'cherry-popups' ),
							'posts' => esc_html__( 'Posts', 'cherry-popups' ),
							'other' => esc_html__( 'Categories, Archive and other', 'cherry-popups' ),
						),
					),
				)
			);



			$this->builder->render();
		}

		/**
		 * Get icons set
		 *
		 * @return array
		 */
		private function get_icons_set() {
			ob_start();

			include CHERRY_POPUPS_DIR . 'assets/fonts/icons.json';

			$json = ob_get_clean();
			$result = array();
			$icons = json_decode( $json, true );

			foreach ( $icons['icons'] as $icon ) {
				$result[] = $icon['id'];
			}

			return $result;
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return object
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}
	}
}
