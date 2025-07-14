<?php
/**
 * Plugin Name:       GLOBTRADE
 * Description:       A B2B platform to connect importers and exporters.
 * Version:           1.1.0
 * Author:            eyad amer
 * Author URI:        mailto:eyad@solynex.com
 * Text Domain:       globtrade
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'GLOBTRADE_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'GLOBTRADE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once GLOBTRADE_PLUGIN_PATH . 'includes/class-globtrade-setup.php';
require_once GLOBTRADE_PLUGIN_PATH . 'includes/class-globtrade-cpt.php';
require_once GLOBTRADE_PLUGIN_PATH . 'includes/class-globtrade-api.php';
require_once GLOBTRADE_PLUGIN_PATH . 'includes/class-globtrade-admin.php';
require_once GLOBTRADE_PLUGIN_PATH . 'includes/class-globtrade-assets.php';
require_once GLOBTRADE_PLUGIN_PATH . 'includes/class-globtrade-woocommerce-integration.php';
require_once GLOBTRADE_PLUGIN_PATH . 'includes/class-globtrade-users-list-table.php';


function globtrade_run() {
    new Globtrade_Setup();
    new Globtrade_CPT();
    new Globtrade_API();
    new Globtrade_Admin();
    new Globtrade_Assets();
    new Globtrade_WooCommerce_Integration();
}
add_action( 'plugins_loaded', 'globtrade_run' );

function globtrade_auth_page_shortcode() {
    ob_start();
    include_once( GLOBTRADE_PLUGIN_PATH . 'frontend/templates/auth-template.php' );
    return ob_get_clean();
}
add_shortcode( 'globtrade_auth_page', 'globtrade_auth_page_shortcode' );

function globtrade_platform_page_shortcode() {
    if ( ! is_user_logged_in() ) {
        $login_page_url = home_url('/auth'); 
        wp_redirect( $login_page_url );
        exit;
    }
    ob_start();
    include_once( GLOBTRADE_PLUGIN_PATH . 'frontend/templates/platform-template.php' );
    return ob_get_clean();
}
add_shortcode( 'globtrade_platform_page', 'globtrade_platform_page_shortcode' );
function globtrade_public_requests_shortcode() {
    ob_start();
    include_once( GLOBTRADE_PLUGIN_PATH . 'frontend/templates/public-requests-template.php' );
    return ob_get_clean();
}
add_shortcode( 'globtrade_public_requests', 'globtrade_public_requests_shortcode' );
function globtrade_featured_requests_shortcode() {
    return '<div id="globtrade-featured-requests-list" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6"></div>';
}
add_shortcode( 'globtrade_featured_requests', 'globtrade_featured_requests_shortcode' );