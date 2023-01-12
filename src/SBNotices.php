<?php
/**
 * Main SBNotices Class
 *
 * @package SBNotices
 */

namespace Smashballoon\Notices;

defined( 'ABSPATH' ) || exit;

/**
 * Main SBNotices Class
 *
 * @class SBNotices
 */
class SBNotices {

	/**
	 * SBNotices version.
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * The single instance of the class.
	 *
	 * @var SBNotices
	 * @since 1.0.0
	 */
	protected static $instance = null;

	/**
	 * Main SBNotices Instance.
	 *
	 * Ensures only one instance of SBNotices is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return SBNotices - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->define_constants();
		$this->init_hooks();
		$this->includes();
	}

	/**
	 * Define Constants.
	 *
	 * @since 1.0.0
	 */
	private function define_constants() {
		$this->define( 'SB_NOTICES_PLUGIN_NAME', 'sb-notices' );
		$this->define( 'SB_NOTICES_VERSION', $this->version );
		$this->define( 'SB_NOTICES_ABSPATH', dirname( SB_NOTICES_PLUGIN_FILE ) . '/' );
		$this->define( 'SB_NOTICES_PLUGIN_URL', $this->plugin_url() );
		$this->define( 'SB_NOTICES_PLUGIN_BASENAME', plugin_basename( SB_NOTICES_PLUGIN_FILE ) );
		$this->define( 'SB_NOTICES_PLUGIN_PATH', $this->plugin_path() );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @since 1.0.0
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {

	}

	/**
	 * Include required files.
	 *
	 * @since 1.0.0
	 */
	private function includes() {

	}

	/**
	 * Get the plugin URL.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', SB_NOTICES_PLUGIN_FILE ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( SB_NOTICES_PLUGIN_FILE ) );
	}

	/**
	 * Get the template path.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function template_path() {
		return apply_filters( 'sb_notices_template_path', '/sb-notices/' );
	}

}
