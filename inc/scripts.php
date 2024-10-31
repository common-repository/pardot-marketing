<?php
/**
 * Plugin CSS & JS
 *
 * @package PardotMarketing
 * @since 1.1.0
 */

/**
 * Register & enqueue the admin plugin JS & CSS
 */
if ( ! function_exists( 'pardotmarketing_admin_scripts' ) ) {
  function pardotmarketing_admin_scripts( $hook_suffix ) {
    // Handle registering & enqueuing scripts based on the current admin page
    switch( $hook_suffix ) {
      case 'pardot-marketing_page_pardot-marketing-prospects':
      case 'pardot-marketing_page_pardot-marketing-forms':
        wp_enqueue_style(
          'pardotmarketing-admin',
          plugin_dir_url( PARDOT_MARKETING ) .
            '/assets/css/admin.css',
          false,
          PARDOT_MARKETING_VERSION
        );

        wp_enqueue_script(
          'pardotmarketing-admin',
          plugin_dir_url( PARDOT_MARKETING ) .
            '/assets/js/admin.js',
          false,
          PARDOT_MARKETING_VERSION
        );
      break;
    }
  }
}
add_action( 'admin_enqueue_scripts', 'pardotmarketing_admin_scripts' );
