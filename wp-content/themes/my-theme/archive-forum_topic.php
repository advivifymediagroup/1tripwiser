<?php
/**
 * Tribe Forum — archive / home, category view, and member-filtered view.
 * Also used by taxonomy-forum_category.php.
 *
 * @package my-theme
 */
get_header();

/* ---- Context detection ---- */
$tw_is_cat    = is_tax( 'forum_category' );
$tw_cat_term  = $tw_is_cat ? get_queried_object() : null;
$tw_member_id = isset( $_GET['member'] ) ? absint( $_GET['member'] ) : 0;
$tw_member    = $tw_member_id ? get_userdata( $tw_member_id ) : null;
$tw_paged     = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );

/* ---- Headline figures ---- */
$tw_total_topics  = (int) wp_count_posts( 'forum_topic' )->publish;
$tw_total_replies = (int) get_comments( array(
    'status' => 'approve',
    'count'  => true,
    'post_type' => 'forum_topic',
) );
$tw_total_members = (int) count_users()['total_users'];

/* ---- Topic-card renderer (local) ---- */
if ( ! function_exists( 'tw_forum_card' ) ) :
function tw_forum_card( $post ) {
    $id       = $post->ID;
    $author   = get_userdata( $post->post_author );
    $a_name   = $author ? ( $author->display_name ?: $author->user_login ) : 'Member';
    $avatar   = get_avatar( $post->post_author, 44, '', '', array( 'class' => 'tribe-card-avatar-img' ) );
    $cats     = get_the_terms( $id, 'forum_category' );
    $cat      = ( $cats && ! is_wp_error( $cats ) ) ? $cats[0] : null;
    $replies  = tw_forum_reply_count( $id );
    $likes    = tw_forum_topic_like_count( $id );
    $views    = tw_forum_get_views( $id );
    $pinned   = tw_forum_is_pinned( $id );
    $solved   = tw_forum_is_solved( $id );
    $excerpt  = wp_trim_words( wp_strip_all_tags( $post->post_content ), 26, '…' );
    ?>
    <article class="tribe-topic <?php echo $pinned ? 'is-pinned' : ''; ?>">
        <a class="tribe-topic-avatar" href="<?php echo esc_url( tw_forum_member_url( $post->post_author ) ); ?>" aria-label="<?php echo esc_attr( $a_name ); ?>">
            <?php echo $avatar; ?>
        </a>
        <div class="tribe-topic-body">
            <div class="tribe-topic-meta-top">
                <?php if ( $cat ) : ?>
                    <a class="tribe-chip-cat" href="<?php echo esc_url( get_term_link( $cat ) ); ?>">
                        <?php echo esc_html( tw_forum_cat_icon( $cat->term_id ) . ' ' . $cat->name ); ?>
                    </a>
                <?php endif; ?>
                <?php if ( $pinned ) : ?><span class="tribe-flag tribe-flag-pin">📌 Pinned</span><?php endif; ?>
                <?php if ( $solved ) : ?><span class="tribe-flag tribe-flag-solved">✅ Solved</span><?php endif; ?>
            </div>
            <h2 class="tribe-topic-title">
                <a href="<?php echo esc_url( get_permalink( $id ) ); ?>"><?php echo esc_html( get_the_title( $id ) ); ?></a>
            </h2>
            <p class="tribe-topic-excerpt"><?php echo esc_html( $excerpt ); ?></p>
            <div class="tribe-topic-meta-bottom">
                <span class="tribe-topic-author">by <strong><?php echo esc_html( $a_name ); ?></strong></span>
                <span class="tribe-dot">·</span>
                <span><?php echo esc_html( tw_forum_ago( get_post_time( 'U', false, $id ) ) ); ?></span>
            </div>
        </div>
        <div class="tribe-topic-stats">
            <span class="tribe-stat" title="Replies">💬 <?php echo esc_html( $replies ); ?></span>
            <span class="tribe-stat" title="Likes">❤️ <?php echo esc_html( $likes ); ?></span>
            <span class="tribe-stat" title="Views">👁️ <?php echo esc_html( $views ); ?></span>
        </div>
    </article>
    <?php
}
endif;

