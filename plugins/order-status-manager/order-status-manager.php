<?php
/*
Plugin Name: Order Status Manager
Description: A plugin to manage order statuses.
Version: 1.0
Author: ghadghoud
*/
include_once plugin_dir_path(__FILE__) . 'order-status-functions.php'; // Include the new file
add_action('init', 'osm_check_order_post_type');
function osm_check_order_post_type()
{
    if (!post_type_exists('custom_order')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die('Order Status Manager requires the Order post type to be registered.');
    }
}
add_filter('manage_custom_order_posts_columns', 'add_order_status_column');
function add_order_status_column($columns)
{
    $columns['order_status'] = 'Status';

    return $columns;
}


add_action('manage_custom_order_posts_custom_column', 'display_order_status_column', 10, 2);
function display_order_status_column($column, $post_id)
{
    if ($column == 'order_status') {
        $status = get_post_meta($post_id, 'order_status', true);

        echo osm_generate_order_status_dropdown($status, $post_id);
    }
}
add_action('admin_enqueue_scripts', 'osm_enqueue_scripts');
function osm_enqueue_scripts() {
    wp_enqueue_script('osm-admin-script', plugin_dir_url(__FILE__) . 'js/osm-admin.js', array('jquery'), '1.0', true);
    wp_localize_script('osm-admin-script', 'ajax_obj', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('osm_nonce')
    ));
}
add_action('wp_ajax_update_order_status', 'osm_update_order_status');
function osm_update_order_status()
{
    check_ajax_referer('osm_nonce', 'nonce');

    $post_id = intval($_POST['post_id']);

    $new_status = sanitize_text_field($_POST['status']);

    if (update_post_meta($post_id, 'order_status', $new_status)) {
        wp_send_json_success('Order status updated successfully.');

    } else {
        wp_send_json_error('Failed to update order status.');
    }
}