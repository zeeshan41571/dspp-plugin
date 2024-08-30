<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
add_action('wp_ajax_dspp_register_user', 'dspp_register_user');
add_action('wp_ajax_nopriv_dspp_register_user', 'dspp_register_user');

function dspp_register_user() {
    if (isset($_POST['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'register_user')) {
        $username = sanitize_text_field($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = sanitize_text_field($_POST['password']);
        $user_id = wp_create_user($username, $password, $email);
        if (is_wp_error($user_id)) {
            echo wp_json_encode(array('status' => 'error', 'message' => $user_id->get_error_message()));
        } else {
            $user = get_user_by('ID', $user_id);
            $user_login = $user->user_login;
            $user_data = array(
                'user_login' => $user_login,
                'user_password' => $password, // Note: Use the user's actual password here
                'remember' => true,
            );

            $user_signon = wp_signon($user_data, false);

            if (is_wp_error($user_signon)) {
                // Handle sign-in error
                echo wp_json_encode(array('status' => 'error', 'message' => 'Error signing in after registration.'));
            } else {
                // Sign in successful, redirect to the profile page
                $profile_page_url = home_url('/bits-dashboard'); // Adjust the URL to your actual profile page
                echo wp_json_encode(array('status' => 'success', 'redirect' => $profile_page_url));
            }
        }
    } else {
        wp_send_json_error('Nonce Verification Failed!');
    }

    wp_die();
}

add_action('wp_ajax_dspp_ajax_user_login', 'dspp_ajax_user_login');
add_action('wp_ajax_nopriv_dspp_ajax_user_login', 'dspp_ajax_user_login');

function dspp_ajax_user_login() {
    if (isset($_POST['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'dspp_ajax_user_login')) {
        $login_data = array(
            'user_login' => sanitize_text_field($_POST['username']),
            'user_password' => sanitize_text_field($_POST['password']),
            'remember' => true,
        );
        $user = wp_signon($login_data, false);
        if (is_wp_error($user)) {
            echo wp_json_encode(array('status' => 'error', 'message' => $user->get_error_message()));
        } else {
            $profile_page_url = home_url('/bits-dashboard'); // Adjust the URL to your actual profile page
            echo wp_json_encode(array('status' => 'success', 'redirect' => $profile_page_url));
        }
    } else {
        wp_send_json_error('Nonce Verification Failed!');
    }
    wp_die();
}

add_action('wp_ajax_dspp_custom_forgot_password', 'dspp_custom_forgot_password_callback');
add_action('wp_ajax_nopriv_dspp_custom_forgot_password', 'dspp_custom_forgot_password_callback');

function dspp_custom_forgot_password_callback() {
    if (isset($_POST['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'custom_forgot_password')) {
        $user_login = sanitize_text_field($_POST['user_login']);
        $user = get_user_by('email', $user_login);
        if (!$user) {
            echo 'User not found.';
            wp_die();
        }
        $reset_link = add_query_arg(array('key' => wp_generate_password(20, false), 'login' => rawurlencode($user_login)), network_site_url('wp-login.php?action=rp', 'login'));
        $subject = 'Password Reset';
        $message = 'Click the following link to reset your password: ' . $reset_link;
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $mail_sent = wp_mail($user->user_email, $subject, $message, $headers);
        if ($mail_sent) {
            echo 'Password reset link sent to your email.';
        } else {
            echo 'Error sending the password reset link.';
        }
    } else {
        wp_send_json_error('Nonce Verification Failed!');
    }
    wp_die();
}

// WordPress AJAX handler
add_action('wp_ajax_dspp_update_user_profile', 'dspp_update_user_profile_callback');
add_action('wp_ajax_nopriv_dspp_update_user_profile', 'dspp_update_user_profile_callback');

function dspp_update_user_profile_callback() {
    if (isset($_POST['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'update_user_profile')) {
        parse_str(sanitize_text_field($_POST['data']), $formData);
        $user_data = array();
        if (isset($formData['email']) && !empty($formData['email'])) {
            $user_data['user_email'] = sanitize_email($formData['email']);
        }
        if (isset($formData['password']) && !empty($formData['password'])) {
            $user_data['user_pass'] = sanitize_text_field($formData['password']);
        }
        if (isset($formData['name_f']) && !empty($formData['name_f'])) {
            $user_data['first_name'] = sanitize_text_field($formData['name_f']);
        }
        if (isset($formData['name_l']) && !empty($formData['name_l'])) {
            $user_data['last_name'] = sanitize_text_field($formData['name_l']);
        }
        if (!empty($user_data)) {
            $user_id = get_current_user_id();
            $updated = wp_update_user(array_merge(['ID' => $user_id], $user_data));
        }
        if ($updated) {
            echo wp_json_encode(array('status' => 'success', 'message' => 'User profile updated successfully!'));
        } else {
            echo wp_json_encode(array('status' => 'error', 'message' => 'Failed to update user profile!'));
        }
    } else {
        wp_send_json_error('Nonce Verification Failed!');
    }
    wp_die();
}

function dspp_disable_admin_bar_for_non_admins() {
    if (!current_user_can('administrator') && !is_admin()) {
        return false;
    }
    return true;
}

add_filter('show_admin_bar', 'dspp_disable_admin_bar_for_non_admins', 999);
