<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Globtrade_Assets {

    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
    }

    public function enqueue_assets() {
        if ( is_page( 'auth' ) ) {
            $this->enqueue_auth_assets();
        }
        if ( is_page( 'dashboard' ) ) {
            $this->enqueue_main_platform_assets();
        }

        global $post;
        if ( is_a( $post, 'WP_Post' ) && ( has_shortcode( $post->post_content, 'globtrade_public_requests' ) || has_shortcode( $post->post_content, 'globtrade_featured_requests' ) ) ) {
            $this->enqueue_public_assets();
        }
    }

    public function enqueue_admin_assets($hook) {
        if ( 'toplevel_page_globtrade-dashboard' != $hook ) {
            return;
        }
        wp_enqueue_media();
        wp_add_inline_script( 'jquery', "
            jQuery(document).ready(function($){
                $('#upload-btn').click(function(e) {
                    e.preventDefault();
                    var image = wp.media({ 
                        title: 'Upload Logo',
                        multiple: false
                    }).open()
                    .on('select', function(e){
                        var uploaded_image = image.state().get('selection').first();
                        var image_url = uploaded_image.toJSON().url;
                        $('#globtrade_logo_url').val(image_url);
                        $('#logo-preview').attr('src', image_url).show();
                    });
                });
            });
        ");
    }

    private function enqueue_auth_assets() {
        wp_enqueue_style( 'gt-google-font-cairo', 'https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap', array(), null );
        wp_enqueue_style( 'gt-tailwind', 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css', array(), '2.2.19' );
        wp_enqueue_style( 'gt-fontawesome', 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css', array(), '6.4.0' );
        wp_enqueue_style( 'gt-select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), '4.1.0' );
        wp_enqueue_style( 'gt-auth-style', GLOBTRADE_PLUGIN_URL . 'frontend/assets/css/auth-style.css', array(), filemtime( GLOBTRADE_PLUGIN_PATH . 'frontend/assets/css/auth-style.css' ) );
        
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'gt-select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array( 'jquery' ), '4.1.0', true );
        wp_enqueue_script( 'gt-auth-app', GLOBTRADE_PLUGIN_URL . 'frontend/assets/js/auth-app.js', array( 'jquery' ), filemtime( GLOBTRADE_PLUGIN_PATH . 'frontend/assets/js/auth-app.js' ), true );
        
        wp_localize_script( 'gt-auth-app', 'globtrade_data', array(
            'api_url' => esc_url_raw( rest_url( 'globtrade/v1/' ) ),
            'nonce'   => wp_create_nonce( 'wp_rest' ),
            'dashboard_url' => home_url('/dashboard'),
            'register_url' => home_url('/auth'),
        ) );
    }

    private function enqueue_main_platform_assets() {
        wp_enqueue_style( 'gt-google-font-cairo', 'https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap', array(), null );
        wp_enqueue_style( 'gt-tailwind', 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css', array(), '2.2.19' );
        wp_enqueue_style( 'gt-fontawesome', 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css', array(), '6.4.0' );
        wp_enqueue_style( 'gt-select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), '4.1.0' );
        wp_enqueue_style( 'gt-main-style', GLOBTRADE_PLUGIN_URL . 'frontend/assets/css/main-style.css', array(), filemtime( GLOBTRADE_PLUGIN_PATH . 'frontend/assets/css/main-style.css' ) );
        
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'gt-select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array( 'jquery' ), '4.1.0', true );
        wp_enqueue_script( 'gt-chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '4.3.0', true );
        wp_enqueue_script( 'gt-main-app', GLOBTRADE_PLUGIN_URL . 'frontend/assets/js/main-app.js', array( 'jquery' ), filemtime( GLOBTRADE_PLUGIN_PATH . 'frontend/assets/js/main-app.js' ), true );

        wp_localize_script( 'gt-main-app', 'globtrade_data', array(
            'api_url' => esc_url_raw( rest_url( 'globtrade/v1/' ) ),
            'nonce'   => wp_create_nonce( 'wp_rest' ),
            'logout_url' => wp_logout_url( home_url('/auth') ),
        ) );
    }

    private function enqueue_public_assets() {
        wp_enqueue_style( 'gt-tailwind', 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css', array(), '2.2.19' );
        wp_enqueue_style( 'gt-fontawesome', 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css', array(), '6.4.0' );
        
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'gt-public-requests-js', GLOBTRADE_PLUGIN_URL . 'frontend/assets/js/public-requests.js', array('jquery'), filemtime( GLOBTRADE_PLUGIN_PATH . 'frontend/assets/js/public-requests.js' ), true );
        
        $current_user = wp_get_current_user();
        $user_role = 'guest';
        if ( $current_user->ID != 0 ) {
            $user_role = $current_user->roles[0];
        }

        wp_localize_script( 'gt-public-requests-js', 'globtrade_data', array(
            'api_url' => esc_url_raw( rest_url( 'globtrade/v1/' ) ),
            'register_url' => home_url('/auth?tab=register'),
            'user_role' => $user_role,
        ) );
    }
}