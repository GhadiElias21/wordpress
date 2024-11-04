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
function handle_add_to_cart() {
    if (isset($_POST['product'])) {
        $product = $_POST['product'];
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

        $productExists = false;

        foreach ($cart as &$item) {
            if ($item['id'] === $product['id']) {
                $item['quantity'] += 1;
                $productExists = true;
                break;
            }
        }

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

function handle_update_cart() {
    if (isset($_POST['productId']) && isset($_POST['quantity'])) {
        $productId = $_POST['productId'];
        $quantity = intval($_POST['quantity']);
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

        if ($quantity <= 0) {
            $cart = array_filter($cart, function($item) use ($productId) {
                return $item['id'] !== $productId;
            });
        } else {
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
=======
function enable_comments_for_products() {
    add_post_type_support('storeproduct', 'comments');
}
add_action('init', 'enable_comments_for_products');



