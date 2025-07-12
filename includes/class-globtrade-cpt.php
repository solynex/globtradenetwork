<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Globtrade_CPT {

    public function __construct() {
        add_action( 'init', array( $this, 'register_post_types' ) );
    }

    public function register_post_types() {
        $this->register_request_cpt();
        $this->register_offer_cpt();
    }

    private function register_request_cpt() {
        $labels = array(
            'name'                  => _x( 'Requests', 'Post type general name', 'globtrade' ),
            'singular_name'         => _x( 'Request', 'Post type singular name', 'globtrade' ),
            'menu_name'             => _x( 'Requests', 'Admin Menu text', 'globtrade' ),
            'add_new'               => __( 'Add New', 'globtrade' ),
            'add_new_item'          => __( 'Add New Request', 'globtrade' ),
            'edit_item'             => __( 'Edit Request', 'globtrade' ),
            'new_item'              => __( 'New Request', 'globtrade' ),
            'view_item'             => __( 'View Request', 'globtrade' ),
            'search_items'          => __( 'Search Requests', 'globtrade' ),
            'not_found'             => __( 'No Requests found', 'globtrade' ),
            'not_found_in_trash'    => __( 'No Requests found in Trash', 'globtrade' ),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'show_ui'            => true,
            'show_in_menu'       => 'globtrade-dashboard',
            'show_in_rest'       => true,
            'supports'           => array( 'title', 'editor', 'author', 'custom-fields' ),
            'rewrite'            => array( 'slug' => 'requests' ),
            'menu_icon'          => 'dashicons-cart',
        );

        register_post_type( 'gt_request', $args );
    }

    private function register_offer_cpt() {
        $labels = array(
            'name'                  => _x( 'Offers', 'Post type general name', 'globtrade' ),
            'singular_name'         => _x( 'Offer', 'Post type singular name', 'globtrade' ),
            'menu_name'             => _x( 'Offers', 'Admin Menu text', 'globtrade' ),
            'add_new_item'          => __( 'Add New Offer', 'globtrade' ),
            'edit_item'             => __( 'Edit Offer', 'globtrade' ),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'show_ui'            => true,
            'show_in_menu'       => 'globtrade-dashboard',
            'show_in_rest'       => true,
            'supports'           => array( 'title', 'author', 'custom-fields', 'parent_item_colon' ),
            'rewrite'            => array( 'slug' => 'offers' ),
            'menu_icon'          => 'dashicons-tag',
        );

        register_post_type( 'gt_offer', $args );
    }
}