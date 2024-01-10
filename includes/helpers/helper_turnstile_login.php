<?php

/**
 * Create a helper function to add add captcha in login form.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Cloudflare turnstile key
function cf_turnstile_key() {
    $site_key= 'xxxxxxxxxxxxxxxxxxxxxxxxx';
    $secret_key= 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
    return [$site_key, $secret_key]; 
}

// Enqueue Cloudflare turnstile script.
function gridlove_child_login_assets() {
    wp_enqueue_script( 'gridlove-turnstile', 'https://challenges.cloudflare.com/turnstile/v0/api.js', array(), null, array('strategy' => 'defer', 'in_footer' => true) );

    $inline_styles = 'div#login { width: 350px; } #loginform .cf-turnstile { margin-bottom: 15px; }';
    wp_add_inline_style( 'login', $inline_styles );
}

// Append the turnstile widget into the login form.
if ( !function_exists( 'helper_append_turnstile_widget' ) ) {
    function helper_append_turnstile_widget() {
        echo '<div class="cf-turnstile" data-sitekey="' . esc_attr( cf_turnstile_key()[0] ) . '"></div>';
    }
    add_action( 'login_form', 'helper_append_turnstile_widget' );
}

// Cloudflare turnstile verification.
if ( !function_exists( 'helper_authenticate_login' ) ) {
    function helper_authenticate_login( $user, $password ) {
        $captcha_token = isset($_POST['cf-turnstile-response']) ? sanitize_text_field($_POST['cf-turnstile-response']) : '';

        if ( !$captcha_token ) {
            return new WP_Error( 'captcha_invalid', __('Captcha Invalid! Please check the captcha.', 'gridlove-child') );
        }

        $secret_key = sanitize_text_field( cf_turnstile_key()[1] );
        $user_ip = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        $api_endpoint = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

        $data = array( 'secret' => $secret_key, 'response' => $captcha_token, 'remoteip' => $user_ip );
        $args = array(
            'body' => $data,
            'headers' => array( 'Content-Type' => 'application/x-www-form-urlencoded' ),
            'user-agent' => 'WordPress/' . get_bloginfo('version'),
        );

        $response = wp_safe_remote_post( $api_endpoint, $args );

        if ( is_wp_error($response) ) {
            return new WP_Error( 'captcha_verification_failed', __('Captcha verification failed.', 'gridlove-child') );
        }

        $response_body = wp_remote_retrieve_body( $response );
        $response_data = json_decode( $response_body, true );

        if ( $response_data['success'] !== true ) {
            return new WP_Error('captcha_invalid', __('Captcha Invalid! Please retry again.', 'gridlove-child'));
        } else {
            return $user;
        }
    }
    add_action( 'wp_authenticate_user', 'helper_authenticate_login', 10, 2 );
}