/* ---- Build the query ---- */
$tw_args = array(
    'post_type'      => 'forum_topic',
    'post_status'    => 'publish',
    'posts_per_page' => 12,
    'paged'          => $tw_paged,
);
if ( $tw_is_cat && $tw_cat_term ) {
    $tw_args['tax_query'] = array( array(
        'taxonomy' => 'forum_category',
        'field'    => 'term_id',
        'terms'    => $tw_cat_term->term_id,
    ) );
}
if ( $tw_member_id ) {
    $tw_args['author'] = $tw_member_id;
}
$tw_q = new WP_Query( $tw_args );

/* Pinned topics shown on top of the main (unfiltered) view only */
$tw_pinned = array();
if ( ! $tw_is_cat && ! $tw_member_id && $tw_paged === 1 ) {
    $tw_pinned_q = new WP_Query( array(
        'post_type'      => 'forum_topic',
        'post_status'    => 'publish',
        'posts_per_page' => 5,
        'meta_key'       => '_tw_topic_pinned',
        'meta_value'     => '1',
    ) );
    $tw_pinned = $tw_pinned_q->posts;
    wp_reset_postdata();
}
$tw_pinned_ids = wp_list_pluck( $tw_pinned, 'ID' );
?>

<main class="main-content tribe-page">

    <!-- ═══════════ TRIBE HERO ═══════════ -->
    <section class="tribe-hero">
        <div class="tribe-hero-overlay" aria-hidden="true"></div>
        <div class="container tribe-hero-inner">
            <span class="tribe-hero-kicker">Join the conversation</span>
            <h1 class="tribe-hero-title">1TRIPWISER <span>TRIBE</span></h1>
            <p class="tribe-hero-sub">
                <?php
                if ( $tw_is_cat && $tw_cat_term ) {
                    echo esc_html( tw_forum_cat_icon( $tw_cat_term->term_id ) . ' ' . $tw_cat_term->name );
                } elseif ( $tw_member ) {
                    echo 'Topics by ' . esc_html( $tw_member->display_name ?: $tw_member->user_login );
                } else {
                    echo 'Where 300K+ travellers swap tips, plan trips together and share the road.';
                }
                ?>
            </p>

            <div class="tribe-hero-stats">
                <div class="tribe-hstat"><strong><?php echo esc_html( number_format_i18n( $tw_total_members ) ); ?></strong><span>Members</span></div>
                <div class="tribe-hstat"><strong><?php echo esc_html( number_format_i18n( $tw_total_topics ) ); ?></strong><span>Topics</span></div>
                <div class="tribe-hstat"><strong><?php echo esc_html( number_format_i18n( $tw_total_replies ) ); ?></strong><span>Replies</span></div>
            </div>

            <div class="tribe-hero-actions">
                <button type="button" class="tribe-start-btn" id="tribe-open-new">
                    <span aria-hidden="true">✍️</span> Start a Discussion
                </button>
                <a class="tribe-ig-btn" href="https://www.instagram.com/1tripwiser_tribe/" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-instagram" aria-hidden="true"></i> Follow on Instagram
                </a>
            </div>
        </div>
    </section>

    <div class="container tribe-layout">

        <!-- ═══════════ MAIN COLUMN ═══════════ -->
        <div class="tribe-main">

            <!-- Category chips -->
            <nav class="tribe-cats" aria-label="Forum categories">
                <a class="tribe-cat-chip <?php echo ( ! $tw_is_cat && ! $tw_member_id ) ? 'active' : ''; ?>"
                   href="<?php echo esc_url( get_post_type_archive_link( 'forum_topic' ) ); ?>">🌐 All</a>
                <?php foreach ( tw_forum_category_list() as $term ) : ?>
                    <a class="tribe-cat-chip <?php echo ( $tw_is_cat && $tw_cat_term && $tw_cat_term->term_id === $term->term_id ) ? 'active' : ''; ?>"
                       href="<?php echo esc_url( get_term_link( $term ) ); ?>">
                        <?php echo esc_html( tw_forum_cat_icon( $term->term_id ) . ' ' . $term->name ); ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <?php if ( $tw_member ) : ?>
                <div class="tribe-member-banner">
                    <?php echo get_avatar( $tw_member->ID, 52, '', '', array( 'class' => 'tribe-member-banner-avatar' ) ); ?>
                    <div>
                        <strong><?php echo esc_html( $tw_member->display_name ?: $tw_member->user_login ); ?></strong>
                        <span>Member since <?php echo esc_html( date_i18n( 'M Y', strtotime( $tw_member->user_registered ) ) ); ?></span>
                    </div>
                    <a class="tribe-member-back" href="<?php echo esc_url( get_post_type_archive_link( 'forum_topic' ) ); ?>">← All topics</a>
                </div>
            <?php endif; ?>

            <!-- Pinned topics -->
            <?php if ( $tw_pinned ) : ?>
                <div class="tribe-topic-list tribe-pinned-list">
                    <?php foreach ( $tw_pinned as $p ) { tw_forum_card( $p ); } ?>
                </div>
            <?php endif; ?>

            <!-- Topic list -->
            <?php if ( $tw_q->have_posts() ) : ?>
                <div class="tribe-topic-list">
                    <?php
                    while ( $tw_q->have_posts() ) :
                        $tw_q->the_post();
                        if ( in_array( get_the_ID(), $tw_pinned_ids, true ) ) { continue; }
                        tw_forum_card( get_post() );
                    endwhile;
                    ?>
                </div>

                <div class="tribe-pagination">
                    <?php
                    echo paginate_links( array(
                        'total'     => $tw_q->max_num_pages,
                        'current'   => $tw_paged,
                        'mid_size'  => 1,
                        'prev_text' => '← Prev',
                        'next_text' => 'Next →',
                    ) );
                    ?>
                </div>
                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <div class="tribe-empty">
                    <div class="tribe-empty-icon">🗺️</div>
                    <h3>No discussions here yet</h3>
                    <p>Be the first to break the ice — start a topic and get the conversation going.</p>
                    <button type="button" class="tribe-start-btn" id="tribe-open-new-2">✍️ Start a Discussion</button>
                </div>
            <?php endif; ?>
        </div>

        <!-- ═══════════ SIDEBAR ═══════════ -->
        <aside class="tribe-sidebar">

            <!-- Follow on Instagram -->
            <div class="tribe-widget tribe-ig-widget">
                <h3 class="tribe-widget-title">📸 Tribe on Instagram</h3>
                <p class="tribe-ig-widget-text">Daily travel inspo, member stories & trip drops.</p>
                <a class="tribe-ig-btn tribe-ig-btn--block" href="https://www.instagram.com/1tripwiser_tribe/" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-instagram" aria-hidden="true"></i> @1tripwiser_tribe
                </a>
            </div>

            <!-- Trending -->
            <div class="tribe-widget">
                <h3 class="tribe-widget-title">🔥 Trending Now</h3>
                <?php $tw_trending = tw_forum_trending( 5 ); if ( $tw_trending ) : ?>
                    <ul class="tribe-trending">
                        <?php foreach ( $tw_trending as $i => $t ) : ?>
                            <li>
                                <span class="tribe-trend-rank"><?php echo (int) ( $i + 1 ); ?></span>
                                <a href="<?php echo esc_url( get_permalink( $t->ID ) ); ?>">
                                    <span class="tribe-trend-title"><?php echo esc_html( get_the_title( $t->ID ) ); ?></span>
                                    <span class="tribe-trend-meta">💬 <?php echo esc_html( tw_forum_reply_count( $t->ID ) ); ?> · ❤️ <?php echo esc_html( tw_forum_topic_like_count( $t->ID ) ); ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p class="tribe-widget-empty">Nothing trending yet — start a discussion!</p>
                <?php endif; ?>
            </div>

            <!-- Leaderboard -->
            <div class="tribe-widget">
                <h3 class="tribe-widget-title">🏆 Top Members</h3>
                <?php $tw_board = tw_forum_leaderboard( 5 ); if ( $tw_board ) : ?>
                    <ul class="tribe-leaderboard">
                        <?php foreach ( $tw_board as $i => $row ) :
                            $m = $row['user']; ?>
                            <li>
                                <span class="tribe-board-rank tribe-board-rank--<?php echo (int) ( $i + 1 ); ?>"><?php echo (int) ( $i + 1 ); ?></span>
                                <a class="tribe-board-user" href="<?php echo esc_url( tw_forum_member_url( $m->ID ) ); ?>">
                                    <?php echo get_avatar( $m->ID, 34, '', '', array( 'class' => 'tribe-board-avatar' ) ); ?>
                                    <span class="tribe-board-name"><?php echo esc_html( $m->display_name ?: $m->user_login ); ?></span>
                                </a>
                                <span class="tribe-board-score"><?php echo esc_html( $row['score'] ); ?> pts</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p class="tribe-widget-empty">No members ranked yet.</p>
                <?php endif; ?>
            </div>

            <!-- Categories list -->
            <div class="tribe-widget">
                <h3 class="tribe-widget-title">📚 Categories</h3>
                <ul class="tribe-cat-links">
                    <?php foreach ( tw_forum_category_list() as $term ) : ?>
                        <li>
                            <a href="<?php echo esc_url( get_term_link( $term ) ); ?>">
                                <span><?php echo esc_html( tw_forum_cat_icon( $term->term_id ) . ' ' . $term->name ); ?></span>
                                <span class="tribe-cat-count"><?php echo esc_html( $term->count ); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>
    </div>
