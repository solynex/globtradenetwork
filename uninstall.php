<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;

$post_types_to_delete = array( 'gt_request', 'gt_offer' );

foreach ( $post_types_to_delete as $post_type ) {
    $posts = get_posts( array(
        'post_type'      => $post_type,
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'fields'         => 'ids',
    ) );

    if ( ! empty( $posts ) ) {
        foreach ( $posts as $post_id ) {
            wp_delete_post( $post_id, true );
        }
    }
}

remove_role( 'importer' );
remove_role( 'exporter' );

delete_option( 'globtrade_settings' );

// $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}globtrade_messages" );