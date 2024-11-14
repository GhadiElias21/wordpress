<?php get_header();
$product_link = get_permalink(get_the_ID());
$current_language = pll_current_language();
/* Template Name: product info Page  */

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
                    <h1 class="mb-3"><?php $current_language != 'ru' ? the_field('name_russian') : the_field('name'); ?></h1>
                    <p class="lead"> <?php $current_language != 'ru' ? the_field('description_russian') : the_field('description'); ?>
                    </p>
                    <p class="text-primary"><strong>
                            <?php echo pll_e('Price:') ?></strong>
                        <?php the_field('price'); ?>$</p>
                    <p><strong> <?php echo pll_e('Color:') ?></strong>
                        <?php $current_language == 'ru' ? the_field('color_russian') : the_field('color'); ?>
                    </p>
                    <p><strong><?php echo pll_e('Stock Status:') ?></strong>
                        <?php $current_language == 'ru' ? the_field('stock_status_russian') : the_field('stock_status'); ?>

                    </p>
                    <button class="btn btn-danger btn-lg mt-3 add-to-cart"
                            data-id="<?php echo get_the_ID(); ?>"
                            data-name="<?php the_field('name'); ?>" data-price="<?php the_field('price'); ?>"
                            data-image="<?php echo $image_url; ?>"
                            data-link="<?php echo esc_url($product_link); ?>"><?php echo pll_e('Add To Cart') ?></button>
                    <a href="<?php echo home_url('/ru/product'); ?>" class="btn btn-dark btn-lg mt-3">
                        <?php echo pll_e('Back To Catalog') ?></a>
                    <?php get_template_part('includes/product','rating'); ?>
                </div>
            </div>

            <?php get_template_part('includes/product','comments'); ?>

        <?php endwhile; else: ?>
            <p><?php _e('Sorry, no product matched your criteria.', 'mytheme'); ?></p>
        <?php endif; ?>
    </div>

<?php get_footer(); ?>