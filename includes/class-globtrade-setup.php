<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Globtrade_Setup {

    public function __construct() {
        add_action( 'init', array( $this, 'add_custom_roles' ) );
        register_activation_hook( GLOBTRADE_PLUGIN_PATH . 'globtrade.php', array( $this, 'activate' ) );
    }

    public function activate() {
        flush_rewrite_rules();
    }

    public function add_custom_roles() {
        if ( ! get_role( 'importer' ) ) {
            add_role(
                'importer',
                __( 'Importer', 'globtrade' ),
                get_role( 'subscriber' )->capabilities
            );
        }

        if ( ! get_role( 'exporter' ) ) {
            add_role(
                'exporter',
                __( 'Exporter', 'globtrade' ),
                get_role( 'subscriber' )->capabilities
            );
        }
    }
}