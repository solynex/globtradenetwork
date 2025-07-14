<?php
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Globtrade_Users_List_Table extends WP_List_Table {

    private $role;

    function __construct($role) {
        $this->role = $role;
        parent::__construct( [
            'singular' => __( 'User', 'globtrade' ),
            'plural'   => __( 'Users', 'globtrade' ),
            'ajax'     => false
        ] );
    }

    function get_columns() {
        return [
            'company_name' => __( 'Company Name', 'globtrade' ),
            'user_email'   => __( 'Email', 'globtrade' ),
            'country'      => __( 'Country', 'globtrade' ),
            'verification' => __( 'Verification Status', 'globtrade' ),
            'user_registered' => __( 'Registered', 'globtrade' ),
        ];
    }

    function prepare_items() {
        $this->_column_headers = array($this->get_columns(), array(), $this->get_sortable_columns());

        $users = get_users(['role' => $this->role]);
        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'ID' => $user->ID,
                'company_name' => get_user_meta($user->ID, 'company_name', true) ?: $user->display_name,
                'user_email' => $user->user_email,
                'country' => get_user_meta($user->ID, 'country', true) ?: 'N/A',
                'user_registered' => $user->user_registered,
            ];
        }
        $this->items = $data;
    }

    function column_default( $item, $column_name ) {
        switch( $column_name ) {
            case 'company_name':
                $edit_link = admin_url('user-edit.php?user_id=' . $item['ID']);
                return '<strong><a href="' . $edit_link . '">' . esc_html($item[$column_name]) . '</a></strong>';
            case 'user_email':
            case 'country':
            case 'user_registered':
                return esc_html( $item[ $column_name ] );
            case 'verification':
                $docs = get_user_meta($item['ID'], 'verification_docs', true);
                if (!is_array($docs)) $docs = [];
                $verified_count = 0;
                $pending_count = 0;

                foreach ($docs as $doc) {
                    if (isset($doc['status'])) {
                       if ($doc['status'] === 'Verified') $verified_count++;
                       if ($doc['status'] === 'Pending') $pending_count++;
                    }
                }

                if ($verified_count === 3) {
                     return '<span style="color:green; font-weight:bold;">Fully Verified</span>';
                } elseif ($pending_count > 0 || $verified_count > 0) {
                     return '<span style="color:orange; font-weight:bold;">Pending Review</span>';
                } else {
                     return '<span style="color:red;">Not Verified</span>';
                }
            default:
                return print_r( $item, true ) ;
        }
    }
}