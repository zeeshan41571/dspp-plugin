<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
function dspp_custom_services_post_type() {
    $labels = array(
        'name' => _x('DSP Services', 'post type general name', 'digital-service-provider-crm'),
        'singular_name' => _x('DSP Service', 'post type singular name', 'digital-service-provider-crm'),
        'menu_name' => _x('DSP Services', 'admin menu', 'digital-service-provider-crm'),
        'name_admin_bar' => _x('DSP Service', 'add new on admin bar', 'digital-service-provider-crm'),
        'add_new' => _x('Add New', 'Service', 'digital-service-provider-crm'),
        'add_new_item' => __('Add New Service', 'digital-service-provider-crm'),
        'new_item' => __('New Service', 'digital-service-provider-crm'),
        'edit_item' => __('Edit Service', 'digital-service-provider-crm'),
        'view_item' => __('View Service', 'digital-service-provider-crm'),
        'all_items' => __('All Services', 'digital-service-provider-crm'),
        'search_items' => __('Search Services', 'digital-service-provider-crm'),
        'parent_item_colon' => __('Parent Services:', 'digital-service-provider-crm'),
        'not_found' => __('No Services found.', 'digital-service-provider-crm'),
        'not_found_in_trash' => __('No Services found in Trash.', 'digital-service-provider-crm')
    );
    $args = array(
        'labels' => $labels,
        'description' => __('Description.', 'digital-service-provider-crm'),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'dspp-service'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
        'taxonomies' => array('dspp_service_category'), // Custom taxonomy for your custom post type
    );
    register_post_type('dspp_service', $args);
    // Register custom taxonomy for your custom post type
    $taxonomy_args = array(
        'hierarchical' => true,
        'labels' => array(
            'name' => _x('Service Categories','Services Category Texonomy', 'digital-service-provider-crm'),
            'singular_name' => _x('Service Category', 'Services Category Texonomy','digital-service-provider-crm'),
            'search_items' => __('Search Service Categories', 'digital-service-provider-crm'),
            'all_items' => __('All Service Categories', 'digital-service-provider-crm'),
            'parent_item' => __('Parent Service Category', 'digital-service-provider-crm'),
            'parent_item_colon' => __('Parent Service Category:', 'digital-service-provider-crm'),
            'edit_item' => __('Edit Service Category', 'digital-service-provider-crm'),
            'update_item' => __('Update Service Category', 'digital-service-provider-crm'),
            'add_new_item' => __('Add New Service Category', 'digital-service-provider-crm'),
            'new_item_name' => __('New Service Category Name', 'digital-service-provider-crm'),
            'menu_name' => __('Service Categories', 'digital-service-provider-crm'),
        ),
        'rewrite' => array('slug' => 'dspp-services-category'),
    );

    register_taxonomy('dspp_services_category', 'dspp_service', $taxonomy_args);
}

add_action('init', 'dspp_custom_services_post_type');

// Add custom meta box for the "Price" field
function dspp_add_service_price_meta_box() {
    add_meta_box(
            'dspp_service_price_meta_box',
            'Price',
            'dspp_service_price_meta_box_callback',
            'dspp_service', // Replace with your custom post type
            'normal',
            'high'
    );
}

add_action('add_meta_boxes', 'dspp_add_service_price_meta_box');

// Callback function to display the "Price" meta box
function dspp_service_price_meta_box_callback($post) {
    // Get the current value of the "Price" field
    $price = get_post_meta($post->ID, '_service_price', true);
    wp_nonce_field('save_service_price', 'service_price_nonce');
    // Output the HTML for the "Price" field
    ?>
    <label for="service_price">Price:</label>
    <input type="text" id="service_price" name="service_price" value="<?php echo esc_attr($price); ?>">
    <?php
}

