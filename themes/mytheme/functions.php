<?php
require_once get_template_directory() . '/widgets/product-rating-widget.php';

function mytheme_setup()
{
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'mytheme'),
    ));
}

add_action('after_setup_theme', 'mytheme_setup');

function enqueue_styles()
{
    wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', array('jquery'), '', true);
    wp_enqueue_style('bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css');
    wp_enqueue_style('custom-style', get_template_directory_uri() . '/css/style.css');
    wp_enqueue_style('custom-rating', get_template_directory_uri() . '/css/rating.css');
    wp_enqueue_style('custom-cart-style', get_template_directory_uri() . '/css/cart.css');
    wp_enqueue_style('custom-menu-styles', get_template_directory_uri() . '/css/custom-menu-styles.css');
    wp_enqueue_style('personal-account', get_template_directory_uri() . '/css/personal-account.css');
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css');

}


add_action('wp_enqueue_scripts', 'enqueue_styles');
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');

function enqueue_custom_scripts()
{
    wp_enqueue_script('jquery');

    wp_enqueue_script(
        'shopping-cart',
        get_template_directory_uri() . '/js/cart.js',
        array('jquery'),
        null,
        true
    );

    wp_enqueue_script(
        'personal-account-js',
        get_template_directory_uri() . '/js/personal-account.js',
        array('jquery'),
        null,
        true
    );

    wp_enqueue_script(
        'star-rating',
        get_template_directory_uri() . '/includes/js/star-rating.js',
        array('jquery'),
        null,
        true
    );

    wp_localize_script('shopping-cart', 'ajax_obj', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'current_user' => array(
            'display_name' => is_user_logged_in() ? wp_get_current_user()->display_name : '',
            'user_email' => is_user_logged_in() ? wp_get_current_user()->user_email : ''
        ),
        'selectedCurrency' => get_selected_currency(),
        'exchangeRate' => get_exchange_rate('USD', get_selected_currency()),
        'translations' => array(
            'empty_cart' => pll__('Your cart is empty.'),
            'sweater' => pll__('Sweater')
        )
    ));

    wp_localize_script('personal-account-js', 'userData', array(
        'email' => wp_get_current_user()->user_email,
        'restUrl' => esc_url(rest_url('custom/v1/orders')),
        'nonce' => wp_create_nonce('wp_rest'),
        'selectedCurrency' => get_selected_currency(),
        'exchangeRate' => get_exchange_rate('USD', get_selected_currency()),
        'translations' => array(
            'no_orders' => pll__('No orders found.'),
            'failed_to_fetch' => pll__('Failed to fetch orders.'),
            'order_id' => pll__('Order ID:'),
            'order_date' => pll__('Order Date:'),
            'total_amount' => pll__('Total Amount:'),
            'status' => pll__('Status:'),
            'products' => pll__('Products:'),
            'pending' => pll__('Pending'),
            'completed' => pll__('Completed'),
            'canceled' => pll__('Canceled'),
            'quantity' => pll__('Quantity:'),
            'price' => pll__('Price of 1 unit:'),
        ),
    ));

}

add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');

