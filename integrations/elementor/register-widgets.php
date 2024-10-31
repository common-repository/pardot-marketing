<?php
/**
 * Handles registering Elementor widgets
 *
 * @package PardotMarketing
 * @since 1.0.0
 */

namespace PardotMarketing;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Class Plugin
 *
 * Main Plugin class
 */
class PardotMarketing_Widgets {

	/**
	 * Instance
	 *
	 * @access private
	 * @static
	 *
	 * @var PardotMarketing_Widgets The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @access public
	 *
	 * @return PardotMarketing_Widgets An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * widget_scripts
	 *
	 * Load required plugin core files.
	 *
	 * @access public
	 */
	public function widget_scripts() {
		$settings = pardotmarketing_options();

		$recaptcha_url = 'https://www.google.com/recaptcha/api.js?render=';
		if ( ! empty( $settings['recaptchav3'] ) ) {
			$recaptcha_url .= $settings['recaptchav3'];
		}

		wp_register_script(
			'pardotmarketing-recaptcha',
			$recaptcha_url,
			array(),
			PARDOT_MARKETING_VERSION,
			true
		);
		wp_register_script( 'pardotmarketing-jquery-validation', plugin_dir_url( PARDOT_MARKETING ) . 'assets/js/jquery.validate.min.js', array( 'jquery' ), PARDOT_MARKETING_VERSION, true );
		wp_register_style( 'pardotmarketing-form-handler', plugin_dir_url( PARDOT_MARKETING ) . 'assets/css/form-handler.css', array(), PARDOT_MARKETING_VERSION );
	}

	/**
	 * Include Widgets files
	 *
	 * Load widgets files
	 *
	 * @access private
	 */
	private function include_widgets_files() {
		require_once __DIR__ . '/widgets/form-handler.php';
	}

	/**
	 * Register Widgets
	 *
	 * Register new Elementor widgets.
	 *
	 * @access public
	 */
	public function register_widgets() {
		// Its now safe to include Widgets files.
		$this->include_widgets_files();

		// Register widgets.
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\FormHandler() );
	}

	/**
	 * Register Dynamic Tags
	 *
	 * @access public
	 */
	public function register_dynamic_tags( $dynamic_tags ) {
		// In our Dynamic Tag we use a group named request-variables so we need
		// To register that group as well before the tag
		\Elementor\Plugin::$instance->dynamic_tags->register_group( 'request-variables', [
			'title' => __( 'Request Variables', 'pardotmarketing' )
		] );

		// Include the Dynamic tag class file.
		include_once __DIR__ . '/dynamic-tags/request-variables.php';
		include_once __DIR__ . '/dynamic-tags/cookies.php';

		// Finally register the tag.
		$dynamic_tags->register_tag( 'Elementor_Request_Var_Tag' );
		$dynamic_tags->register_tag( 'Elementor_Cookies_Tag' );
	}

	/**
	 *  Plugin class constructor
	 *
	 * Register plugin action hooks and filters
	 *
	 * @access public
	 */
	public function __construct() {

		// Register widget scripts.
		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'widget_scripts' ) );

		// Register widgets.
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widgets' ) );

		// Register dynamic tags.
		add_action( 'elementor/dynamic_tags/register_tags', array( $this, 'register_dynamic_tags' ) );
	}
}

// Instantiate Plugin Class.
PardotMarketing_Widgets::instance();
