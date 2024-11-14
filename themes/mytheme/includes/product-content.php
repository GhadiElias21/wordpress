<?php
// content-product.php
$image = get_field('image');
$image_url = $image ? esc_url($image['url']) : '';
$image_alt = $image ? esc_attr($image['alt']) : '';
$product_link = get_permalink(get_the_ID());
$rating = get_field('rating');

$current_language = pll_current_language();

$selected_currency = get_selected_currency();
$currencySymbol = $selected_currency === 'BYN' ? ' byn' : '$';

$product_price = get_field('price');
$exchange_rate = get_exchange_rate('USD', $selected_currency);
$converted_price = $product_price * $exchange_rate;
?>


<div class="col-md-4 mb-4">
    <div class="card h-100">
        <?php if ($image_url): ?>
            <img class="product-image" height="280px" src="<?php echo $image_url; ?>" alt="<?php echo $image_alt; ?>" />
        <?php endif; ?>

        <div class="card-body d-flex flex-column">
            <h5 class="card-title"><?php $current_language == 'ru' ? the_field('name_russian') : the_field('name'); ?>
            </h5>


            <p class="card-text"><strong> <?php echo pll_e('Price:') ?></strong>
                <?php echo number_format($converted_price, 2) . $currencySymbol; ?>

            </p>
            <p class="card-text">
                <strong> <?php echo pll_e('Color:') ?></strong>
                <?php $current_language == 'ru' ? the_field('color_russian') : the_field('color'); ?>
            </p>
            <p class="card-text"><strong> <?php echo pll_e('Size:') ?></strong> <?php the_field('size'); ?></p>
            <p class="card-text"><strong> <?php echo pll_e('Stock Status:') ?>
                </strong><?php $current_language == 'ru' ? the_field('stock_status_russian') : the_field('stock_status'); ?>
            </p>
            <div class="star-rating non-clickable">
                <?php
                $stars = range(1, 5);
                foreach ($stars as $i): ?>
                    <span class="fa fa-star <?php echo ($i <= $rating) ? 'checked' : ''; ?>"></span>
                <?php endforeach; ?>
            </div>

            <div>
                <button class="btn btn-danger btn-lg add-to-cart" data-id="<?php echo get_the_ID(); ?>"
                    data-name="<?php the_field('name'); ?>" data-price="<?php the_field('price'); ?>"
                    data-image="<?php echo $image_url; ?>"
                    data-link="<?php echo esc_url($product_link); ?>"><?php echo pll_e('Add To Cart') ?>
                </button>


                <a href="<?php echo esc_url($product_link); ?>"
                    class="btn btn-primary"><?php echo pll_e('Learn More') ?>
                </a>
            </div>
        </div>
    </div>
</div>