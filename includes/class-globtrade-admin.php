<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Globtrade_Admin {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'register_admin_pages' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'show_user_profile', array( $this, 'add_verification_fields_to_profile' ) );
        add_action( 'edit_user_profile', array( $this, 'add_verification_fields_to_profile' ) );
        add_action( 'personal_options_update', array( $this, 'save_verification_fields' ) );
        add_action( 'edit_user_profile_update', array( $this, 'save_verification_fields' ) );
    }

    public function register_settings() {
        register_setting( 'globtrade_settings_group', 'globtrade_logo_url' );
        register_setting( 'globtrade_settings_group', 'globtrade_seal_url' ); 

        add_settings_section('globtrade_general_section', 'General Settings', null, 'globtrade-dashboard');

        add_settings_field('globtrade_logo_url', 'Platform Logo', array( $this, 'logo_field_html' ), 'globtrade-dashboard', 'globtrade_general_section');
        add_settings_field('globtrade_seal_url', 'Platform Seal Image', array( $this, 'seal_field_html' ), 'globtrade-dashboard', 'globtrade_general_section');
    }

    public function register_admin_pages() {
        add_menu_page('GLOBTRADE', 'GLOBTRADE', 'manage_options', 'globtrade-dashboard', array( $this, 'settings_page_html' ), 'dashicons-globe-alt', 25);
        add_submenu_page( 'globtrade-dashboard', 'Settings', 'Settings', 'manage_options', 'globtrade-dashboard' );
        add_submenu_page( 'globtrade-dashboard', 'Exporter Users', 'Exporter Users', 'manage_options', 'globtrade-exporter-users', array( $this, 'exporter_users_page_html' ) );
        add_submenu_page( 'globtrade-dashboard', 'Importer Users', 'Importer Users', 'manage_options', 'globtrade-importer-users', array( $this, 'importer_users_page_html' ) );
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
        <div style="margin-top:10px;"><img id="logo-preview" src="<?php echo esc_attr( $logo_url ); ?>" style="max-width:100px; max-height:100px; display: <?php echo $logo_url ? 'block' : 'none'; ?>;"></div>
        <?php
    }
    
    public function seal_field_html() {
        $seal_url = get_option( 'globtrade_seal_url' );
        ?>
        <input type="text" name="globtrade_seal_url" id="globtrade_seal_url" value="<?php echo esc_attr( $seal_url ); ?>" class="regular-text">
        <input type="button" name="upload-seal-btn" id="upload-seal-btn" class="button-secondary" value="Upload Seal">
        <div style="margin-top:10px;"><img id="seal-preview" src="<?php echo esc_attr( $seal_url ); ?>" style="max-width:100px; max-height:100px; display: <?php echo $seal_url ? 'block' : 'none'; ?>;"></div>
        <?php
    }

    public function exporter_users_page_html() {
        echo '<div class="wrap"><h1>Exporter Users</h1>';
        $list_table = new Globtrade_Users_List_Table('exporter');
        $list_table->prepare_items();
        $list_table->display();
        echo '</div>';
    }

    public function importer_users_page_html() {
        echo '<div class="wrap"><h1>Importer Users</h1>';
        $list_table = new Globtrade_Users_List_Table('importer');
        $list_table->prepare_items();
        $list_table->display();
        echo '</div>';
    }

    public function add_verification_fields_to_profile( $user ) {
        if ( !in_array('importer', $user->roles) && !in_array('exporter', $user->roles) ) {
            return;
        }
        $docs = get_user_meta( $user->ID, 'verification_docs', true );
        if (!is_array($docs)) $docs = [];
        ?>
        <h3>Account Verification Documents</h3>
        <table class="form-table">
            <?php
            $doc_keys = ['commercial_register' => 'Commercial Register', 'tax_card' => 'Tax Card', 'iban_doc' => 'IBAN Document'];
            foreach ($doc_keys as $key => $label) :
                $doc = $docs[$key] ?? null;
            ?>
            <tr>
                <th><label for="<?php echo $key; ?>_status"><?php echo $label; ?></label></th>
                <td>
                    <?php if ($doc && isset($doc['url'])) : ?>
                        <div style="margin-bottom: 5px;">
                            <a href="<?php echo esc_url($doc['url']); ?>" target="_blank">View: <?php echo esc_html($doc['name']); ?></a>
                        </div>
                        <select name="verification_docs[<?php echo $key; ?>][status]" id="<?php echo $key; ?>_status">
                            <option value="Pending" <?php selected($doc['status'], 'Pending'); ?>>Pending Review</option>
                            <option value="Verified" <?php selected($doc['status'], 'Verified'); ?>>Verified</option>
                            <option value="Rejected" <?php selected($doc['status'], 'Rejected'); ?>>Rejected</option>
                        </select>
                        <input type="hidden" name="verification_docs[<?php echo $key; ?>][url]" value="<?php echo esc_attr($doc['url']); ?>" />
                        <input type="hidden" name="verification_docs[<?php echo $key; ?>][file]" value="<?php echo esc_attr($doc['file'] ?? ''); ?>" />
                        <input type="hidden" name="verification_docs[<?php echo $key; ?>][type]" value="<?php echo esc_attr($doc['type']); ?>" />
                        <input type="hidden" name="verification_docs[<?php echo $key; ?>][name]" value="<?php echo esc_attr($doc['name']); ?>" />
                    <?php else: ?>
                        <span>No file uploaded.</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php
    }

    public function save_verification_fields( $user_id ) {
        if ( !current_user_can( 'edit_user', $user_id ) ) { 
            return false; 
        }
        if (isset($_POST['verification_docs'])) {
            update_user_meta( $user_id, 'verification_docs', $_POST['verification_docs'] );
        }
    }
}