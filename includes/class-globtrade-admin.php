<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Globtrade_Admin {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'register_admin_pages' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    public function register_settings() {
        register_setting( 'globtrade_settings_group', 'globtrade_logo_url' );

        add_settings_section(
            'globtrade_general_section',
            'General Settings',
            null,
            'globtrade-dashboard'
        );

        add_settings_field(
            'globtrade_logo_url',
            'Platform Logo',
            array( $this, 'logo_field_html' ),
            'globtrade-dashboard',
            'globtrade_general_section'
        );
    }

    public function register_admin_pages() {
        add_menu_page(
            __( 'GLOBTRADE', 'globtrade' ),
            __( 'GLOBTRADE', 'globtrade' ),
            'manage_options',
            'globtrade-dashboard',
            array( $this, 'settings_page_html' ),
            'dashicons-globe-alt',
            25
        );

        add_submenu_page( 'globtrade-dashboard', __( 'Settings', 'globtrade' ), __( 'Settings', 'globtrade' ), 'manage_options', 'globtrade-dashboard', array( $this, 'settings_page_html' ) );
        add_submenu_page( 'globtrade-dashboard', __( 'Exporter Users', 'globtrade' ), __( 'Exporter Users', 'globtrade' ), 'manage_options', 'globtrade-exporter-users', array( $this, 'exporter_users_page_html' ) );
        add_submenu_page( 'globtrade-dashboard', __( 'Importer Users', 'globtrade' ), __( 'Importer Users', 'globtrade' ), 'manage_options', 'globtrade-importer-users', array( $this, 'importer_users_page_html' ) );
    }

    public function settings_page_html() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( 'globtrade_settings_group' );
                do_settings_sections( 'globtrade-dashboard' );
                submit_button( 'Save Settings' );
                ?>
            </form>
        </div>
        <?php
    }

    public function logo_field_html() {
        $logo_url = get_option( 'globtrade_logo_url' );
        ?>
        <input type="text" name="globtrade_logo_url" id="globtrade_logo_url" value="<?php echo esc_attr( $logo_url ); ?>" class="regular-text">
        <input type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload Logo">
        <p class="description">Upload or select a logo for your platform.</p>
        <div style="margin-top:10px;">
            <img id="logo-preview" src="<?php echo esc_attr( $logo_url ); ?>" style="max-width:100px; max-height:100px; display: <?php echo $logo_url ? 'block' : 'none'; ?>;">
        </div>
        <?php
    }

    public function exporter_users_page_html() {
        $this->display_users_table( 'exporter' );
    }

    public function importer_users_page_html() {
        $this->display_users_table( 'importer' );
    }
    
    private function display_users_table( $role ) {
        echo '<div class="wrap"><h1>' . esc_html( ucfirst( $role ) . 's List', 'globtrade' ) . '</h1></div>';
        
        $users = get_users( array( 'role' => $role ) );

        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>Username</th><th>Email</th><th>Registered</th></tr></thead>';
        echo '<tbody>';

        if ( ! empty( $users ) ) {
            foreach ( $users as $user ) {
                echo '<tr>';
                echo '<td>' . esc_html( $user->user_login ) . '</td>';
                echo '<td>' . esc_html( $user->user_email ) . '</td>';
                echo '<td>' . esc_html( date( 'Y-m-d', strtotime( $user->user_registered ) ) ) . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="3">No users found.</td></tr>';
        }

        echo '</tbody></table>';
    }
}