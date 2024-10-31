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
    wp_enqueue_style('bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css', array(), null);
    wp_enqueue_style('custom-style', get_template_directory_uri() . '/style.css');

}

add_action('wp_enqueue_scripts', 'enqueue_bootstrap');
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');
function enqueue_custom_scripts() {
    wp_enqueue_script('jquery'); // Load jQuery
    wp_enqueue_script(
        'shopping-cart',
        get_template_directory_uri() . '/cart.js',
        array('jquery'), // Make jQuery a dependency
        null,
        true // Load script in footer

    );

    wp_localize_script('shopping-cart', 'ajax_obj', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}


<<<<<<< Updated upstream
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
=======
function enable_comments_for_products() {
    add_post_type_support('storeproduct', 'comments');
}
add_action('init', 'enable_comments_for_products');


function start_session() {
    if (!session_id()) {
        session_start();
    }
}
add_action('init', 'start_session');

// Handle adding items to the cart
function handle_add_to_cart() {
    if (isset($_POST['product'])) {
        $product = $_POST['product'];
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

        $productExists = false;

        foreach ($cart as &$item) { // Use reference to modify the original array
            if ($item['id'] === $product['id']) {
                // Product found, increase quantity
                $item['quantity'] += 1;
                $productExists = true;
                break;
            }
        }

        // If product does not exist, add it to the cart
        if (!$productExists) {
            $product['quantity'] = 1;
            $cart[] = $product;
        }

        $_SESSION['cart'] = $cart;

        // Return the updated cart count
        wp_send_json_success(array(
            'cart_count' => count($cart),
        ));
    }

    wp_send_json_error(array('message' => 'Failed to add product'));
}
add_action('wp_ajax_add_to_cart', 'handle_add_to_cart');
add_action('wp_ajax_nopriv_add_to_cart', 'handle_add_to_cart');

// Handle updating items in the cart
function handle_update_cart() {
    if (isset($_POST['productId']) && isset($_POST['quantity'])) {
        $productId = $_POST['productId'];
        $quantity = intval($_POST['quantity']);
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

        // If quantity is zero or less, remove the item from the cart
        if ($quantity <= 0) {
            $cart = array_filter($cart, function($item) use ($productId) {
                return $item['id'] !== $productId;
            });
        } else {
            // Update quantity if the product exists
            foreach ($cart as &$item) {
                if ($item['id'] === $productId) {
                    $item['quantity'] = $quantity;
                    break;
                }
            }
        }

        $_SESSION['cart'] = $cart;

        wp_send_json_success(array(
            'cart_count' => count($cart)
        ));
    }
    wp_send_json_error();
}
add_action('wp_ajax_update_cart', 'handle_update_cart');
add_action('wp_ajax_nopriv_update_cart', 'handle_update_cart');


// Handle removing items from the cart
function handle_remove_from_cart() {
    if (isset($_POST['productId'])) {
        $productId = $_POST['productId'];
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

        // Filter out the item to remove
        $cart = array_filter($cart, function($item) use ($productId) {
            return $item['id'] !== $productId;
        });

        $_SESSION['cart'] = $cart;

        wp_send_json_success(array(
            'cart_count' => count($cart)
        ));
    }
    wp_send_json_error();
}
add_action('wp_ajax_remove_from_cart', 'handle_remove_from_cart');
add_action('wp_ajax_nopriv_remove_from_cart', 'handle_remove_from_cart');

>>>>>>> Stashed changes

