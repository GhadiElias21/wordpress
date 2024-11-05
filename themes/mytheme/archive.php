<?php get_header(); ?>

    <div class="container">
        <h1 class="text-center"><?php bloginfo('name'); ?></h1>
        <p class="text-center"><?php bloginfo('description'); ?></p>

        <!-- Products Section -->
        <h2 class="text-center my-4">Our Products</h2>
        <div class="row">
            <?php
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => 10,
            );
            $product_query = new WP_Query($args);

            if ($product_query->have_posts()) :
                while ($product_query->have_posts()) : $product_query->the_post();
                    // Include the product card template part
                    get_template_part('includes/content', 'product');
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p class="text-center">' . __('No products found.', 'mytheme') . '</p>';
            endif; ?>
        </div>
    </div>
<?php get_footer(); ?>