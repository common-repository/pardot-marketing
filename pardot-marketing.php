<?php
/**
 * Pardot Marketing WordPress Plugin
 *
 * @package    PardotMarketing
 * @subpackage WordPress
 * @since      1.0.0
 * @author     Highfivery LLC
 * @copyright  2021 Highfivery LLC
 * @license    GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Pardot Marketing
 * Plugin URI:        https://www.highfivery.com/projects/pardot-wordpress-plugin/
 * Description:       Annoyed with Pardot's constraining form handler embeds? The Pardot Marketing WordPress plugin allows you to easily add styled forms to your site — and more.
 * Version:           1.1.4
 * Requires at least: 5.2
 * Requires PHP:      7.3
 * Author:            Highfivery LLC
 * Author URI:        https://www.highfivery.com
 * Text Domain:       pardotmarketing
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

/**
 * Security Note: Blocks direct access to the plugin PHP files.
 */
defined( 'ABSPATH' ) || die();

// Define plugin constants
define( 'PARDOT_MARKETING', __FILE__ );
define( 'PARDOT_MARKETING_VERSION', '1.1.4' );

if ( ! function_exists( 'pardotmarketing_install' ) ) {
	/**
	 * Creates the custom Pardot capabilities.
	 */
	function pardotmarketing_install() {
		// Assign WP admin all Pardot Marketing capabilities.
		$admin = get_role( 'administrator' );
		$admin->add_cap( 'pardotmarketing_read_prospects', true );
		$admin->add_cap( 'pardotmarketing_read_forms', true );

		// Create the Pardot Adminstrator role.
		$pardot_admin_capabilities = get_role( 'administrator' )->capabilities;

		$pardot_admin_capabilities['pardotmarketing_read_prospects'] = true;
		$pardot_admin_capabilities['pardotmarketing_read_forms']     = true;

		add_role( 'pardotmarketing_admin', 'Pardot Administrator', $pardot_admin_capabilities );
	}
}
add_action( 'init', 'pardotmarketing_install' );

/**
 * Pardot API class
 */
require plugin_dir_path( PARDOT_MARKETING ) . '/classes/class-pardot-api.php';

/**
 * Helpers
 */
require plugin_dir_path( PARDOT_MARKETING ) . '/inc/helpers.php';

/**
 * Plugin scripts
 */
require plugin_dir_path( PARDOT_MARKETING ) . '/inc/scripts.php';

/**
 * Admin interface & functionality
 */
require plugin_dir_path( PARDOT_MARKETING ) . '/inc/admin.php';

/**
 * Elementor functionality
 */
require plugin_dir_path( PARDOT_MARKETING ) . '/integrations/elementor/widgets.php';
