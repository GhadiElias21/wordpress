<?php
$product_id = get_the_ID();
$average_rating = get_post_meta($product_id, 'rating', true);
?>

<div class="mt-4">
    <h4><?php _e('Rate this product', 'mytheme'); ?></h4>
    <?php if (is_user_logged_in()) : ?>
        <form id="product-rating-form" method="post">
            <div class="star-rating clickable">
                <span class="fa fa-star" data-rating="1"></span>
                <span class="fa fa-star" data-rating="2"></span>
                <span class="fa fa-star" data-rating="3"></span>
                <span class="fa fa-star" data-rating="4"></span>
                <span class="fa fa-star" data-rating="5"></span>
            </div>
            <input type="hidden" name="rating" id="rating" value="0">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <input type="submit" value="<?php _e('Submit Rating', 'mytheme'); ?>" class="btn btn-primary">
        </form>
    <?php else : ?>
        <p><?php _e('Please log in to rate this product.', 'mytheme'); ?></p>
    <?php endif; ?>

    <?php if ($average_rating) : ?>
        <p class="mt-2">
            <strong><?php _e('Average Rating:', 'mytheme'); ?></strong> <?php echo sprintf('%.1f', $average_rating); ?>
        </p>
    <?php endif; ?>
</div>