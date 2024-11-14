<?php
get_header();
/*
Template Name: Product Page
*/
?>
    <div class="archive-section">
        <div class="product-archive">
            <div class="content-area">
                <h1 class="widget-title"> <?php echo pll_e('top rated products') ?>
                </h1>
            </div>

            <aside class="sidebar-area">
                <?php if (is_active_sidebar('product-archive-sidebar')) : ?>
                    <?php dynamic_sidebar('product-archive-sidebar'); ?>
                <?php endif; ?>
            </aside>
        </div>
        <div class="container">
            <div class="row">
                <?php
                $args = array(
                    'post_type' => 'product',
                    'posts_per_page' => 10,
                );
                $product_query = new WP_Query($args);

                if ($product_query->have_posts()) :
                    while ($product_query->have_posts()) : $product_query->the_post(); ?>
                        <?php get_template_part('includes/product', 'content') ?>

                    <?php endwhile;
                    wp_reset_postdata();
                else :
                    echo '<p class="text-center">' . __('No products found.', 'mytheme') . '</p>';
                endif; ?>
            </div>
        </div>
    </div>

<?php get_footer(); ?>