<?php

function mytheme_setup()
{
    // Add support for menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'mytheme'),
    ));
}


add_action('after_setup_theme', 'mytheme_setup');

function enqueue_bootstrap()
{
    wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', array('jquery'), null, true);
}

add_action('wp_enqueue_scripts', 'enqueue_bootstrap');

//function create_product_post_type() {
//    $labels = array(
//        'name' => __( 'Products' ),
//        'singular_name' => __( 'Product' ),
//        'menu_name' => __( 'Products' ),
//        'name_admin_bar' => __( 'Product' ),
//        'add_new' => __( 'Add New Product' ),
//        'add_new_item' => __( 'Add New Product' ),
//        'new_item' => __( 'New Product' ),
//        'edit_item' => __( 'Edit Product' ),
//        'view_item' => __( 'View Product' ),
//        'all_items' => __( 'All Products' ),
//        'search_items' => __( 'Search Products' ),
//        'parent_item_colon' => __( 'Parent Products:' ),
//        'not_found' => __( 'No products found.' ),
//        'not_found_in_trash' => __( 'No products found in Trash.' )
//    );
//
//    $args = array(
//        'labels' => $labels,
//        'public' => true,
//        'has_archive' => true,
//        'supports' => array( 'thumbnail' ), // Exclude default fields like title and editor
//        'menu_position' => 5,
//        'menu_icon' => 'dashicons-cart',
//        'rewrite' => array( 'slug' => 'products' ),
//    );
//
//    register_post_type( 'product', $args );
//}
//
//add_action( 'init', 'create_product_post_type' );

