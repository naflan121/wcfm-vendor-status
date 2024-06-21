<?php 

/*
Plugin Name: Vendor Status Plugin
Description: Tracks online status of vendors and provides a shortcode to display their status.
Version: 1.0
Author: Mohamed Naflan
Author Email: mnaflan121@gmail.com
Author URI: https://www.facebook.com/M.NAFLAN/
*/


add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_open', 10 ); 
function woocommerce_template_loop_product_link_open(){
    global $product;
    $vendor_id = get_post_field( 'post_author', $product->get_id() );
    if (is_vendor_online($vendor_id)) {
        // Vendor is online
        $status = '<div class="status product-card">';
        $status .= '<div class="status-indicator-container">';
        $status .= '<span class="status-indicator online"></span>';
        $status .= '</div>';
        $status .= '<span class="status-text">Online</span>';
        $status .= '</div>';
    } else {
        // Vendor is offline
        $status = '<div class="status product-card">';
        $status .= '<div class="status-indicator-container">';
        $status .= '<span class="status-indicator offline"></span>';
        $status .= '</div>';
        $status .= '<span class="status-text">Offline</span>';
        $status .= '</div>';
    }
    echo $status;
}

function update_online_vendors_status(){
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $user_roles = $current_user->roles;

        // Check if the user has the 'wcfm_vendor' role
        if (in_array('wcfm_vendor', $user_roles)) {
            if(($logged_in_vendors = get_transient('vendors_online')) === false) $logged_in_vendors = array();

            $current_user_id = $current_user->ID;  
            $current_time = current_time('timestamp');

            if(!isset($logged_in_vendors[$current_user_id]) || ($logged_in_vendors[$current_user_id] < ($current_time - (15 * 60)))){
                $logged_in_vendors[$current_user_id] = $current_time;
                set_transient('vendors_online', $logged_in_vendors, 30 * 60);
            }
        }
    }
}
add_action('wp', 'update_online_vendors_status');


function is_vendor_online($vendor_id) {
    // Get the online vendors list
    $logged_in_vendors = get_transient('vendors_online');

    // Check if the vendor is in the list and had activity within the last 15 minutes
    return isset($logged_in_vendors[$vendor_id]) && ($logged_in_vendors[$vendor_id] > (current_time('timestamp') - (15 * 60)));
}


function vendor_status_shortcode() {
    global $WCFM, $WCFMmp, $wp, $WCFM_Query, $post;
    $status = ''; // Initialize status variable

    $vendor_id = '';
    // Check if shortcode fires on the store page
    if (!$vendor_id && wcfm_is_store_page()) {
        // Get current vendor store URL
        $wcfm_store_url = wcfm_get_option('wcfm_store_url', 'store');
        $store_name = apply_filters('wcfmmp_store_query_var', get_query_var($wcfm_store_url));
        // Get vendor object from store slug
        $vendor = get_user_by('slug', $store_name);
        $vendor_id = $vendor->ID;

        // Check if the vendor is online
        if (is_vendor_online($vendor_id)) {
            // Vendor is online
            $status = '<div class="status">';
            $status .= '<div class="status-indicator-container">';
            $status .= '<span class="status-indicator online"></span>';
            $status .= '</div>';
            $status .= '<span class="status-text">Online</span>';
            $status .= '</div>';
        } else {
            // Vendor is offline
            $status = '<div class="status">';
            $status .= '<div class="status-indicator-container">';
            $status .= '<span class="status-indicator offline"></span>';
            $status .= '</div>';
            $status .= '<span class="status-text">Offline</span>';
            $status .= '</div>';
        }
    }

    return $status;
}

add_shortcode('vendor_status', 'vendor_status_shortcode');



function vendor_status_enqueue_styles() {
    wp_enqueue_style('vendor-status-style', plugin_dir_url(__FILE__) . 'style.css');
}

add_action('wp_enqueue_scripts', 'vendor_status_enqueue_styles');
