<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
function enqueue_custom_scripts()
{
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

add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');
function handle_add_to_cart()
{
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

function handle_update_cart()
{
    if (isset($_POST['productId']) && isset($_POST['quantity'])) {
        $productId = $_POST['productId'];
        $quantity = intval($_POST['quantity']);
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

        if ($quantity <= 0) {
            $cart = array_filter($cart, function ($item) use ($productId) {
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
function handle_remove_from_cart()
{
    if (isset($_POST['productId'])) {
        $productId = $_POST['productId'];
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

        // Filter out the item to remove
        $cart = array_filter($cart, function ($item) use ($productId) {
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
function create_order_post_type() {
    register_post_type('custom_order',
        array(
            'labels' => array(
                'name' => __('Orders'),
                'singular_name' => __('Order'),
                'menu_name' => __('Orders'),
                'name_admin_bar' => __('Order'),
                'add_new' => __('Add New'),
                'add_new_item' => __('Add New Order'),
                'edit_item' => __('Edit Order'),
                'new_item' => __('New Order'),
                'view_item' => __('View Order'),
                'search_items' => __('Search Orders'),
                'not_found' => __('No orders found'),
                'not_found_in_trash' => __('No orders found in Trash'),
                'all_items' => __('All Orders'),
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title'),
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-cart',
            'capability_type' => 'custom_order',
            'map_meta_cap' => true,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'show_in_rest' => true,
            'show_in_quick_edit' => true,
            'publicly_queryable' => true,
        )
    );
}

add_action('init', 'create_order_post_type', 0);


//add_action('init', function() {
//    global $wp_post_types;
//    echo '<pre>';
//    print_r(array_keys($wp_post_types));
//    echo '</pre>';
//});


function create_order()
{
    if (!isset($_POST['full_name'], $_POST['email'], $_POST['products'], $_POST['total_amount'])) {
        wp_send_json_error('Missing required fields');
        return;
    }

    $full_name = sanitize_text_field($_POST['full_name']);
    $email = sanitize_email($_POST['email']);

    // Decode the JSON string of products
    $products = json_decode(stripslashes($_POST['products']), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error('Invalid JSON format for products');
        return;
    }

    $total_amount = sanitize_text_field($_POST['total_amount']);

    // Create the custom order post
    $order_id = wp_insert_post(array(
        'post_type' => 'custom_order',
        'post_title' => 'Order for ' . $full_name,
        'post_status' => 'publish'
    ));

    if (is_wp_error($order_id)) {
        wp_send_json_error('Failed to create order post');
        return;
    }

    // Store the product data as a JSON string
    update_post_meta($order_id, 'product_data', wp_slash(json_encode($products)));

    // Update custom fields
    update_field('full_name', $full_name, $order_id);
    update_field('email', $email, $order_id);
    update_field('total_amount', $total_amount, $order_id);

    wp_send_json_success('Order created successfully');
}

add_action('wp_ajax_create_order', 'create_order');
add_action('wp_ajax_nopriv_create_order', 'create_order');

function set_custom_edit_order_columns($columns)
{
    $columns['fullname'] = __('Fullname');
    $columns['email'] = __('Email');
    $columns['products'] = __('Products');
    $columns['total_amount'] = __('Total Amount');
    return $columns;
}

add_filter('manage_custom_order_posts_columns', 'set_custom_edit_order_columns');

function custom_order_column($column, $post_id)
{
    switch ($column) {
        case 'fullname':
            echo '<span style="font-weight: bold;">' . esc_html(get_field('full_name', $post_id)) . '</span>';
            break;
        case 'email':
            echo '<span style="color: blue;">' . esc_html(get_field('email', $post_id)) . '</span>';
            break;
        case 'products':
            $product_data = get_post_meta($post_id, 'product_data', true); // Retrieve the product data
            $products = json_decode($product_data, true);
            if ($products && is_array($products)) {
                foreach ($products as $product) {
                    if (isset($product['name']) && isset($product['quantity']) && isset($product['price'])) {
                        echo '<div style="margin-bottom: 5px;">' . esc_html($product['name']) . ' x' . esc_html($product['quantity']) . ' - $' . esc_html($product['price'] * $product['quantity']) . '</div>';
                    } else {
                        echo 'Invalid product data<br>';
                    }
                }
            } else {
                echo 'No products found';
            }
            break;
        case 'total_amount':
            echo '<span style="color: #268000;">$' . esc_html(get_field('total_amount', $post_id)) . '</span>';
            break;
    }
}

add_action('manage_custom_order_posts_custom_column', 'custom_order_column', 10, 2);