// Save the "Price" field when the post is saved
function dspp_save_service_price_meta_box($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE || !isset($_POST['service_price_nonce'])) {
        return;
    }
    if (!isset( $_POST['service_price_nonce'] ) && !wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['service_price_nonce'])) , 'save_service_price')){
        return;
    }
    if (!current_user_can('edit_post', $post_id))
        return;

    // Save the "Price" field value
    if (isset($_POST['service_price'])) {
        update_post_meta($post_id, '_service_price', sanitize_text_field($_POST['service_price']));
    }
}

add_action('save_post', 'dspp_save_service_price_meta_box');

// Add custom columns to the admin screen
function dspp_custom_service_columns($columns) {
    $columns['services_category'] = 'Category';
    $columns['price'] = 'Price';
    return $columns;
}

add_filter('manage_dspp_service_posts_columns', 'dspp_custom_service_columns');

// Populate custom columns with data
function dspp_custom_service_column_content($column, $post_id) {
    switch ($column) {
        case 'services_category':
            $category = get_the_terms($post_id, 'dspp_services_category'); // Replace with your actual taxonomy name
            echo!empty($category) ? esc_html($category[0]->name) : 'N/A';
            break;

        case 'price':
            $price = get_post_meta($post_id, '_service_price', true);
            echo!empty($price) ? esc_html($price) : 'N/A';
            break;
    }
}

add_action('manage_dspp_service_posts_custom_column', 'dspp_custom_service_column_content', 10, 2);

function dspp_add_to_session() {
    if (isset($_POST['nonce']) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'])) , 'add_to_session_nonce')){
        $response = array();
        $data_to_add = array();
        $site = sanitize_text_field($_POST['site']);
        $user_id_wp = sanitize_key($_POST['user_id']);
        $price = sanitize_key($_POST['price']);
        $post_id = sanitize_key($_POST['post_id']);
        $data_to_add ['site'] = $site;
        $data_to_add ['price'] = $price;
        $data_to_add ['user_id'] = $user_id_wp;
        $data_to_add ['post_id'] = $post_id;
        $custom_website_data = get_user_meta($user_id_wp, 'custom_website_data', true);
        $decoded_array = json_decode($custom_website_data, true);
//    $total_price = (isset($decoded_array['custom_website_data_' . $user_id_wp]['discount_price']) && !empty($decoded_array['custom_website_data_' . $user_id_wp]['discount_price'])) ? $data_to_add['total_price'] + $decoded_array['custom_website_data_' . $user_id_wp]['discount_price'] : $data_to_add['total_price'];
        $data_array = (isset($decoded_array) && !empty($decoded_array)) ? $decoded_array : array();
        if (isset($data_array['custom_website_data_' . $user_id_wp]["$site"])) {
            $current = $data_array['custom_website_data_' . $user_id_wp]["$site"]['quantity'] = 1;
            $data_array['custom_website_data_' . $user_id_wp]["$site"]['quantity'] = $current + 1;
            $encoded_array = wp_json_encode($data_array);
            update_user_meta($user_id_wp, 'custom_website_data', $encoded_array);
            $response = array("status" => true, "message" => "Quantity updated successfully.");
        } else {
            $data_array['custom_website_data_' . $user_id_wp]["$site"] = $data_to_add;
            $data_array['custom_website_data_' . $user_id_wp]["$site"]['quantity'] = 1;
            $encoded_array = wp_json_encode($data_array);
            update_user_meta($user_id_wp, 'custom_website_data', $encoded_array);
            $response = array("status" => true, "message" => "Website added to cart successfully.");
        }
        echo wp_json_encode($response);
    } else {
        wp_send_json_error('Nonce verification failed');
    }
    wp_die();
}

add_action('wp_ajax_dspp_add_to_session', 'dspp_add_to_session'); // For logged-in users
add_action('wp_ajax_nopriv_dspp_add_to_session', 'dspp_add_to_session'); // For non-logged-in users

