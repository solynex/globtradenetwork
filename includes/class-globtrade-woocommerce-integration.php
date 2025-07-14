<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Globtrade_WooCommerce_Integration {

    public function __construct() {
        // <<< هذا هو السطر الوحيد الذي تم تعديله >>>
        add_action( 'woocommerce_order_status_processing', array( $this, 'on_order_processed' ), 10, 1 );
        add_action( 'woocommerce_order_status_completed', array( $this, 'on_order_processed' ), 10, 1 );
    }

    public function on_order_processed( $order_id ) {
        $order = wc_get_order( $order_id );
        if ( ! $order ) {
            return;
        }

        $user_id = $order->get_user_id();
        if ( ! $user_id ) {
            return;
        }

        // نتأكد أننا لم نقم بتفعيل باقة لهذا الطلب من قبل
        if ( get_post_meta( $order_id, '_globtrade_package_activated', true ) ) {
            return;
        }

        $package_key = '';
        $credits = 0;

        foreach ( $order->get_items() as $item ) {
            $product_id = $item->get_product_id();
            
            switch ( $product_id ) {
                case 158:
                    $package_key = 'exporter-package-1';
                    $credits = 60; 
                    break;
                case 159:
                    $package_key = 'exporter-package-2';
                    $credits = 180;
                    break;
                case 160:
                    $package_key = 'exporter-package-3';
                    $credits = 350;
                    break;
            }
        }

        if ( ! empty( $package_key ) ) {
            $end_date = date('Y-m-d H:i:s', strtotime('+1 year'));

            update_user_meta( $user_id, 'package', $package_key );
            update_user_meta( $user_id, 'credits', $credits );
            update_user_meta( $user_id, 'subscription_end_date', $end_date );
            delete_user_meta( $user_id, '_pending_package_id' );
            
            // نضع علامة بأن هذا الطلب قد تم تفعيله
            $order->update_meta_data( '_globtrade_package_activated', 'yes' );
            $order->save();
        }
    }
}