<?php
if (post_password_required()) {
    return;
}

// Properly initialise all variables required by comment_form() fields
$commenter     = wp_get_current_commenter();
$req           = get_option('require_name_email');
$aria_req      = ($req ? " aria-required='true'" : '');
global $user_identity;
if (empty($user_identity)) {
    $current_user  = wp_get_current_user();
    $user_identity = $current_user->exists() ? $current_user->display_name : '';
}
?>

<div id="comments" class="comments-area">
    <?php if (have_comments()) : ?>
        <h3 class="comments-title">
            <?php
            printf(
                _nx('One comment', '%1$s comments', get_comments_number(), 'comments title', 'mytheme'),
                number_format_i18n(get_comments_number())
            );
            ?>
        </h3>

        <ol class="comment-list">
            <?php
            wp_list_comments(array(
                'style' => 'ol',
                'short_ping' => true,
                'avatar_size' => 50,
            ));
            ?>
        </ol>

        <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
            <nav class="comment-navigation" role="navigation">
                <div class="nav-previous"><?php previous_comments_link(__('Older Comments', 'mytheme')); ?></div>
                <div class="nav-next"><?php next_comments_link(__('Newer Comments', 'mytheme')); ?></div>
            </nav>
        <?php endif; ?>

    <?php endif; ?>

    <?php if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) : ?>
        <p class="no-comments"><?php _e('Comments are closed.', 'mytheme'); ?></p>
    <?php endif; ?>

    <?php
    comment_form(array(
        'title_reply' => __('Leave a Comment', 'mytheme'),
        'title_reply_to' => __('Leave a Reply to %s', 'mytheme'),
        'cancel_reply_link' => __('Cancel Reply', 'mytheme'),
        'label_submit' => __('Post Comment', 'mytheme'),
        'comment_field' => '<p class="comment-form-comment"><label for="comment">' . _x('Comment', 'noun') . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>',
        'must_log_in' => '<p class="must-log-in">' . sprintf(__('You must be <a href="%s">logged in</a> to post a comment.', 'mytheme'), wp_login_url(apply_filters('the_permalink', get_permalink()))) . '</p>',
        'logged_in_as' => '<p class="logged-in-as">' . sprintf(__('Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'mytheme'), admin_url('profile.php'), $user_identity, wp_logout_url(apply_filters('the_permalink', get_permalink()))) . '</p>',
        'comment_notes_before' => '<p class="comment-notes">' . __('Your email address will not be published.', 'mytheme') . '</p>',
        'comment_notes_after' => '',
        'fields' => apply_filters('comment_form_default_fields', array(
            'author' => '<p class="comment-form-author">' . '<label for="author">' . __('Name', 'mytheme') . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) . '<input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30"' . $aria_req . ' /></p>',
            'email' => '<p class="comment-form-email"><label for="email">' . __('Email', 'mytheme') . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) . '<input id="email" name="email" type="text" value="' . esc_attr($commenter['comment_author_email']) . '" size="30"' . $aria_req . ' /></p>',
            'url' => '<p class="comment-form-url"><label for="url">' . __('Website', 'mytheme') . '</label>' . '<input id="url" name="url" type="text" value="' . esc_attr($commenter['comment_author_url']) . '" size="30" /></p>'
        ))
    ));
    ?>
</div>