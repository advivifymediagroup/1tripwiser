<?php
/**
 * Tribe Forum — single topic view (content + replies + reply form).
 *
 * @package my-theme
 */
get_header();

while ( have_posts() ) :
    the_post();
    $tw_id     = get_the_ID();
    tw_forum_bump_views( $tw_id ); // count this view

    $tw_author = get_userdata( get_the_author_meta( 'ID' ) );
    $tw_a_name = $tw_author ? ( $tw_author->display_name ?: $tw_author->user_login ) : 'Member';
    $tw_cats   = get_the_terms( $tw_id, 'forum_category' );
    $tw_cat    = ( $tw_cats && ! is_wp_error( $tw_cats ) ) ? $tw_cats[0] : null;
    $tw_likes  = tw_forum_topic_like_count( $tw_id );
    $tw_liked  = tw_forum_user_liked_topic( $tw_id );
    $tw_views  = tw_forum_get_views( $tw_id );
    $tw_repl   = tw_forum_reply_count( $tw_id );
    $tw_pinned = tw_forum_is_pinned( $tw_id );
    $tw_solved = tw_forum_is_solved( $tw_id );
    $tw_can_solve = is_user_logged_in() && ( (int) get_the_author_meta( 'ID' ) === get_current_user_id() || current_user_can( 'moderate_comments' ) );
    ?>

