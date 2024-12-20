<?php get_header();
$product_link = get_permalink(get_the_ID());
?>

    <div class="container mt-5">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <div class="row">
                <div class="col-md-6">
                    <?php
                    $image = get_field('image');
                    if ($image) :
                        $image_url = esc_url($image['url']);
                        $image_alt = esc_attr($image['alt']);
                        ?>
                        <img src="<?php echo $image_url; ?>" alt="<?php echo $image_alt; ?>" class="img-fluid mb-3 rounded">
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <h1 class="mb-3"><?php the_field('name'); ?></h1>
                    <p class="lead"><?php the_field('description'); ?></p>
                    <p class="text-primary"><strong>Price:</strong> <?php the_field('price'); ?>$</p>
                    <p><strong>Color:</strong> <?php the_field('color'); ?></p>
                    <p><strong>Stock Status:</strong> <?php the_field('stock_status'); ?></p>

                    <button class="btn btn-danger btn-lg mt-3 add-to-cart" data-id="<?php echo get_the_ID(); ?>" data-name="<?php the_field('name'); ?>" data-price="<?php the_field('price'); ?>" data-image="<?php echo $image_url; ?>" data-link="<?php echo esc_url($product_link); ?>">Add to Cart</button>
                    <a href="<?php echo home_url('/product'); ?>" class="btn btn-dark btn-lg mt-3">Back to Catalog</a>

                    <?php get_template_part('includes/product','rating'); ?>
                </div>
            </div>

            <?php get_template_part('includes/product','comments'); ?>

        <?php endwhile; else: ?>
            <p><?php _e('Sorry, no product matched your criteria.', 'mytheme'); ?></p>
        <?php endif; ?>
    </div>

<?php get_footer(); ?>