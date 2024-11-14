<!DOCTYPE html>
<html <?php language_attributes(); ?>>


<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

    <nav class="navbar  navbar-expand-lg navbar-light bg-light  ">
        <a class="navbar-brand" href="<?php echo home_url(); ?>"><?php bloginfo('name'); ?></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">


                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container' => false,
                    'menu_class' => 'navbar-nav',
                    'items_wrap' => '%3$s',
                ));

                if (function_exists('pll_the_languages')) {
                    pll_the_languages(array('dropdown' => 1, 'show_flags' => 1, 'show_names' => 0));
                }

                ?>
            </ul>
            <nav class="nav-item">

                <a class="nav-link" href="#" id="cart-icon" data-toggle="modal" data-target="#cartModal">
                    <div class="cart-container">
                        <span id="cart-count" class="badge badge-primary">0</span>
                        <i class="bi bi-cart"></i>
                    </div>
                </a>
            </nav>
            </ul>
        </div>
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="currencyDropdown" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false"> Currency
            </button>
            <div class="dropdown-menu" aria-labelledby="currencyDropdown">
                <a class="dropdown-item" href="#" onclick="setCurrency('USD')">USD</a>
                <a class="dropdown-item" href="#" onclick="setCurrency('BYN')">BYN</a>
            </div>
        </div>
    </nav>
    <script> function setCurrency(currency) { document.cookie = "selected_currency=" + currency + "; path=/"; location.reload(); } </script>
    <?php get_template_part('cart-modal'); ?>

    <?php wp_footer(); ?>
</body>

</html>