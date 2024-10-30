<?php get_header(); ?>
<div class="jumbotron jumbotron-fluid text-center bg-dark text-white">
    <div class="container">
        <h1 class="display-4"><?php bloginfo('name'); ?></h1>
        <p class="lead"><?php bloginfo('description'); ?></p>
        <a href="<?php echo home_url('/catalog'); ?>" class="btn btn-outline-light btn-lg">Shop Now</a>
    </div>
</div>

<div id="catalog" class="container my-5">
    <h2 class="text-center">Featured Products</h2>

        <div class="row">
            <?php
            $args = array(
                'post_type' => 'storeProduct',
                'posts_per_page' => 10,
            );
            $product_query = new WP_Query($args);

            if ($product_query->have_posts()) :
                while ($product_query->have_posts()) : $product_query->the_post(); ?>
                    <?php get_template_part('includes/product','content')?>
                <?php endwhile;
                wp_reset_postdata();
            else :
                echo '<p class="text-center">' . __('No products found.', 'mytheme') . '</p>';
            endif; ?>
        </div>
    </div>


<div class="container-fluid bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h3>About Us</h3>
                <p>Discover the story behind our brand and what drives our passion for fashion. We strive to bring you the best in style, quality, and affordability.</p>
                <a href="#about" class="btn btn-dark btn-lg">Learn More</a>
            </div>
            <div class="col-md-6">
                <img src="https://th.bing.com/th/id/R.747d880586e1f6fbb2af6fabc8fbaac6?rik=utX2uF9hpiyeTw&pid=ImgRaw&r=0g" class="img-fluid" alt="About Us">
            </div>
        </div>
    </div>
</div>

<div class="container my-5">
    <h2 class="text-center">Customer Testimonials</h2>
    <div class="row">
        <div class="col-md-4">
            <blockquote class="blockquote">
                <p class="mb-0">Amazing quality and fast shipping!</p>
                <footer class="blockquote-footer">leo in <cite title="Source Title">Lyon</cite></footer>
            </blockquote>
        </div>
        <div class="col-md-4">
            <blockquote class="blockquote">
                <p class="mb-0">Stylish and affordable, my favorite store!</p>
                <footer class="blockquote-footer">rita in <cite title="Source Title">Madrid</cite></footer>
            </blockquote>
        </div>
        <div class="col-md-4">
            <blockquote class="blockquote">
                <p class="mb-0">Great customer service and amazing products!</p>
                <footer class="blockquote-footer">Ghadi in <cite title="Source Title">Minsk</cite></footer>
            </blockquote>
        </div>
    </div>
</div>

<?php get_footer(); ?>
