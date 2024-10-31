<?php get_header(); ?>



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
                <p><strong>Stock Status:</strong> <?php the_field('description'); ?></p>

                <button class="btn btn-danger btn-lg mt-3 add-to-cart" data-id="<?php echo get_the_ID(); ?>" data-name="<?php the_field('name'); ?>" data-price="<?php the_field('price'); ?>" data-image="<?php echo $image_url; ?>"  >Add to Cart</button>
                <a href="<?php echo home_url('/catalog'); ?>" class="btn btn-dark btn-lg mt-3">Back to Catalog</a>
            </div>
        </div>
<<<<<<< Updated upstream
=======

        <!-- Comments Section -->
        <div class="mt-5">
            <h3 class="mb-4">Customer Reviews</h3>
            <?php
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;
            ?>
        </div>

>>>>>>> Stashed changes
    <?php endwhile; else: ?>
        <p><?php _e('Sorry, no product matched your criteria.', 'mytheme'); ?></p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>