function dspp_update_quantity_in_session() {
    if (isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'])) , 'update_quantity_in_session')){
        $response = array();
        $data_to_add = array();
        $site = sanitize_text_field($_POST['site']);
        $user_id_wp = sanitize_key($_POST['user_id']);
        $price = sanitize_key($_POST['price']);
        $post_id = sanitize_key($_POST['post_id']);
        $value= sanitize_text_field($_POST['value']);
        $data_to_add ['site'] = $site;
        $data_to_add ['price'] = $price;
        $data_to_add ['user_id'] = $user_id_wp;
        $data_to_add ['post_id'] = $post_id;
        $data_to_add ['value'] = $value;
        $custom_website_data = get_user_meta($user_id_wp, 'custom_website_data', true);
        $decoded_array = json_decode($custom_website_data, true);
        $data_array = (isset($decoded_array) && !empty($decoded_array)) ? $decoded_array : array();
        if (isset($data_array['custom_website_data_' . $user_id_wp]["$site"])) {
            $data_array['custom_website_data_' . $user_id_wp]["$site"]['quantity'] = $data_to_add['value'];
            $encoded_array = wp_json_encode($data_array);
            update_user_meta($user_id_wp, 'custom_website_data', $encoded_array);
            $response = array("status" => true, "message" => "Quantity updated successfully.");
        }
        echo wp_json_encode($response);
    } else {
        wp_send_json_error('Nonce verification failed');
    }
    wp_die();
}

add_action('wp_ajax_dspp_update_quantity_in_session', 'dspp_update_quantity_in_session'); // For logged-in users
add_action('wp_ajax_nopriv_dspp_update_quantity_in_session', 'dspp_update_quantity_in_session'); // For non-logged-in users

function dspp_hasSubarray($array) {
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            return true;
        }
    }
    return false;
}

function dspp_remove_from_session() {
    if (isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'])) , 'remove_from_session')){
        $website_url = sanitize_text_field($_POST['website_url']);
        $user_id_wp = sanitize_key($_POST['user_id']);
        $custom_website_data = get_user_meta($user_id_wp, 'custom_website_data', true);
        $decoded_array = json_decode($custom_website_data, true);
        $data_array = (isset($decoded_array) && !empty($decoded_array)) ? $decoded_array : array();
//    $total_price = (isset($data_array['custom_website_data_' . $user_id_wp]['discount_price']) && !empty($data_array['custom_website_data_' . $user_id_wp]['discount_price'])) ? $_POST['total_price'] + $data_array['custom_website_data_' . $user_id_wp]['discount_price'] : $_POST['total_price'];
        if (isset($data_array['custom_website_data_' . $user_id_wp][$website_url])) {
            unset($data_array['custom_website_data_' . $user_id_wp][$website_url]);
//        $data_array['custom_website_data_' . $user_id_wp]["total_price"] = $total_price;
            $hasSubarray = dspp_hasSubarray($data_array['custom_website_data_' . $user_id_wp]);
            if (!$hasSubarray) {
                unset($data_array['custom_website_data_' . $user_id_wp]['coupon_code']);
                unset($data_array['custom_website_data_' . $user_id_wp]['coupon_value']);
                unset($data_array['custom_website_data_' . $user_id_wp]['discount_price']);
                unset($data_array['custom_website_data_' . $user_id_wp]['coupon_discount_type']);
            }
            $encoded_array = wp_json_encode($data_array);
            update_user_meta($user_id_wp, 'custom_website_data', $encoded_array);
            $response = array("status" => true, "message" => "Item removed from cart successfully.");
        } else {
            $response = array("status" => false, "message" => "Item not found in cart.");
        }
        echo wp_json_encode($response);
    } else {
        wp_send_json_error('Nonce verification failed');
    }
    wp_die();
}

add_action('wp_ajax_dspp_remove_from_session', 'dspp_remove_from_session'); // For logged-in users
add_action('wp_ajax_nopriv_dspp_remove_from_session', 'dspp_remove_from_session'); // For non-logged-in users