<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Globtrade_API {
     
    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    public function register_routes() {
        register_rest_route('globtrade/v1', '/conversations/upload-file', ['methods' => 'POST', 'callback' => [$this, 'handle_chat_file_upload'], 'permission_callback' => [$this, 'is_user_logged_in']]);
        register_rest_route('globtrade/v1', '/dashboard-stats', ['methods' => 'GET', 'callback' => [$this, 'get_dashboard_stats'], 'permission_callback' => [$this, 'is_user_logged_in']]);
        register_rest_route('globtrade/v1', '/users/me/verification-docs', ['methods' => 'POST', 'callback' => [$this, 'handle_verification_upload'], 'permission_callback' => [$this, 'is_user_logged_in']]);
        register_rest_route('globtrade/v1', '/marketing-offers', [
            ['methods' => 'POST', 'callback' => [$this, 'create_marketing_offer'], 'permission_callback' => [$this, 'is_p3_exporter']],
            ['methods' => 'GET', 'callback' => [$this, 'get_marketing_offers'], 'permission_callback' => [$this, 'can_user_access_community']],
        ]);
        register_rest_route('globtrade/v1', '/marketing-offers/(?P<id>\d+)/handle', [
            ['methods' => 'POST', 'callback' => [$this, 'handle_marketing_offer_action'], 'permission_callback' => [$this, 'is_user_importer']],
        ]);
    register_rest_route('globtrade/v1', '/conversations/initiate', ['methods' => 'POST', 'callback' => [$this, 'initiate_conversation'], 'permission_callback' => [$this, 'is_user_logged_in']]);
    register_rest_route('globtrade/v1', '/users/(?P<id>\d+)', ['methods' => 'GET', 'callback' => [$this, 'get_single_user'], 'permission_callback' => [$this, 'is_user_logged_in']]);
    register_rest_route('globtrade/v1', '/users', ['methods' => 'GET', 'callback' => [$this, 'get_community_users'], 'permission_callback' => [$this, 'can_user_access_community']]);
    register_rest_route('globtrade/v1', '/agreements', ['methods' => 'GET', 'callback' => [$this, 'get_agreements'], 'permission_callback' => [$this, 'is_user_logged_in']]);
    register_rest_route( 'globtrade/v1', '/initiate-payment', ['methods' => 'POST', 'callback' => [$this, 'initiate_package_payment'], 'permission_callback' => [$this, 'is_user_logged_in']]);
    register_rest_route('globtrade/v1', '/conversations', ['methods' => 'GET', 'callback' => [$this, 'get_user_conversations'], 'permission_callback' => [$this, 'is_user_logged_in']]);

        
    register_rest_route('globtrade/v1', '/public/requests', ['methods' => 'GET', 'callback' => [$this, 'get_public_requests'], 'permission_callback' => '__return_true']);
    register_rest_route('globtrade/v1', '/register', ['methods' => 'POST', 'callback' => [$this, 'handle_registration'], 'permission_callback' => '__return_true']);
    register_rest_route('globtrade/v1', '/login', ['methods' => 'POST', 'callback' => [$this, 'handle_login'], 'permission_callback' => '__return_true']);

    register_rest_route('globtrade/v1', '/users/me', [
        ['methods' => 'GET', 'callback' => [$this, 'get_current_user_data'], 'permission_callback' => [$this, 'is_user_logged_in']],
        ['methods' => 'POST', 'callback' => [$this, 'update_current_user_data'], 'permission_callback' => [$this, 'is_user_logged_in']]
    ]);
    register_rest_route('globtrade/v1', '/users/me/logo-upload', ['methods' => 'POST', 'callback' => [$this, 'handle_profile_logo_upload'], 'permission_callback' => [$this, 'is_user_logged_in']]);

    
    register_rest_route('globtrade/v1', '/requests', [
        ['methods' => 'POST', 'callback' => [$this, 'create_request'], 'permission_callback' => [$this, 'check_user_subscription_permission']],
        ['methods' => 'GET', 'callback' => [$this, 'get_all_requests'], 'permission_callback' => [$this, 'check_user_subscription_permission']]
    ]);
    register_rest_route('globtrade/v1', '/requests/me', ['methods' => 'GET', 'callback' => [$this, 'get_my_requests'], 'permission_callback' => [$this, 'is_user_importer']]);
    register_rest_route('globtrade/v1', '/requests/(?P<id>\d+)', [
        ['methods' => 'GET', 'callback' => [$this, 'get_single_request'], 'permission_callback' => [$this, 'can_user_view_request']],
        ['methods' => 'PUT', 'callback' => [$this, 'update_request'], 'permission_callback' => [$this, 'can_user_manage_request']],
        ['methods' => 'DELETE', 'callback' => [$this, 'delete_request'], 'permission_callback' => [$this, 'can_user_manage_request']]
    ]);

    register_rest_route('globtrade/v1', '/offers', ['methods' => 'POST', 'callback' => [$this, 'create_offer'], 'permission_callback' => [$this, 'check_user_subscription_permission']]);
    register_rest_route('globtrade/v1', '/offers/me', ['methods' => 'GET', 'callback' => [$this, 'get_my_offers'], 'permission_callback' => [$this, 'is_user_exporter']]);
    
    register_rest_route('globtrade/v1', '/offers/(?P<id>\d+)', [
        ['methods' => 'GET', 'callback' => [$this, 'get_single_offer'], 'permission_callback' => [$this, 'can_user_manage_offer']],
        ['methods' => 'PUT', 'callback' => [$this, 'update_offer'], 'permission_callback' => [$this, 'can_user_manage_offer']],
        ['methods' => 'DELETE', 'callback' => [$this, 'delete_offer'], 'permission_callback' => [$this, 'can_user_manage_offer']]
    ]);
    
    register_rest_route('globtrade/v1', '/requests/(?P<request_id>\d+)/offers', ['methods' => 'GET', 'callback' => [$this, 'get_offers_for_request'], 'permission_callback' => [$this, 'can_user_view_request_offers']]);
    register_rest_route('globtrade/v1', '/offers/(?P<id>\d+)/accept', ['methods' => 'POST', 'callback' => [$this, 'handle_accept_offer'], 'permission_callback' => [$this, 'can_user_manage_offer_status']]);
    register_rest_route('globtrade/v1', '/offers/(?P<id>\d+)/reject', ['methods' => 'POST', 'callback' => [$this, 'handle_reject_offer'], 'permission_callback' => [$this, 'can_user_manage_offer_status']]);

    register_rest_route('globtrade/v1', '/conversations', ['methods' => 'GET', 'callback' => [$this, 'get_user_conversations'], 'permission_callback' => [$this, 'is_user_logged_in']]);
    
    register_rest_route('globtrade/v1', '/conversations/(?P<id>\d+)/messages', [
        ['methods' => 'GET', 'callback' => [$this, 'get_conversation_messages'], 'permission_callback' => [$this, 'can_user_view_conversation']],
        ['methods' => 'POST', 'callback' => [$this, 'add_conversation_message'], 'permission_callback' => [$this, 'can_user_view_conversation']]
    ]);

    register_rest_route( 'globtrade/v1', '/packages', ['methods'  => 'GET', 'callback' => [$this, 'get_available_packages'], 'permission_callback' => [$this, 'is_user_logged_in']]);
    register_rest_route( 'globtrade/v1', '/users/me/subscription', ['methods'  => 'GET', 'callback' => [$this, 'get_my_subscription_data'], 'permission_callback' => [$this, 'is_user_logged_in']]);
}

    public function is_user_logged_in() {
        return is_user_logged_in();
    }

    public function is_user_importer() {
        return current_user_can('importer');
    }

    public function is_user_exporter() {
        return current_user_can('exporter');
    }

    public function can_user_manage_request( $request ) {
        $post = get_post( $request['id'] );
        return $post && $post->post_author == get_current_user_id();
    }

    public function can_user_view_request( $request ) {
        if ( current_user_can('exporter') ) {
            return true;
        }
        $post = get_post( $request['id'] );
        return $post && $post->post_author == get_current_user_id();
    }

    public function can_user_manage_offer( $request ) {
        $post = get_post( $request['id'] );
        return $post && $post->post_author == get_current_user_id();
    }

  public function handle_registration( $request ) {
    $params = $request->get_params();
    $email = sanitize_email( $params['email'] );
    if ( email_exists( $email ) ) {
        return new WP_Error( 'email_exists', 'This email is already registered.', array( 'status' => 400 ) );
    }
    $user_id = wp_create_user( $email, $params['password'], $email );
    if ( is_wp_error( $user_id ) ) {
        return $user_id;
    }

    $role = sanitize_text_field( $params['type'] );
    wp_update_user( array( 'ID' => $user_id, 'role' => $role, 'display_name' => sanitize_text_field($params['company']) ) );
    update_user_meta( $user_id, 'company_name', sanitize_text_field( $params['company'] ) );
    update_user_meta( $user_id, 'country', sanitize_text_field( $params['country'] ) );
    update_user_meta( $user_id, 'country_code', sanitize_text_field( $params['country_code'] ) );
    update_user_meta( $user_id, 'business_category', sanitize_text_field( $params['category'] ) );

    $package_product_id = absint( $params['package'] );

    if ($role === 'importer') {
        update_user_meta( $user_id, 'package', 'importer-package' );
        update_user_meta( $user_id, 'credits', 'Unlimited' );
        $end_date = date('Y-m-d H:i:s', strtotime('+1 year'));
        update_user_meta( $user_id, 'subscription_end_date', $end_date );
    }

    if ($role === 'exporter' && $package_product_id) {
        update_user_meta( $user_id, '_pending_package_id', $package_product_id );
    }

    wp_set_auth_cookie( $user_id, true );
    return new WP_REST_Response( array( 'status' => 'success', 'redirect_url' => home_url('/dashboard') ), 200 );
}

    public function handle_login( $request ) {
        $creds = array(
            'user_login'    => sanitize_email( $request['email'] ),
            'user_password' => $request['password'],
            'remember'      => true
        );
        $user = wp_signon( $creds, false );

        if ( is_wp_error( $user ) ) {
            return new WP_Error( 'login_failed', 'Invalid username or password.', array( 'status' => 401 ) );
        }
        return new WP_REST_Response( array( 'status' => 'success', 'message' => 'User logged in successfully.' ), 200 );
    }

   public function get_current_user_data() {
    $user_id = get_current_user_id();
    $user_data = get_userdata( $user_id );
    $user_meta = get_user_meta( $user_id );
    $roles = (array) $user_data->roles;

    $credits = get_user_meta( $user_id, 'credits', true ) ?: '0';
    if (in_array('importer', $roles)) {
        $credits = 'Unlimited';
    }

    $days_remaining = 'N/A';
    $end_date_str = get_user_meta( $user_id, 'subscription_end_date', true );
    if ($end_date_str) {
        $end_date = new DateTime($end_date_str);
        $now = new DateTime();
        if ($end_date > $now) {
            $interval = $now->diff($end_date);
            $days_remaining = $interval->format('%a days');
        } else {
            $days_remaining = 'Expired';
        }
    }

    $verification_docs = get_user_meta($user_id, 'verification_docs', true) ?: [];
    
    $response_data = array(
        'id'                          => $user_id,
        'email'                       => $user_data->user_email,
        'company_name'                => $user_meta['company_name'][0] ?? '',
        'role'                        => $roles[0] ?? '',
        'phone'                       => $user_meta['phone'][0] ?? '',
        'country'                     => $user_meta['country'][0] ?? '',
        'country_code'                => $user_meta['country_code'][0] ?? '',
        'business_category'           => $user_meta['business_category'][0] ?? '',
        'commercial_registration_no'  => $user_meta['commercial_registration_no'][0] ?? '',
        'website'                     => $user_meta['website'][0] ?? '',
        'address'                     => $user_meta['address'][0] ?? '',
        'company_description'         => $user_meta['company_description'][0] ?? '',
        'profile_logo_url'            => get_user_meta( $user_id, 'profile_logo_url', true ) ?? '',
        'package'                     => get_user_meta( $user_id, 'package', true ) ?: '',
        'credits'                     => $credits,
        'days_remaining'              => $days_remaining,
        'verificationDocs'            => $verification_docs,
    );
    return new WP_REST_Response( $response_data, 200 );
}

    public function create_request( $request ) {
        $params = $request->get_params();
        $post_data = array(
            'post_title'   => sanitize_text_field( $params['request-product'] ),
            'post_content' => sanitize_textarea_field( $params['request-description'] ),
            'post_status'  => 'publish',
            'post_type'    => 'gt_request',
            'post_author'  => get_current_user_id(),
        );
        $post_id = wp_insert_post( $post_data, true );
        if ( is_wp_error( $post_id ) ) return $post_id;
        foreach ($params as $key => $value) {
            if ( strpos($key, 'request-') === 0 ) update_post_meta( $post_id, $key, sanitize_text_field( $value ) );
        }
        return new WP_REST_Response( array( 'status' => 'success', 'post_id' => $post_id ), 201 );
    }

   public function get_my_requests() {
    $requests = array();
    $query = new WP_Query( array(
        'post_type' => 'gt_request', 
        'author' => get_current_user_id(), 
        'posts_per_page' => -1,
        'post_status' => array( 'publish', 'private', 'draft', 'pending', 'completed' ) 
    ) );
    if ( $query->have_posts() ) {
        while( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $requests[] = array(
                'id'     => $post_id,
                'title'  => get_the_title(),
                'date'   => get_the_date( 'Y-m-d', $post_id ),
                'status' => get_post_status( $post_id ),
                'meta'   => get_post_meta( $post_id )
            );
        }
    }
    wp_reset_postdata();
    return new WP_REST_Response( $requests, 200 );
}

    public function get_all_requests( $request ) {
    $category_filter = $request->get_param('category');
    
    $query_args = array(
        'post_type' => 'gt_request', 
        'post_status' => 'publish', 
        'posts_per_page' => -1
    );

    if ( ! empty( $category_filter ) ) {
        $query_args['meta_query'] = array(
            array(
                'key'     => 'request-category',
                'value'   => sanitize_text_field( $category_filter ),
                'compare' => '=',
            ),
        );
    }

    $requests = array();
    $query = new WP_Query($query_args);

    if ( $query->have_posts() ) {
        while( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $author_id = get_post_field( 'post_author', $post_id );
            $importer_data = get_userdata( $author_id );
            $importer_meta = get_user_meta( $author_id );
            $requests[] = array(
                'id'         => $post_id,
                'title'      => get_the_title(),
                'content'    => get_the_content(),
                'date'       => get_the_date( 'Y-m-d', $post_id ),
                'status'     => get_post_status( $post_id ),
                'meta'       => get_post_meta( $post_id ),
                'importer'   => array(
                    'company_name' => $importer_meta['company_name'][0] ?? $importer_data->display_name,
                    'country'      => $importer_meta['country'][0] ?? 'N/A',
                    'country_code' => $importer_meta['country_code'][0] ?? ''
                )
            );
        }
    }
    wp_reset_postdata();
    return new WP_REST_Response( $requests, 200 );
}

    public function get_single_request( $request ) {
        $post_id = $request['id'];
        $post = get_post( $post_id );
        return new WP_REST_Response( array(
            'id'      => $post->ID,
            'title'   => $post->post_title,
            'content' => $post->post_content,
            'status'  => $post->post_status,
            'meta'    => get_post_meta( $post_id )
        ), 200 );
    }

    public function update_request( $request ) {
        $post_id = $request['id'];
        $params = $request->get_params();
        $post_data = array( 'ID' => $post_id );
        if ( isset($params['request-product']) ) $post_data['post_title'] = sanitize_text_field( $params['request-product'] );
        if ( isset($params['request-description']) ) $post_data['post_content'] = sanitize_textarea_field( $params['request-description'] );
        if( !empty($post_data) ) wp_update_post( $post_data );
        foreach ($params as $key => $value) {
            if ( strpos($key, 'request-') === 0 ) update_post_meta( $post_id, $key, sanitize_text_field( $value ) );
        }
        return new WP_REST_Response( array( 'status' => 'success', 'message' => 'Request updated.' ), 200 );
    }

    public function delete_request( $request ) {
        $post_id = $request['id'];
        $deleted = wp_delete_post( $post_id, true );
        if ( ! $deleted ) return new WP_Error( 'delete_failed', 'Failed to delete request.', array( 'status' => 500 ) );
        return new WP_REST_Response( array( 'status' => 'success', 'message' => 'Request deleted.' ), 200 );
    }

   public function create_offer( $request ) {
    $user_id = get_current_user_id();
    $params = $request->get_params();
    if ( empty( $params['requestId'] ) || empty( $params['offerData'] ) ) return new WP_Error( 'bad_request', 'Missing parameters.', array( 'status' => 400 ) );
    
    $request_id = absint( $params['requestId'] );
    
    // Find any existing offers from this user for this request
    $existing_offers_query = new WP_Query([
        'post_type' => 'gt_offer',
        'author' => $user_id,
        'meta_key' => 'request_id',
        'meta_value' => $request_id,
        'posts_per_page' => -1
    ]);

    if ($existing_offers_query->have_posts()) {
        foreach($existing_offers_query->posts as $offer_post) {
            $status = get_post_meta($offer_post->ID, 'status', true);
            // If offer was rejected, delete it to allow a new one
            if ($status === 'rejected') {
                wp_delete_post($offer_post->ID, true);
            } else {
                // If an active or accepted offer exists, block submission
                return new WP_Error('offer_exists', 'You already have an active offer for this request. Please edit the existing one.', array('status' => 409));
            }
        }
    }

    $original_request = get_post( $request_id );
    if ( ! $original_request || $original_request->post_type !== 'gt_request' ) return new WP_Error( 'not_found', 'Request not found.', array( 'status' => 404 ) );
    
    $post_data = array(
        'post_title'   => 'Offer on: ' . $original_request->post_title,
        'post_status'  => 'publish',
        'post_type'    => 'gt_offer',
        'post_author'  => $user_id,
    );
    $offer_id = wp_insert_post( $post_data, true );
    if ( is_wp_error( $offer_id ) ) return $offer_id;
    
    update_post_meta( $offer_id, 'request_id', $request_id );
    foreach ( $params['offerData'] as $key => $value ) {
        update_post_meta( $offer_id, sanitize_key( $key ), sanitize_text_field( $value ) );
    }
    return new WP_REST_Response( array( 'status' => 'success', 'message' => 'Offer submitted.' ), 201 );
}

    public function get_my_offers() {
        $offers = array();
        $query = new WP_Query( array('post_type' => 'gt_offer', 'author' => get_current_user_id(), 'posts_per_page' => -1) );
        if ( $query->have_posts() ) {
            while( $query->have_posts() ) {
                $query->the_post();
                $post_id = get_the_ID();
                $request_id = get_post_meta( $post_id, 'request_id', true );
                $original_request = get_post( $request_id );
                $importer = get_userdata( $original_request->post_author );
                $offers[] = array(
                    'id'      => $post_id,
                    'status'  => get_post_status( $post_id ),
                    'date'    => get_the_date( 'Y-m-d', $post_id ),
                    'meta'    => get_post_meta( $post_id ),
                    'request' => array('title' => $original_request ? $original_request->post_title : 'N/A'),
                    'importer' => array('company_name' => get_user_meta( $original_request->post_author, 'company_name', true ) ?: $importer->display_name)
                );
            }
        }
        wp_reset_postdata();
        return new WP_REST_Response( $offers, 200 );
    }

    public function update_offer( $request ) {
        $offer_id = $request['id'];
        $params = $request->get_params();
        foreach ( $params['offerData'] as $key => $value ) {
            update_post_meta( $offer_id, sanitize_key( $key ), sanitize_text_field( $value ) );
        }
        return new WP_REST_Response( array( 'status' => 'success', 'message' => 'Offer updated.' ), 200 );
    }

    public function delete_offer( $request ) {
        $offer_id = $request['id'];
        $deleted = wp_delete_post( $offer_id, true );
        if ( ! $deleted ) return new WP_Error( 'delete_failed', 'Failed to delete offer.', array( 'status' => 500 ) );
        return new WP_REST_Response( array( 'status' => 'success', 'message' => 'Offer deleted.' ), 200 );
    }

    public function get_public_requests() {
        $requests = array();
        $query = new WP_Query( array(
            'post_type'      => 'gt_request',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
        ) );
        if ( $query->have_posts() ) {
            while( $query->have_posts() ) {
                $query->the_post();
                $post_id = get_the_ID();
                $author_id = get_post_field( 'post_author', $post_id );
                $importer_meta = get_user_meta( $author_id );
                $requests[] = array(
                    'id'           => $post_id,
                    'title'        => get_the_title(),
                    'content'      => get_the_content(),
                    'date'         => get_the_date( 'Y-m-d', $post_id ),
                    'status'       => get_post_status( $post_id ),
                    'meta'         => get_post_meta( $post_id ),
                    'importer'     => array(
                        'company_name' => 'Importer from ' . ($importer_meta['country'][0] ?? 'N/A'),
                        'country'      => $importer_meta['country'][0] ?? 'N/A',
                        'country_code' => $importer_meta['country_code'][0] ?? ''
                    )
                );
            }
        }
        wp_reset_postdata();
        return new WP_REST_Response( $requests, 200 );
    }

    public function handle_profile_logo_upload( $request ) {
        $user_id = get_current_user_id();
        if ( !$user_id ) {
            return new WP_Error( 'rest_not_logged_in', 'You are not currently logged in.', array( 'status' => 401 ) );
        }
        if ( ! function_exists( 'wp_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }
        if ( ! function_exists( 'media_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            require_once( ABSPATH . 'wp-admin/includes/media.php' );
        }
        $files = $request->get_file_params();
        if ( empty( $files['profile_logo'] ) ) {
            return new WP_Error( 'no_file_uploaded', 'No profile logo file was uploaded.', array( 'status' => 400 ) );
        }
        $uploaded_file = $files['profile_logo'];
        $allowed_types = array( 'image/jpeg', 'image/png', 'image/gif' );
        if ( ! in_array( $uploaded_file['type'], $allowed_types ) ) {
            return new WP_Error( 'invalid_file_type', 'Invalid file type. Only JPG, PNG, and GIF images are allowed.', array( 'status' => 400 ) );
        }
        $upload_overrides = array( 'test_form' => false );
        $movefile = wp_handle_upload( $uploaded_file, $upload_overrides );
        if ( $movefile && ! isset( $movefile['error'] ) ) {
            $attachment_id = wp_insert_attachment( array(
                'guid'           => $movefile['url'],
                'post_mime_type' => $movefile['type'],
                'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $movefile['file'] ) ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            ), $movefile['file'], $user_id );
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            $attachment_data = wp_generate_attachment_metadata( $attachment_id, $movefile['file'] );
            wp_update_attachment_metadata( $attachment_id, $attachment_data );
            update_user_meta( $user_id, 'profile_logo_url', $movefile['url'] );
            return new WP_REST_Response( array( 'status' => 'success', 'message' => 'Logo uploaded successfully.', 'logo_url' => $movefile['url'] ), 200 );
        } else {
            return new WP_Error( 'upload_failed', $movefile['error'], array( 'status' => 500 ) );
        }
    }

    public function get_offers_for_request( $request ) {
        $request_id = (int) $request['request_id'];
        $offers_query = new WP_Query(array(
            'post_type'  => 'gt_offer',
            'meta_key'   => 'request_id',
            'meta_value' => $request_id,
            'posts_per_page' => -1,
        ));
        $offers = array();
        if ( $offers_query->have_posts() ) {
            while ( $offers_query->have_posts() ) {
                $offers_query->the_post();
                $offer_id = get_the_ID();
                $exporter_id = get_post_field( 'post_author', $offer_id );
                $exporter_data = get_userdata( $exporter_id );
                $exporter_meta = get_user_meta( $exporter_id );
                $offers[] = array(
                    'id'   => $offer_id,
                    'meta' => get_post_meta( $offer_id ),
                    'request_title' => get_the_title( $request_id ),
                    'exporter' => array(
                        'id' => $exporter_id,
                        'company_name' => $exporter_meta['company_name'][0] ?? $exporter_data->display_name,
                    )
                );
            }
        }
        wp_reset_postdata();
        return new WP_REST_Response( $offers, 200 );
    }

    public function can_user_view_request_offers( $request ) {
        $request_id = (int) $request['request_id'];
        $original_request = get_post( $request_id );
        return $original_request && $original_request->post_author == get_current_user_id();
    }

    public function can_user_manage_offer_status( $request ) {
        $offer_id = (int) $request['id'];
        $offer = get_post( $offer_id );
        if ( ! $offer || 'gt_offer' !== $offer->post_type ) {
            return false;
        }
        $request_id = get_post_meta( $offer_id, 'request_id', true );
        if ( ! $request_id ) {
            return false;
        }
        $original_request = get_post( $request_id );
        return $original_request && $original_request->post_author == get_current_user_id();
    }

    public function handle_accept_offer( $request ) {
    $offer_id = (int) $request['id'];
    $request_id = get_post_meta( $offer_id, 'request_id', true );
    $importer_id = get_current_user_id();
    $exporter_id = get_post_field( 'post_author', $offer_id );
    
    update_post_meta( $offer_id, 'status', 'accepted' );
    
    if ( $request_id ) {
        wp_update_post([ 'ID' => $request_id, 'post_status' => 'completed' ]);
    }
    
    $conversation_id = $this->get_or_create_conversation($importer_id, $exporter_id, $request_id);
    $this->_add_system_message($conversation_id, $importer_id, "I have accepted your offer. Let's proceed.");

    return new WP_REST_Response( [ 'status' => 'success', 'conversation_id' => $conversation_id ], 200 );
}

public function handle_reject_offer( $request ) {
    $offer_id = (int) $request['id'];
    $request_id = get_post_meta( $offer_id, 'request_id', true );
    $importer_id = get_current_user_id();
    $exporter_id = get_post_field( 'post_author', $offer_id );
    $params = $request->get_json_params();
    $reason = isset( $params['reason'] ) ? sanitize_text_field( $params['reason'] ) : 'No reason provided';
    update_post_meta( $offer_id, 'status', 'rejected' );
    update_post_meta( $offer_id, 'rejection_reason', $reason );
    $conversation_id = $this->get_or_create_conversation($importer_id, $exporter_id, $request_id);
    $this->_add_system_message($conversation_id, $importer_id, "Offer has been rejected. Reason: " . $reason);
    return new WP_REST_Response( [ 'status' => 'success', 'conversation_id' => $conversation_id ], 200 );
}
    
private function get_or_create_conversation($user1_id, $user2_id, $request_id = null) {
    $p1 = min($user1_id, $user2_id);
    $p2 = max($user1_id, $user2_id);

    $meta_query = array(
        'relation' => 'AND',
        array('key' => '_participant_1', 'value' => $p1, 'compare' => '='),
        array('key' => '_participant_2', 'value' => $p2, 'compare' => '=')
    );

    $conversations = get_posts(array(
        'post_type' => 'gt_conversation',
        'post_status' => 'any',
        'posts_per_page' => 1,
        'meta_query' => $meta_query
    ));

    if (!empty($conversations)) {
        return $conversations[0]->ID;
    }

    $user1_data = get_userdata($user1_id);
    $user2_data = get_userdata($user2_id);
    $conversation_title = 'Conversation between ' . $user1_data->display_name . ' and ' . $user2_data->display_name;
    
    $conversation_id = wp_insert_post(array(
        'post_title'  => $conversation_title,
        'post_type'   => 'gt_conversation',
        'post_status' => 'private',
    ));

    if (!is_wp_error($conversation_id)) {
        update_post_meta($conversation_id, '_participant_1', $p1);
        update_post_meta($conversation_id, '_participant_2', $p2);
        if ($request_id) {
            update_post_meta($conversation_id, '_related_request_id', $request_id);
        }
        update_post_meta($conversation_id, '_messages', array());
    }

    return $conversation_id;
}

    public function can_user_view_conversation( $request ) {
    $conversation_id = (int) $request['id'];
    $user_id = get_current_user_id();
    $participant1 = get_post_meta($conversation_id, '_participant_1', true);
    $participant2 = get_post_meta($conversation_id, '_participant_2', true);
    return ($user_id == $participant1 || $user_id == $participant2);
}

public function get_user_conversations( $request ) {
    $user_id = get_current_user_id();
    $query_args = array(
        'post_type' => 'gt_conversation', 'post_status' => 'private', 'posts_per_page' => -1,
        'meta_query' => array('relation' => 'OR',
            array('key' => '_participant_1', 'value' => $user_id),
            array('key' => '_participant_2', 'value' => $user_id)
        )
    );
    $conversations_query = new WP_Query($query_args);
    $conversations = array();
    while ($conversations_query->have_posts()) {
        $conversations_query->the_post();
        $conversation_id = get_the_ID();
        $p1_id = get_post_meta($conversation_id, '_participant_1', true);
        $p2_id = get_post_meta($conversation_id, '_participant_2', true);
        $other_user_id = ($user_id == $p1_id) ? $p2_id : $p1_id;
        $other_user_data = get_userdata($other_user_id);
        $messages = get_post_meta($conversation_id, '_messages', true) ?: array();
        $last_message = !empty($messages) ? end($messages) : null;

        $conversations[] = array(
            'id' => $conversation_id,
            'title' => get_the_title(),
            'request_id' => get_post_meta($conversation_id, '_related_request_id', true),
            'last_message' => $last_message ? $last_message['content'] : 'No messages yet.',
            'last_message_date' => $last_message ? date('m/d/Y', $last_message['timestamp']) : get_the_date('m/d/Y'),
            'other_user' => array(
                'id' => $other_user_id,
                'company_name' => get_user_meta($other_user_id, 'company_name', true) ?: $other_user_data->display_name,
                'type' => (in_array('exporter', $other_user_data->roles)) ? 'Exporter' : 'Importer',
                'avatar_url' => get_user_meta($other_user_id, 'profile_logo_url', true)
            )
        );
    }
    wp_reset_postdata();
    return new WP_REST_Response($conversations, 200);
}

public function get_conversation_messages( $request ) {
    $conversation_id = (int) $request['id'];
    $messages = get_post_meta($conversation_id, '_messages', true);
    return new WP_REST_Response($messages ?: array(), 200);
}

public function add_conversation_message( $request ) {
    $conversation_id = (int) $request['id'];
    $params = $request->get_json_params();
    
    $message_content = isset($params['content']) ? sanitize_textarea_field($params['content']) : '';
    $message_type = isset($params['type']) ? sanitize_text_field($params['type']) : 'text';
    
    $new_message = array(
        'sender_id' => get_current_user_id(),
        'content'   => $message_content,
        'type'      => $message_type,
        'timestamp' => time(),
        'meta'      => isset($params['meta']) ? $params['meta'] : [],
    );

    $this->_add_system_message($conversation_id, $new_message);

    return new WP_REST_Response(array('status' => 'success', 'message' => 'Message sent.'), 201);
}
private function _add_system_message($conversation_id, $sender_id, $content) {
    if (!$conversation_id) return;
    $messages = get_post_meta($conversation_id, '_messages', true) ?: array();
    $new_message = [
        'sender_id' => $sender_id,
        'content' => $content,
        'timestamp' => current_time('timestamp'),
    ];
    $messages[] = $new_message;
    update_post_meta($conversation_id, '_messages', $messages);
}
public function get_agreements($request) {
    $user_id = get_current_user_id();
    $agreements = array();

    $accepted_offers_query = new WP_Query([
        'post_type' => 'gt_offer',
        'posts_per_page' => -1,
        'meta_query' => [
            [
                'key' => 'status',
                'value' => 'accepted',
                'compare' => '='
            ]
        ]
    ]);

    while ($accepted_offers_query->have_posts()) {
        $accepted_offers_query->the_post();
        $offer_id = get_the_ID();
        $request_id = get_post_meta($offer_id, 'request_id', true);
        $request_post = get_post($request_id);
        if (!$request_post) continue;

        $importer_id = $request_post->post_author;
        $exporter_id = get_post_field('post_author', $offer_id);

        if ($user_id == $importer_id || $user_id == $exporter_id) {
            $importer = get_userdata($importer_id);
            $exporter = get_userdata($exporter_id);

            $agreements[] = [
                'offer_id' => $offer_id,
                'request_id' => $request_id,
                'document_id' => 'GLOBTRADE-AGR-' . $offer_id,
                'issue_date' => get_the_modified_date('m/d/Y', $offer_id),
                'importer' => [
                    'company_name' => get_user_meta($importer_id, 'company_name', true) ?: $importer->display_name,
                    'email' => $importer->user_email,
                    'country' => get_user_meta($importer_id, 'country', true) ?: 'N/A',
                    'reg_no' => get_user_meta($importer_id, 'commercial_registration_no', true) ?: 'N/A'
                ],
                'exporter' => [
                    'company_name' => get_user_meta($exporter_id, 'company_name', true) ?: $exporter->display_name,
                    'email' => $exporter->user_email,
                    'country' => get_user_meta($exporter_id, 'country', true) ?: 'N/A',
                    'reg_no' => get_user_meta($exporter_id, 'commercial_registration_no', true) ?: 'N/A'
                ],
                'request_details' => [
                    'product_name' => $request_post->post_title,
                    'description' => $request_post->post_content,
                    'quantity' => get_post_meta($request_id, 'request-quantity-value', true) . ' ' . get_post_meta($request_id, 'request-quantity-unit', true),
                    'specifications' => get_post_meta($request_id, 'request-specs', true) ?: 'N/A'
                ],
                'offer_details' => [
                    'price' => get_post_meta($offer_id, 'offer-price', true) . ' ' . get_post_meta($offer_id, 'offer-currency', true),
                    'quantity' => get_post_meta($offer_id, 'offer-quantity-value', true) . ' ' . get_post_meta($offer_id, 'offer-quantity-unit', true),
                    'specifications' => get_post_meta($offer_id, 'offer-specs', true) ?: 'N/A',
                    'payment_method' => get_post_meta($offer_id, 'offer-payment', true) ?: 'As Agreed',
                    'shipping_method' => get_post_meta($offer_id, 'offer-shipping', true) ?: 'As Agreed',
                    'port_destination' => get_post_meta($request_id, 'request-port', true) ?: 'N/A'
                ]
            ];
        }
    }
    wp_reset_postdata();
    return new WP_REST_Response($agreements, 200);
}
public function get_my_subscription_data( $request ) {
    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        return new WP_Error( 'rest_not_logged_in', 'You are not logged in.', array( 'status' => 401 ) );
    }
    $response_data = array(
        'package'      => get_user_meta( $user_id, 'package', true ) ?: '',
        'credits'      => get_user_meta( $user_id, 'credits', true ) ?: '0',
        'end_date'     => get_user_meta( $user_id, 'subscription_end_date', true ) ? date( 'F j, Y', strtotime(get_user_meta( $user_id, 'subscription_end_date', true )) ) : 'N/A',
        'pending_package_id' => get_user_meta( $user_id, '_pending_package_id', true ) ?: '',
        'my_account_url' => wc_get_account_endpoint_url( 'subscriptions' )
    );
    return new WP_REST_Response( $response_data, 200 );
}

public function check_user_subscription_permission() {
    $user_id = get_current_user_id();
    if ( !$user_id ) {
        return false;
    }

    $user = get_userdata( $user_id );
    $roles = (array) $user->roles;

    if ( in_array( 'importer', $roles ) ) {
        $register_date = new DateTime( $user->user_registered );
        $current_date = new DateTime();
        $interval = $current_date->diff( $register_date );
        if ( $interval->y < 1 ) {
            return true;
        }
    }
    
    $package = get_user_meta( $user_id, 'package', true );
    $end_date_str = get_user_meta( $user_id, 'subscription_end_date', true );

    if ( !empty($package) && !empty($end_date_str) ) {
        $end_date = new DateTime( $end_date_str );
        $current_date = new DateTime();
        if ( $end_date > $current_date ) {
            return true;
        }
    }

    return new WP_Error( 'no_subscription', 'You need an active subscription. Please visit the subscription page to activate your plan.', array( 'status' => 403 ) );
}

public function get_available_packages( $request ) {
    $user = wp_get_current_user();
    $is_exporter = in_array( 'exporter', (array) $user->roles );

    $category_slug = $is_exporter ? 'exporter-packages' : 'importer-packages';

    $products_query = new WP_Query( array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $category_slug,
            ),
        ),
    ) );

    $packages = array();
    while ( $products_query->have_posts() ) {
        $products_query->the_post();
        $product = wc_get_product( get_the_ID() );
        
        if ( $product && $product->is_type( 'simple' ) ) {
            $packages[] = array(
                'id'          => $product->get_id(),
                'name'        => $product->get_name(),
                'price_html'  => $product->get_price_html(),
                'description' => $product->get_short_description(),
                'add_to_cart_url' => wc_get_checkout_url() . '?add-to-cart=' . $product->get_id(),
            );
        }
    }
    wp_reset_postdata();

    return new WP_REST_Response( $packages, 200 );
}

public function get_single_offer( $request ) {
    $post_id = $request['id'];
    $post = get_post( $post_id );
    if ( ! $post || 'gt_offer' !== $post->post_type ) {
        return new WP_Error( 'not_found', 'Offer not found.', array( 'status' => 404 ) );
    }
    
    $request_id = get_post_meta($post_id, 'request_id', true);
    $original_request = get_post($request_id);

    $response_data = array(
        'id'      => $post->ID,
        'title'   => $post->post_title,
        'meta'    => get_post_meta( $post_id ),
        'request_title' => $original_request ? $original_request->post_title : 'N/A'
    );
    return new WP_REST_Response( $response_data, 200 );
}
public function initiate_package_payment( $request ) {
    $params = $request->get_json_params();
    $product_id = absint( $params['product_id'] );
    if ( $product_id && function_exists('WC') ) {
        WC()->cart->empty_cart();
        WC()->cart->add_to_cart( $product_id );
        return new WP_REST_Response( [ 'checkout_url' => wc_get_checkout_url() ], 200 );
    }
    return new WP_Error( 'payment_initiation_failed', 'Could not initiate payment.', array( 'status' => 400 ) );
}
public function can_user_access_community() {
    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        return false;
    }

    $user = get_userdata( $user_id );
    $roles = (array) $user->roles;

    if ( in_array( 'importer', $roles ) ) {
        return true;
    }

    if ( in_array( 'exporter', $roles ) ) {
        $package = get_user_meta( $user_id, 'package', true );
        if ( $package === 'exporter-package-3' ) {
            return true;
        }
    }

    return false;
}
public function get_community_users() {
    $current_user_id = get_current_user_id();
    
    $importers = get_users( array( 'role' => 'importer', 'exclude' => array( $current_user_id ) ) );

    $exporters_p3 = get_users( array(
        'role' => 'exporter',
        'exclude' => array( $current_user_id ),
        'meta_query' => array(
            array(
                'key' => 'package',
                'value' => 'exporter-package-3',
                'compare' => '='
            )
        )
    ) );

    $community_users_raw = array_merge( $importers, $exporters_p3 );
    
    $community_users = array();
    foreach ($community_users_raw as $user) {
        $community_users[] = array(
            'id' => $user->ID,
            'company' => get_user_meta( $user->ID, 'company_name', true ) ?: $user->display_name,
            'email' => $user->user_email,
            'type' => $user->roles[0],
            'country' => get_user_meta( $user->ID, 'country', true ),
            'countryCode' => get_user_meta( $user->ID, 'country_code', true ),
            'logoUrl' => get_user_meta( $user->ID, 'profile_logo_url', true )
        );
    }

    return new WP_REST_Response( $community_users, 200 );
}
public function get_single_user($request) {
    $user_id = (int) $request['id'];
    $user_data = get_userdata($user_id);

    if (!$user_data) {
        return new WP_Error('not_found', 'User not found.', array('status' => 404));
    }

    $response_data = array(
        'id' => $user_id,
        'company' => get_user_meta($user_id, 'company_name', true) ?: $user_data->display_name,
        'type' => $user_data->roles[0],
        'country' => get_user_meta($user_id, 'country', true),
        'countryCode' => get_user_meta($user_id, 'country_code', true),
        'category' => get_user_meta($user_id, 'business_category', true),
        'description' => get_user_meta($user_id, 'company_description', true) ?: 'No description available.',
        'socials' => get_user_meta($user_id, 'socials', true) ?: []
    );

    return new WP_REST_Response($response_data, 200);
}
public function initiate_conversation( $request ) {
    $params = $request->get_json_params();
    $other_user_id = isset( $params['user_id'] ) ? absint( $params['user_id'] ) : 0;
    $current_user_id = get_current_user_id();

    if ( empty( $other_user_id ) || $other_user_id === $current_user_id ) {
        return new WP_Error( 'bad_request', 'Invalid user ID provided.', array( 'status' => 400 ) );
    }

    $conversation_id = $this->get_or_create_conversation( $current_user_id, $other_user_id );

    if ( is_wp_error( $conversation_id ) ) {
        return $conversation_id;
    }

    $other_user_data = get_userdata($other_user_id);

    return new WP_REST_Response( array(
        'status' => 'success',
        'conversation_id' => $conversation_id,
        'other_user' => array(
            'id' => $other_user_id,
            'company_name' => get_user_meta($other_user_id, 'company_name', true) ?: $other_user_data->display_name,
            'type' => $other_user_data->roles[0] ?? '',
            'avatar_url' => get_user_meta($other_user_id, 'profile_logo_url', true)
        )
    ), 200 );
}
public function is_p3_exporter() {
    $user_id = get_current_user_id();
    if ( ! $user_id ) return false;
    if ( ! user_can($user_id, 'exporter') ) return false;
    
    $package = get_user_meta( $user_id, 'package', true );
    return $package === 'exporter-package-3';
}

public function create_marketing_offer($request) {
    $params = $request->get_json_params();
    $user_id = get_current_user_id();

    $post_id = wp_insert_post([
        'post_title' => sanitize_text_field($params['product']),
        'post_content' => sanitize_textarea_field($params['description']),
        'post_author' => $user_id,
        'post_type' => 'gt_marketing_offer',
        'post_status' => 'publish',
    ]);

    if (is_wp_error($post_id)) {
        return $post_id;
    }

    update_post_meta($post_id, 'quantity', sanitize_text_field($params['quantity']));
    update_post_meta($post_id, 'target_importer_ids', $params['target_importer_ids']);
    update_post_meta($post_id, 'status', 'active');

    return new WP_REST_Response(['status' => 'success', 'offer_id' => $post_id], 201);
}

public function get_marketing_offers($request) {
    $user_id = get_current_user_id();
    $user = get_userdata($user_id);
    $offers = [];

    if (in_array('importer', (array)$user->roles)) {
        $query_args = [
            'post_type' => 'gt_marketing_offer',
            'posts_per_page' => -1,
            'meta_query' => [
                'relation' => 'AND',
                ['key' => 'target_importer_ids', 'value' => '"' . $user_id . '"', 'compare' => 'LIKE'],
                ['key' => 'status', 'value' => 'active', 'compare' => '=']
            ]
        ];
    } elseif ($this->is_p3_exporter()) {
         $query_args = [
            'post_type' => 'gt_marketing_offer',
            'posts_per_page' => -1,
            'author' => $user_id,
        ];
    } else {
        return new WP_REST_Response([], 200);
    }

    $query = new WP_Query($query_args);
    while ($query->have_posts()) {
        $query->the_post();
        $offer_id = get_the_ID();
        $author_id = get_the_author_meta('ID');
        $author_data = get_userdata($author_id);
        $offers[] = [
            'id' => $offer_id,
            'product' => get_the_title(),
            'description' => get_the_content(),
            'quantity' => get_post_meta($offer_id, 'quantity', true),
            'status' => get_post_meta($offer_id, 'status', true),
            'exporter' => [
                'id' => $author_id,
                'company_name' => get_user_meta($author_id, 'company_name', true) ?: $author_data->display_name,
            ],
            'accepted_by' => get_post_meta($offer_id, 'accepted_by_importer_id', true),
            'rejected_by' => get_post_meta($offer_id, 'rejected_by_importer_ids', true) ?: [],
        ];
    }
    wp_reset_postdata();

    return new WP_REST_Response($offers, 200);
}

public function handle_marketing_offer_action($request) {
    $offer_id = (int) $request['id'];
    $importer_id = get_current_user_id();
    $params = $request->get_json_params();
    $action = $params['action']; // 'accept' or 'reject'

    $target_ids = get_post_meta($offer_id, 'target_importer_ids', true);
    if (!is_array($target_ids) || !in_array($importer_id, $target_ids)) {
        return new WP_Error('forbidden', 'You are not a target of this offer.', ['status' => 403]);
    }

    if ($action === 'accept') {
        if (get_post_meta($offer_id, 'status', true) !== 'active') {
             return new WP_Error('conflict', 'This offer is no longer active.', ['status' => 409]);
        }
        update_post_meta($offer_id, 'status', 'completed');
        update_post_meta($offer_id, 'accepted_by_importer_id', $importer_id);
        
        $exporter_id = get_post_field('post_author', $offer_id);
        $this->get_or_create_conversation($importer_id, $exporter_id);
        
    } elseif ($action === 'reject') {
        $rejected_ids = get_post_meta($offer_id, 'rejected_by_importer_ids', true) ?: [];
        if (!in_array($importer_id, $rejected_ids)) {
            $rejected_ids[] = $importer_id;
            update_post_meta($offer_id, 'rejected_by_importer_ids', $rejected_ids);
        }
    } else {
        return new WP_Error('bad_request', 'Invalid action.', ['status' => 400]);
    }
    
    return new WP_REST_Response(['status' => 'success'], 200);
}
public function update_current_user_data( $request ) {
    $user_id = get_current_user_id();
    $params = $request->get_json_params();

    foreach ( $params as $key => $value ) {
        if ( $key === 'socials' && is_array($value) ) {
            $sanitized_socials = array();
            foreach ($value as $social_link) {
                if (isset($social_link['type']) && !empty($social_link['url'])) {
                    $sanitized_socials[] = array(
                        'type' => sanitize_text_field($social_link['type']),
                        'url' => esc_url_raw($social_link['url']),
                    );
                }
            }
            update_user_meta( $user_id, 'socials', $sanitized_socials );
        } else {
            if ($key !== 'socials') {
                update_user_meta( $user_id, sanitize_key( $key ), sanitize_text_field( $value ) );
            }
        }
    }
    return new WP_REST_Response( array( 'status' => 'success', 'message' => 'Profile updated.' ), 200 );
}
public function handle_verification_upload($request) {
    if (!function_exists('wp_handle_upload')) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
    }

    $user_id = get_current_user_id();
    $files = $request->get_file_params();
    $verification_docs = get_user_meta($user_id, 'verification_docs', true);
    if (!is_array($verification_docs)) {
        $verification_docs = [];
    }

    $supported_types = ['image/jpeg', 'image/png', 'application/pdf'];

    foreach ($files as $key => $file) {
        if (isset($file['error']) && $file['error'] === UPLOAD_ERR_OK) {
            if (!in_array($file['type'], $supported_types)) {
                continue;
            }
            
            $upload_overrides = ['test_form' => false];
            $movefile = wp_handle_upload($file, $upload_overrides);

            if ($movefile && !isset($movefile['error'])) {
                $verification_docs[$key] = [
                    'url' => $movefile['url'],
                    'file' => $movefile['file'],
                    'type' => $movefile['type'],
                    'status' => 'Pending',
                    'name' => basename($file['name'])
                ];
            }
        }
    }

    update_user_meta($user_id, 'verification_docs', $verification_docs);
    return new WP_REST_Response(['status' => 'success', 'docs' => $verification_docs], 200);
}
public function get_dashboard_stats() {
    $user_id = get_current_user_id();
    $user = get_userdata($user_id);
    $stats = [
        'my_submitted_offers' => 0,
        'my_requests' => 0,
        'new_messages' => 0,
        'registered_users' => count_users()['total_users'],
    ];

    if (in_array('exporter', (array)$user->roles)) {
        $offer_query = new WP_Query(['post_type' => 'gt_offer', 'author' => $user_id, 'posts_per_page' => -1]);
        $stats['my_submitted_offers'] = $offer_query->found_posts;
    }

    if (in_array('importer', (array)$user->roles)) {
        $request_query = new WP_Query(['post_type' => 'gt_request', 'author' => $user_id, 'posts_per_page' => -1]);
        $stats['my_requests'] = $request_query->found_posts;
    }
    
    return new WP_REST_Response($stats, 200);
}
public function handle_chat_file_upload($request) {
    if (!function_exists('wp_handle_upload')) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
    }

    $conversation_id = isset($_POST['conversation_id']) ? intval($_POST['conversation_id']) : 0;
    if (!$conversation_id || !$this->can_user_view_conversation(['id' => $conversation_id])) {
        return new WP_Error('forbidden', 'You do not have permission to access this conversation.', ['status' => 403]);
    }

    $file = $_FILES['chat_file'];
    $upload_overrides = ['test_form' => false];
    $movefile = wp_handle_upload($file, $upload_overrides);

    if ($movefile && !isset($movefile['error'])) {
        $new_message = [
            'sender_id' => get_current_user_id(),
            'content'   => $file['name'],
            'type'      => 'file',
            'timestamp' => time(),
            'meta'      => [
                'url' => $movefile['url'],
                'type' => $movefile['type'],
                'file' => $movefile['file']
            ]
        ];
        $this->_add_system_message($conversation_id, $new_message);
        return new WP_REST_Response(['status' => 'success', 'file' => $new_message], 200);
    }
    
    return new WP_Error('upload_failed', $movefile['error'] ?? 'Upload failed.', ['status' => 500]);
}
}