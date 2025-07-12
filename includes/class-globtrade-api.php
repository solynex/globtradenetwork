<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Globtrade_API {
     
    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    public function register_routes() {
        register_rest_route( 'globtrade/v1', '/public/requests', array(
            array(
                'methods'  => 'GET',
                'callback' => array( $this, 'get_public_requests' ),
                'permission_callback' => '__return_true',
            ),
        ) );
        
        register_rest_route( 'globtrade/v1', '/users/me/logo-upload', array(
            'methods'  => 'POST',
            'callback' => array( $this, 'handle_profile_logo_upload' ),
            'permission_callback' => array( $this, 'is_user_logged_in' ),
        ) );

        register_rest_route( 'globtrade/v1', '/register', array(
            'methods'  => 'POST',
            'callback' => array( $this, 'handle_registration' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( 'globtrade/v1', '/login', array(
            'methods'  => 'POST',
            'callback' => array( $this, 'handle_login' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( 'globtrade/v1', '/users/me', array(
            array(
                'methods'  => 'GET',
                'callback' => array( $this, 'get_current_user_data' ),
                'permission_callback' => array( $this, 'is_user_logged_in' ),
            ),
            array(
                'methods'  => 'POST',
                'callback' => array( $this, 'update_current_user_data' ),
                'permission_callback' => array( $this, 'is_user_logged_in' ),
            ),
        ) );

        register_rest_route( 'globtrade/v1', '/requests', array(
            array(
                'methods'  => 'POST',
                'callback' => array( $this, 'create_request' ),
                'permission_callback' => array( $this, 'is_user_importer' ),
            ),
            array(
                'methods'  => 'GET',
                'callback' => array( $this, 'get_all_requests' ),
                'permission_callback' => array( $this, 'is_user_exporter' ),
            ),
        ) );

        register_rest_route( 'globtrade/v1', '/requests/me', array(
            array(
                'methods'  => 'GET',
                'callback' => array( $this, 'get_my_requests' ),
                'permission_callback' => array( $this, 'is_user_importer' ),
            ),
        ) );

        register_rest_route( 'globtrade/v1', '/requests/(?P<id>\d+)', array(
            array(
                'methods'  => 'GET',
                'callback' => array( $this, 'get_single_request' ),
                'permission_callback' => array( $this, 'can_user_view_request' ),
            ),
            array(
                'methods'  => 'PUT',
                'callback' => array( $this, 'update_request' ),
                'permission_callback' => array( $this, 'can_user_manage_request' ),
            ),
            array(
                'methods'  => 'DELETE',
                'callback' => array( $this, 'delete_request' ),
                'permission_callback' => array( $this, 'can_user_manage_request' ),
            ),
        ) );

        register_rest_route( 'globtrade/v1', '/offers', array(
            array(
                'methods'  => 'POST',
                'callback' => array( $this, 'create_offer' ),
                'permission_callback' => array( $this, 'is_user_exporter' ),
            ),
        ) );

        register_rest_route( 'globtrade/v1', '/offers/me', array(
            array(
                'methods'  => 'GET',
                'callback' => array( $this, 'get_my_offers' ),
                'permission_callback' => array( $this, 'is_user_exporter' ),
            ),
        ) );

        register_rest_route( 'globtrade/v1', '/offers/(?P<id>\d+)', array(
            array(
                'methods'  => 'PUT',
                'callback' => array( $this, 'update_offer' ),
                'permission_callback' => array( $this, 'can_user_manage_offer' ),
            ),
            array(
                'methods'  => 'DELETE',
                'callback' => array( $this, 'delete_offer' ),
                'permission_callback' => array( $this, 'can_user_manage_offer' ),
            ),
            array(
                'methods'  => 'DELETE',
                'callback' => array( $this, 'delete_offer' ),
                'permission_callback' => array( $this, 'can_user_manage_offer' ),
            ),
        ) );
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
        $password = $params['password'];
        $role = sanitize_text_field( $params['type'] );
        $company_name = sanitize_text_field( $params['company'] );

        if ( email_exists( $email ) ) {
            return new WP_Error( 'email_exists', 'This email is already registered.', array( 'status' => 400 ) );
        }

        $user_data = array(
            'user_login'    => $email,
            'user_email'    => $email,
            'user_pass'     => $password,
            'display_name'  => $company_name,
            'role'          => $role,
        );
        $user_id = wp_insert_user( $user_data );

        if ( is_wp_error( $user_id ) ) {
            return $user_id;
        }

        update_user_meta( $user_id, 'company_name', $company_name );
        update_user_meta( $user_id, 'country', sanitize_text_field( $params['country'] ) );
        update_user_meta( $user_id, 'country_code', sanitize_text_field( $params['country_code'] ) );
        update_user_meta( $user_id, 'package', sanitize_text_field( $params['package'] ) );
        wp_set_auth_cookie( $user_id );

        return new WP_REST_Response( array( 'status' => 'success', 'message' => 'User registered successfully.' ), 200 );
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

    $response_data = array(
        'id'                          => $user_id,
        'email'                       => $user_data->user_email,
        'company_name'                => $user_meta['company_name'][0] ?? '',
        'role'                        => $user_data->roles[0] ?? '',
        'phone'                       => $user_meta['phone'][0] ?? '',
        'country'                     => $user_meta['country'][0] ?? '',
        'country_code'                => $user_meta['country_code'][0] ?? '',
        'business_category'           => $user_meta['business_category'][0] ?? '',
        'commercial_registration_no'  => $user_meta['commercial_registration_no'][0] ?? '',
        'website'                     => $user_meta['website'][0] ?? '',
        'address'                     => $user_meta['address'][0] ?? '',
        'company_description'         => $user_meta['company_description'][0] ?? '',
        'package'                     => $user_meta['package'][0] ?? '',
        'profile_logo_url'            => get_user_meta( $user_id, 'profile_logo_url', true ) ?? '', // إضافة رابط اللوجو
    );
    return new WP_REST_Response( $response_data, 200 );
}

    public function update_current_user_data( $request ) {
        $user_id = get_current_user_id();
        foreach ( $request->get_params() as $key => $value ) {
            update_user_meta( $user_id, sanitize_key( $key ), sanitize_text_field( $value ) );
        }
        return new WP_REST_Response( array( 'status' => 'success', 'message' => 'Profile updated.' ), 200 );
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
        $query = new WP_Query( array('post_type' => 'gt_request', 'author' => get_current_user_id(), 'posts_per_page' => -1) );
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

    public function get_all_requests() {
        $requests = array();
        $query = new WP_Query( array('post_type' => 'gt_request', 'post_status' => 'publish', 'posts_per_page' => -1) );
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

    public function get_single_offer( $request ) {
        $post_id = $request['id'];
        $post = get_post( $post_id );

        $response_data = array(
            'id'      => $post->ID,
            'title'   => $post->post_title,
            'content' => $post->post_content,
            'status'  => $post->post_status,
            'meta'    => get_post_meta( $post_id )
        );
        return new WP_REST_Response( $response_data, 200 );
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

    // فحص نوع الملف (يمكنك إضافة المزيد من الأنواع المسموح بها)
    $allowed_types = array( 'image/jpeg', 'image/png', 'image/gif' );
    if ( ! in_array( $uploaded_file['type'], $allowed_types ) ) {
        return new WP_Error( 'invalid_file_type', 'Invalid file type. Only JPG, PNG, and GIF images are allowed.', array( 'status' => 400 ) );
    }

    // تهيئة مجلد الرفع
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

        // إنشاء البيانات الوصفية للملف المرفق
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        $attachment_data = wp_generate_attachment_metadata( $attachment_id, $movefile['file'] );
        wp_update_attachment_metadata( $attachment_id, $attachment_data );

        // حفظ رابط الصورة في بيانات المستخدم (user meta)
        update_user_meta( $user_id, 'profile_logo_url', $movefile['url'] );

        // مسح الكاش (إذا كنت تستخدمه)
        if ( class_exists( 'LiteSpeed\Purge' ) ) {
            $dashboard_url = home_url('/dashboard');
            do_action( 'litespeed_purge_url', $dashboard_url );
        } elseif ( function_exists( 'litespeed_purge_all' ) ) {
            litespeed_purge_all();
        }

        return new WP_REST_Response( array( 'status' => 'success', 'message' => 'Logo uploaded successfully.', 'logo_url' => $movefile['url'] ), 200 );
    } else {
        return new WP_Error( 'upload_failed', $movefile['error'], array( 'status' => 500 ) );
    }
}
}