</main>

<!-- ═══════════ NEW TOPIC MODAL ═══════════ -->
<div class="tribe-modal" id="tribe-modal" aria-hidden="true">
    <div class="tribe-modal-backdrop" data-tribe-close></div>
    <div class="tribe-modal-box" role="dialog" aria-modal="true" aria-labelledby="tribe-modal-title">
        <button type="button" class="tribe-modal-close" data-tribe-close aria-label="Close">✕</button>
        <h2 class="tribe-modal-title" id="tribe-modal-title">Start a Discussion</h2>

        <?php if ( is_user_logged_in() ) : ?>
        <form id="tribe-new-form" class="tribe-form">
            <div class="tribe-field">
                <label for="tribe-f-title">Title</label>
                <input type="text" id="tribe-f-title" name="title" maxlength="140" placeholder="What's your question or topic?" required>
            </div>
            <div class="tribe-field">
                <label for="tribe-f-cat">Category</label>
                <select id="tribe-f-cat" name="category">
                    <?php foreach ( tw_forum_category_list() as $term ) : ?>
                        <option value="<?php echo esc_attr( $term->term_id ); ?>"><?php echo esc_html( tw_forum_cat_icon( $term->term_id ) . ' ' . $term->name ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="tribe-field">
                <label for="tribe-f-content">Your message</label>
                <textarea id="tribe-f-content" name="content" rows="6" placeholder="Share the details…" required></textarea>
            </div>
            <div class="tribe-form-msg" id="tribe-form-msg" role="status"></div>
            <div class="tribe-form-actions">
                <button type="button" class="tribe-btn-ghost" data-tribe-close>Cancel</button>
                <button type="submit" class="tribe-btn-primary" id="tribe-submit">Post Topic</button>
            </div>
        </form>
        <?php else : ?>
        <div class="tribe-login-prompt">
            <p>You need to be a Tribe member to start a discussion.</p>
            <div class="tribe-form-actions">
                <a class="tribe-btn-ghost" href="<?php echo esc_url( home_url( '/register/' ) ); ?>">Sign Up Free</a>
                <a class="tribe-btn-primary" href="<?php echo esc_url( home_url( '/login/' ) ); ?>">Log In</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
