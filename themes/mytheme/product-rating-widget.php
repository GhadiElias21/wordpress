<?php
class Product_Rating_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'product_rating_widget',
            __('Product Rating Widget', 'textdomain'),
            array('description' => __('Displays top 5 products by rating', 'textdomain'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];


        $query_args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
         'orderby' => 'meta_value_num',
            'meta_key' => 'rating',
            'order' => 'DESC',
        );

        $query = new WP_Query($query_args);

        if ($query->have_posts()) {
            echo '<ul class="top-rated-products">';
            while ($query->have_posts()) {
                $query->the_post();
                $rating = get_post_meta(get_the_ID(), 'rating', true);
                echo '<li class="top-rated-product">';
                echo '<a href="' . get_permalink() . '" class="product-link">' . get_the_title() . '</a>';
                echo '<span class="product-rating">'  . esc_html($rating) . ' ★ </span>';
                echo '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p>' . __('No top rated products found.', 'textdomain') . '</p>';
        }

        wp_reset_postdata();
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Top Rated Products', 'textdomain');
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'textdomain'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }
}

