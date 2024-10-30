<?php
// content-product.php
$image = get_field('image');
$image_url = $image ? esc_url($image['url']) : '';
$image_alt = $image ? esc_attr($image['alt']) : '';
$product_link = get_permalink();

?>

<div class="col-md-4 mb-4">
    <div class="card h-100">
        <?php if ($image_url) : ?>
            <img src="<?php echo $image_url; ?>" alt="<?php echo $image_alt; ?>" class="card-img-top" />
        <?php endif; ?>
        <div class="card-body d-flex flex-column">
            <h5 class="card-title"><?php the_field('name'); ?></h5>
            <p class="card-text"><strong>Price:</strong> <?php the_field('price'); ?>$</p>
            <p class="card-text"><strong>Color:</strong> <?php the_field('color'); ?></p>
            <p class="card-text"><strong>Size:</strong> <?php the_field('size'); ?></p>
            <p class="card-text"><strong>Stock Status:</strong> <?php the_field('stock_status'); ?></p>
            <div class="mt-auto">
                <a href="#" class="btn btn-primary">Add to Cart</a>
                <a href="<?php echo esc_url($product_link); ?>" class="btn btn-danger">learn more</a>

            </div>
        </div>
    </div>
</div>