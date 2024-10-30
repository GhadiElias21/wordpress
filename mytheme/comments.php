<?php if ( post_password_required() ) { return; } ?>
<div id="comments" class="comments-area mt-5">
    <h2 class="text-center mb-4">
        <?php _e('Comments', 'textdomain'); ?>
    </h2>
    <?php if ( have_comments() ) : ?>
        <h3 class="comments-title">
            <?php
            printf(
                _nx('One comment', '%1$s comments', get_comments_number(), 'comments title', 'textdomain'),
                number_format_i18n(get_comments_number())
            );
            ?>
        </h3>
        <ol class="comment-list list-group mb-4">
            <?php
            wp_list_comments(array(
                'style' => 'ol',
                'short_ping' => true,
                'avatar_size' => 50,
                'callback' => function($comment, $args, $depth) {
                    $GLOBALS['comment'] = $comment; ?>
                    <li id="comment-<?php comment_ID(); ?>" class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="comment-author vcard d-flex align-items-center">
                                <?php echo get_avatar($comment, 50, '', '', array('class' => 'rounded-circle mr-3')); ?>
                                <cite class="fn"><?php comment_author_link(); ?></cite>
                            </div>
                            <div>
                                <span class="comment-date text-muted small"><?php printf(__('%1$s at %2$s', 'textdomain'), get_comment_date(), get_comment_time()); ?></span>
                            </div>
                        </div>
                        <div class="comment-text mt-3">
                            <?php comment_text(); ?>
                        </div>
                        <div class="reply mt-2">
                            <?php comment_reply_link(array_merge($args, array('reply_text' => __('Reply', 'textdomain'), 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
                        </div>
                    </li>
                <?php },
            ));
            ?>
        </ol>
        <?php the_comments_navigation(); ?>
    <?php endif; ?>
    <?php if ( ! comments_open() && get_comments_number() ) : ?>
        <p class="no-comments"><?php esc_html_e('Comments are closed.', 'textdomain'); ?></p>
    <?php endif; ?>
    <div class="comment-form mt-4">
        <h3 class="text-center mb-4"><?php _e('Leave a Reply', 'textdomain'); ?></h3>
        <?php comment_form(array('class_submit' => 'btn btn-primary')); ?>
    </div>
</div>