<main class="main-content tribe-page tribe-single">
    <div class="container tribe-single-wrap">

        <!-- Breadcrumb -->
        <nav class="tribe-crumbs" aria-label="Breadcrumb">
            <a href="<?php echo esc_url( get_post_type_archive_link( 'forum_topic' ) ); ?>"><i class="fi-rr-camping" aria-hidden="true"></i> Tribe</a>
            <?php if ( $tw_cat ) : ?>
                <span>›</span>
                <a href="<?php echo esc_url( get_term_link( $tw_cat ) ); ?>"><?php echo esc_html( $tw_cat->name ); ?></a>
            <?php endif; ?>
        </nav>

        <article class="tribe-post">
            <!-- Header -->
            <header class="tribe-post-head">
                <div class="tribe-post-flags">
                    <?php if ( $tw_cat ) : ?>
                        <a class="tribe-chip-cat" href="<?php echo esc_url( get_term_link( $tw_cat ) ); ?>">
                            <?php echo tw_forum_cat_icon( $tw_cat->term_id ) . ' ' . esc_html( $tw_cat->name ); ?>
                        </a>
                    <?php endif; ?>
                    <?php if ( $tw_pinned ) : ?><span class="tribe-flag tribe-flag-pin"><i class="fi-rr-thumbtack" aria-hidden="true"></i> Pinned</span><?php endif; ?>
                    <span class="tribe-flag tribe-flag-solved <?php echo $tw_solved ? '' : 'is-off'; ?>" id="tribe-solved-badge"><?php echo $tw_solved ? '<i class="fi-rr-check-circle" aria-hidden="true"></i> Solved' : '<i class="fi-rr-checkbox" aria-hidden="true"></i> Unsolved'; ?></span>
                </div>

                <h1 class="tw-h1 tribe-post-title"><?php the_title(); ?></h1>

                <div class="tribe-post-byline">
                    <a class="tribe-byline-user" href="<?php echo esc_url( tw_forum_member_url( get_the_author_meta( 'ID' ) ) ); ?>">
                        <?php echo get_avatar( get_the_author_meta( 'ID' ), 40, '', '', array( 'class' => 'tribe-byline-avatar' ) ); ?>
                        <span>
                            <strong><?php echo esc_html( $tw_a_name ); ?></strong>
                            <small><?php echo esc_html( tw_forum_ago( get_post_time( 'U' ) ) ); ?></small>
                        </span>
                    </a>
                </div>
            </header>

            <!-- Body -->
            <div class="tribe-post-content">
                <?php the_content(); ?>
            </div>

            <!-- Action bar -->
            <div class="tribe-post-actions">
                <button type="button"
                        class="tribe-like-btn <?php echo $tw_liked ? 'is-liked' : ''; ?>"
                        data-type="topic" data-id="<?php echo esc_attr( $tw_id ); ?>">
                    <span class="tribe-like-icon"><?php echo $tw_liked ? '<i class="fi-rr-heart" aria-hidden="true"></i>' : '<i class="fi-rr-heart" aria-hidden="true"></i>'; ?></span>
                    <span class="tribe-like-count"><?php echo esc_html( $tw_likes ); ?></span>
                    <span class="tribe-like-label">Like</span>
                </button>

                <span class="tribe-action-stat"><i class="fi-rr-comment" aria-hidden="true"></i> <?php echo esc_html( $tw_repl ); ?> replies</span>
                <span class="tribe-action-stat"><i class="fi-rr-eye" aria-hidden="true"></i> <?php echo esc_html( $tw_views ); ?> views</span>

                <?php if ( $tw_can_solve ) : ?>
                    <button type="button" class="btn-secondary btn-sm tribe-solve-btn <?php echo $tw_solved ? 'is-solved' : ''; ?>" data-id="<?php echo esc_attr( $tw_id ); ?>">
                        <?php echo $tw_solved ? '<i class="fi-rr-undo" aria-hidden="true"></i> Mark unsolved' : '<i class="fi-rr-check-circle" aria-hidden="true"></i> Mark solved'; ?>
                    </button>
                <?php endif; ?>
            </div>
        </article>

        <!-- ═══════════ REPLIES ═══════════ -->
        <section class="tribe-replies" id="replies">
            <h2 class="tw-h2 tribe-replies-title"><?php echo esc_html( $tw_repl ); ?> <?php echo ( 1 === $tw_repl ) ? 'Reply' : 'Replies'; ?></h2>

            <?php if ( have_comments() ) : ?>
                <ol class="tribe-reply-list">
                    <?php
                    wp_list_comments( array(
                        'type'     => 'comment',
                        'callback' => 'tw_forum_render_reply',
                        'style'    => 'ol',
                    ) );
                    ?>
                </ol>
            <?php else : ?>
                <p class="tribe-no-replies">No replies yet — be the first to chime in.</p>
            <?php endif; ?>

            <!-- Reply form -->
            <div class="tribe-reply-form-wrap" id="reply-form">
                <?php if ( is_user_logged_in() ) : ?>
                    <div class="tribe-mod-note"><i class="fi-rr-memo" aria-hidden="true"></i> Replies are reviewed by a moderator before they appear publicly.</div>
                    <?php
                    comment_form( array(
                        'title_reply'         => 'Add your reply',
                        'title_reply_before'  => '<h3 class="tribe-reply-form-title">',
                        'title_reply_after'   => '</h3>',
                        'class_form'          => 'tribe-reply-form',
                        'comment_field'       => '<div class="tribe-field"><textarea name="comment" id="comment" rows="5" required placeholder="Share your thoughts, tips or answer…"></textarea></div>',
                        'label_submit'        => 'Post Reply',
                        'class_submit'        => 'btn-primary',
                        'comment_notes_before'=> '',
                        'comment_notes_after' => '',
                        'logged_in_as'        => '',
                    ) );
                    ?>
                <?php else : ?>
                    <div class="tribe-login-prompt tribe-login-inline">
                        <p>Join the Tribe to reply to this discussion.</p>
                        <div class="tribe-form-actions">
                            <a class="btn-secondary" href="<?php echo esc_url( home_url( '/register/' ) ); ?>">Sign Up Free</a>
                            <a class="btn-primary" href="<?php echo esc_url( home_url( '/login/' ) ); ?>">Log In</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <a class="tribe-back-link" href="<?php echo esc_url( get_post_type_archive_link( 'forum_topic' ) ); ?>">← Back to all discussions</a>
    </div>
</main>

<?php
endwhile;
get_footer();
