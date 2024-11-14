<?php
/*
Template Name: personal
*/


get_header();


if (is_user_logged_in()) {
    $current_user = wp_get_current_user();

    ?>
    <div class="container order-history-container">
        <h1><?php echo pll_e('Personal Account') ?></h1>
        <div class="user-info">
        <p><strong><?php echo pll_e('Full Name') ?>:</strong> <?php echo esc_html($current_user->display_name); ?></p>
        <p><strong><?php echo pll_e('Email') ?>:</strong> <?php echo esc_html($current_user->user_email); ?></p>
        </div>
        <h2><?php pll_e('Order History'); ?></h2>
        <div id="order-history" class="order-history"></div>
    </div>
    <?php
} else {
    echo '<p>You need to log in to view this page.</p>';
}

get_footer();
?>