function handle_add_to_cart()
{
    if (isset($_POST['product'])) {
        $product = $_POST['product'];

        if (!isset($product['slug'])) {
            wp_send_json_error(array('message' => 'Product slug not set', 'product' => $product));
            return;
        }

        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        $productExists = false;

        foreach ($cart as &$item) {
            if ($item['slug'] === $product['slug']) {
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

        wp_send_json_success(array(
            'cart_count' => count($cart),
        ));
    } else {
        wp_send_json_error(array('message' => 'Failed to add product', 'product_data' => $_POST['product']));
    }
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
function handle_remove_from_cart()
{
    if (isset($_POST['productId'])) {
        $productId = $_POST['productId'];
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];


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
function create_order_post_type()
{
    register_post_type(
        'custom_order',
        array(
            'labels' => array(
                'name' => __('Orders'),
                'singular_name' => __('Order'),
                'menu_name' => __('Orders'),
                'name_admin_bar' => __('Order'),
                'add_new' => __('Add New'),
                'add_new_item' => __('Add New Order'),
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

function create_order()
{
    if (!isset($_POST['full_name'], $_POST['email'], $_POST['products'], $_POST['total_amount'])) {
        wp_send_json_error('Missing required fields');
        return;
    }

    $full_name = sanitize_text_field($_POST['full_name']);
    $email = sanitize_email($_POST['email']);

    $products = json_decode(stripslashes($_POST['products']), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error('Invalid JSON format for products');
        return;
    }

    $total_amount = sanitize_text_field($_POST['total_amount']);

    $order_id = wp_insert_post(array(
        'post_type' => 'custom_order',
        'post_title' => 'Order for ' . $full_name,
        'post_status' => 'pending',
    ));

    if (is_wp_error($order_id)) {
        wp_send_json_error('Failed to create order post');
        return;
    }

    update_post_meta($order_id, 'product_data', wp_slash(json_encode($products)));

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
    $columns['total_amount'] = __('Total Amount $');
    $columns['total_amount_byn'] = __('Total Amount byn');

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
            $product_data = get_post_meta($post_id, 'product_data', true);
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
        case 'total_amount_byn':
            $total_amount_usd = get_field('total_amount', $post_id);
            $exchange_rate = get_exchange_rate('USD', 'BYN');
            $total_amount_byn = $total_amount_usd * $exchange_rate;
            echo '<span style="color: #268000;">  ' . number_format($total_amount_byn, 2) . ' ' . 'byn' . '</span>';
            break;
    }
}

add_action('manage_custom_order_posts_custom_column', 'custom_order_column', 10, 2);


function my_theme_register_menus()
{
    register_nav_menus(
        array(
            'primary' => __('Primary Menu', 'my-theme'),
        )
    );
}

add_action('after_setup_theme', 'my_theme_register_menus');


function handle_product_rating()
{
    if (isset($_POST['rating']) && isset($_POST['product_id']) && is_user_logged_in()) {
        $product_id = intval($_POST['product_id']);
        $rating = intval($_POST['rating']);

        $ratings = get_post_meta($product_id, 'product_ratings', true);
        if (!$ratings) {
            $ratings = array();
        }

        $user_id = get_current_user_id();
        $ratings[$user_id] = $rating;

        update_post_meta($product_id, 'product_ratings', $ratings);

        $average_rating = array_sum($ratings) / count($ratings);
        update_post_meta($product_id, 'rating', $average_rating);

        wp_redirect(get_permalink($product_id));
        exit;
    }
}

add_action('init', 'handle_product_rating');


function my_custom_sidebar()
{
    register_sidebar(array(
        'name' => 'Product Archive Sidebar',
        'id' => 'product-archive-sidebar',
        'before_widget' => '<div class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));
}

add_action('widgets_init', 'my_custom_sidebar');

function register_product_rating_widget()
{
    register_widget('Product_Rating_Widget');
}

add_action('widgets_init', 'register_product_rating_widget');

function register_custom_rest_routes()
{
    register_rest_route('custom/v1', '/orders', array(
        'methods' => 'POST',
        'callback' => 'handle_get_user_orders',
        'permission_callback' => function () {
            return is_user_logged_in();
        }
    ));
}

add_action('rest_api_init', 'register_custom_rest_routes');

function handle_get_user_orders(WP_REST_Request $request)
{
    $current_user = wp_get_current_user();
    $email = sanitize_email($request->get_param('email'));

    if ($email !== $current_user->user_email) {
        return new WP_Error('unauthorized', 'Unauthorized access', array('status' => 401));
    }

    $orders = get_posts(array(
        'post_type' => 'custom_order',
        'meta_key' => 'email',
        'meta_value' => $email,
        'post_status' => 'any',
    ));

    if (empty($orders)) {
        return rest_ensure_response([]);
    }

    $order_data = array();
    foreach ($orders as $order) {
        $products = get_post_meta($order->ID, 'product_data', true);
        $total_amount = get_field('total_amount', $order->ID);
        $status = get_post_status($order->ID);

        switch ($status) {
            case 'publish':
                $status = 'Completed';
                break;
            case 'pending':
                $status = 'Pending';
                break;
            case 'draft':
                $status = 'Canceled';
                break;
            default:
                $status = ucfirst($status);
                break;
        }

        $order_data[] = array(
            'order_id' => $order->ID,
            'order_date' => get_the_date('', $order),
            'products' => json_decode($products, true),
            'total_amount' => $total_amount,
            'status' => $status
        );
    }

    return rest_ensure_response($order_data);
}

function modify_custom_order_query($query)
{
    if (is_admin() && $query->is_main_query() && $query->get('post_type') === 'custom_order') {
        $query->set('post_status', array('publish', 'pending', 'canceled', 'completed', 'any'));
    }
}

add_action('pre_get_posts', 'modify_custom_order_query');

add_filter('use_block_editor_for_post_type', '__return_false');

function add_custom_templates($templates)
{
    $templates['personal.php'] = 'personal account';
    $templates['archive-product.php'] = 'products';
    $templates['front-page.php'] = 'Home Page';
    $templates['single-product.php'] = 'product info Page';


    return $templates;
}

add_filter('theme_page_templates', 'add_custom_templates');


function register_strings()
{
    pll_register_string('no_orders', 'No orders found.', 'orders');
    pll_register_string('failed_to_fetch', 'Failed to fetch orders.', 'orders');
    pll_register_string('order_id', 'Order ID:', 'orders');
    pll_register_string('order_date', 'Order Date:', 'orders');
    pll_register_string('total_amount', 'Total Amount:', 'orders');
    pll_register_string('status', 'Status:', 'orders');
    pll_register_string('products', 'Products:', 'orders');
    pll_register_string('pending', 'Pending', 'orders');
    pll_register_string('completed', 'Completed', 'orders');
    pll_register_string('canceled', 'Canceled', 'orders');
    pll_register_string('quantity', 'Quantity:', 'orders');
    pll_register_string('price', 'Price of 1 unit:', 'orders');

    pll_register_string('personal account', 'Personal Account', 'Home page');
    pll_register_string('full name', 'Full Name', 'Home page');
    pll_register_string('email', 'Email', 'Home page');
    pll_register_string('top rated products', 'top rated products', 'Home page');
    pll_register_string('about us', 'About Us', 'Home page');
    pll_register_string('about us text', 'Discover the story behind our brand and what drives our passion for fashion. We strive to bring you the best in style, quality, and affordability.', 'Home page');
    pll_register_string('Customer Testimonials', 'Customer Testimonials', 'Home page');
    pll_register_string('Featured Products', 'Featured Products', 'Home page');

    pll_register_string('Price', 'Price:', 'products');
    pll_register_string('Color', 'Color:', 'products');
    pll_register_string('Size', 'Size:', 'products');
    pll_register_string('Stock Status', 'Stock Status:', 'products');

    pll_register_string('Close', 'Close', 'Cart Modal');
    pll_register_string('PLace An Order', 'PLace An Order', 'Cart Modal');
    pll_register_string('Confirm Order', 'Confirm Order', 'Cart Modal');
    pll_register_string('Shopping Cart', 'Shopping Cart', 'Cart Modal');
    pll_register_string('empty_cart', 'Your cart is empty.', 'Cart Modal');
    pll_register_string('sweater', 'Sweater', 'Cart Modal');

    pll_register_string('Order Confirmation', 'Order Confirmation', 'Cart Modal');
    pll_register_string('order Confirmation message', 'Your order will be placed using the following details:', 'Cart Modal');

    pll_register_string('add to cart', 'Add To Cart', 'buttons');
    pll_register_string('back to catalog', 'Back To Catalog', 'buttons');
    pll_register_string('learn more', 'Learn More', 'buttons');


}

add_action('init', 'register_strings');


function get_selected_currency()
{
    if (isset($_COOKIE['selected_currency'])) {
        return $_COOKIE['selected_currency'];
    } else {
        return '$';
    }
}

function get_exchange_rate($base_currency, $target_currency)
{
    $api_url = "https://v6.exchangerate-api.com/v6/75394a746605ddde13f6cf29/latest/$base_currency";
    $response = wp_remote_get($api_url);

    if (is_wp_error($response)) {
        return 1;
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (isset($data['conversion_rates'][$target_currency])) {
        return $data['conversion_rates'][$target_currency];
    } else {
        return 1;
    }
}
