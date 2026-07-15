<?php
/**
 * 1TRIPWISER TRIBE — Community Forum
 * ------------------------------------------------------------------
 * Self-contained module: custom post type (forum_topic), category
 * taxonomy, reply moderation, likes, view counts, pinned/solved flags,
 * trending + leaderboard queries, and the frontend AJAX handlers.
 *
 * Included from functions.php.
 *
 * @package my-theme
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/* =============================================================
 * 1. POST TYPE + TAXONOMY
 * ============================================================= */
function tw_forum_register() {

    register_taxonomy( 'forum_category', array( 'forum_topic' ), array(
        'labels' => array(
            'name'          => __( 'Tribe Categories', 'mytheme' ),
            'singular_name' => __( 'Tribe Category', 'mytheme' ),
            'add_new_item'  => __( 'Add New Category', 'mytheme' ),
            'edit_item'     => __( 'Edit Category', 'mytheme' ),
            'search_items'  => __( 'Search Categories', 'mytheme' ),
        ),
        'public'            => true,
        'hierarchical'      => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'rewrite'           => array( 'slug' => 'tribe-category' ),
    ) );

    register_post_type( 'forum_topic', array(
        'labels' => array(
            'name'          => __( 'Tribe Topics', 'mytheme' ),
            'singular_name' => __( 'Tribe Topic', 'mytheme' ),
            'add_new_item'  => __( 'Add New Topic', 'mytheme' ),
            'edit_item'     => __( 'Edit Topic', 'mytheme' ),
            'new_item'      => __( 'New Topic', 'mytheme' ),
            'view_item'     => __( 'View Topic', 'mytheme' ),
            'search_items'  => __( 'Search Topics', 'mytheme' ),
            'menu_name'     => __( 'Tribe Forum', 'mytheme' ),
        ),
        'public'        => true,
        'has_archive'   => 'tribe',
        'menu_icon'     => 'dashicons-groups',
        'rewrite'       => array( 'slug' => 'tribe', 'with_front' => false ),
        'show_in_rest'  => true,
        'supports'      => array( 'title', 'editor', 'author', 'comments', 'thumbnail' ),
        'taxonomies'    => array( 'forum_category' ),
    ) );
}
add_action( 'init', 'tw_forum_register' );

/* Flush rewrite rules once after the CPT is registered so /tribe/ URLs resolve.
   Bump the version string to force a re-flush after future rewrite changes. */
function tw_forum_maybe_flush() {
    if ( get_option( 'tw_forum_rewrites_v' ) !== '2' ) {
        flush_rewrite_rules( false );
        update_option( 'tw_forum_rewrites_v', '2' );
    }
}
add_action( 'init', 'tw_forum_maybe_flush', 99 );

/* Seed default categories once (idempotent) */
function tw_forum_seed_categories() {
    if ( ! taxonomy_exists( 'forum_category' ) ) { return; }
    $defaults = array(
        'general'        => array( 'Trip Talk',        '<i class="fi-rr-comment" aria-hidden="true"></i>', 'General travel chat, intros and anything goes.' ),
        'destinations'   => array( 'Destinations',     '<i class="fi-rr-map" aria-hidden="true"></i>', 'Place-specific questions, tips and hidden gems.' ),
        'planning'       => array( 'Trip Planning',    '<i class="fi-rr-location-crosshairs" aria-hidden="true"></i>', 'Itineraries, budgets, visas and logistics.' ),
        'gear'           => array( 'Gear & Packing',   '<i class="fi-rr-backpack" aria-hidden="true"></i>', 'What to pack, gear reviews and travel hacks.' ),
        'group-trips'    => array( 'Group Trips',      '<i class="fi-rr-users" aria-hidden="true"></i>', 'Find travel buddies and discuss group tours.' ),
        'experiences'    => array( 'Trip Stories',     '<i class="fi-rr-camera" aria-hidden="true"></i>', 'Share your journeys, photos and memories.' ),
    );
    foreach ( $defaults as $slug => $data ) {
        if ( ! term_exists( $slug, 'forum_category' ) ) {
            $term = wp_insert_term( $data[0], 'forum_category', array( 'slug' => $slug, 'description' => $data[2] ) );
            if ( ! is_wp_error( $term ) ) {
                update_term_meta( $term['term_id'], 'tw_cat_icon', $data[1] );
            }
        }
    }
}
add_action( 'init', 'tw_forum_seed_categories', 20 );

/* =============================================================
 * 2. REPLY MODERATION — hold forum replies for admin approval
 * ============================================================= */
function tw_forum_hold_replies( $approved, $commentdata ) {
    if ( ! empty( $commentdata['comment_post_ID'] ) ) {
        $post = get_post( (int) $commentdata['comment_post_ID'] );
        if ( $post && $post->post_type === 'forum_topic' ) {
            return 0; // 0 = pending moderation
        }
    }
    return $approved;
}
add_filter( 'pre_comment_approved', 'tw_forum_hold_replies', 10, 2 );

/* =============================================================
 * 3. VIEW COUNTER
 * ============================================================= */
function tw_forum_get_views( $post_id ) {
    return (int) get_post_meta( $post_id, '_tw_topic_views', true );
}
function tw_forum_bump_views( $post_id ) {
    // Skip counting the topic author's own views + obvious bots
    if ( get_current_user_id() && (int) get_post_field( 'post_author', $post_id ) === get_current_user_id() ) {
        return;
    }
    $views = tw_forum_get_views( $post_id ) + 1;
    update_post_meta( $post_id, '_tw_topic_views', $views );
}

/* =============================================================
 * 4. LIKE HELPERS
 * ============================================================= */
function tw_forum_topic_like_count( $post_id ) {
    return (int) get_post_meta( $post_id, '_tw_topic_likes', true );
}
function tw_forum_user_liked_topic( $post_id, $user_id = null ) {
    $user_id = $user_id ?: get_current_user_id();
    if ( ! $user_id ) { return false; }
    $liked = get_post_meta( $post_id, '_tw_topic_liked_by', true );
    return is_array( $liked ) && in_array( $user_id, $liked, true );
}
function tw_forum_reply_like_count( $comment_id ) {
    return (int) get_comment_meta( $comment_id, '_tw_reply_likes', true );
}
function tw_forum_user_liked_reply( $comment_id, $user_id = null ) {
    $user_id = $user_id ?: get_current_user_id();
    if ( ! $user_id ) { return false; }
    $liked = get_comment_meta( $comment_id, '_tw_reply_liked_by', true );
    return is_array( $liked ) && in_array( $user_id, $liked, true );
}

/* =============================================================
 * 5. SOLVED / PINNED FLAGS
 * ============================================================= */
function tw_forum_is_pinned( $post_id ) { return (bool) get_post_meta( $post_id, '_tw_topic_pinned', true ); }
function tw_forum_is_solved( $post_id ) { return (bool) get_post_meta( $post_id, '_tw_topic_solved', true ); }

/* =============================================================
 * 6. STATS, TRENDING + LEADERBOARD QUERIES
 * ============================================================= */
function tw_forum_reply_count( $post_id ) {
    return (int) get_comments( array(
        'post_id' => $post_id,
        'status'  => 'approve',
        'count'   => true,
        'type'    => 'comment',
    ) );
}

/** Trending topics: weighted score of likes, replies and views (recent activity first). */
function tw_forum_trending( $limit = 5 ) {
    $q = new WP_Query( array(
        'post_type'      => 'forum_topic',
        'post_status'    => 'publish',
        'posts_per_page' => 30,
        'no_found_rows'  => true,
    ) );
    $scored = array();
    foreach ( $q->posts as $p ) {
        $likes   = tw_forum_topic_like_count( $p->ID );
        $replies = tw_forum_reply_count( $p->ID );
        $views   = tw_forum_get_views( $p->ID );
        $scored[] = array(
            'post'  => $p,
            'score' => ( $likes * 3 ) + ( $replies * 2 ) + $views,
        );
    }
    wp_reset_postdata();
    usort( $scored, function ( $a, $b ) { return $b['score'] <=> $a['score']; } );
    return array_slice( wp_list_pluck( $scored, 'post' ), 0, $limit );
}

/** Leaderboard: members ranked by (topics started * 2 + approved replies). */
function tw_forum_leaderboard( $limit = 5 ) {
    global $wpdb;

    // Topics started per author
    $topic_counts = $wpdb->get_results(
        "SELECT post_author AS uid, COUNT(*) AS c
         FROM {$wpdb->posts}
         WHERE post_type = 'forum_topic' AND post_status = 'publish'
         GROUP BY post_author",
        OBJECT_K
    );

    // Approved replies per user on forum topics
    $reply_counts = $wpdb->get_results(
        "SELECT c.user_id AS uid, COUNT(*) AS c
         FROM {$wpdb->comments} c
         INNER JOIN {$wpdb->posts} p ON p.ID = c.comment_post_ID
         WHERE p.post_type = 'forum_topic'
           AND c.comment_approved = '1'
           AND c.user_id > 0
         GROUP BY c.user_id",
        OBJECT_K
    );

    $scores = array();
    foreach ( $topic_counts as $uid => $row ) {
        $scores[ $uid ] = ( (int) $row->c * 2 );
    }
    foreach ( $reply_counts as $uid => $row ) {
        $scores[ $uid ] = ( $scores[ $uid ] ?? 0 ) + (int) $row->c;
    }
    arsort( $scores );
    $scores = array_slice( $scores, 0, $limit, true );

    $out = array();
    foreach ( $scores as $uid => $score ) {
        $u = get_userdata( $uid );
        if ( ! $u ) { continue; }
        $out[] = array(
            'user'    => $u,
            'score'   => $score,
            'topics'  => isset( $topic_counts[ $uid ] ) ? (int) $topic_counts[ $uid ]->c : 0,
            'replies' => isset( $reply_counts[ $uid ] ) ? (int) $reply_counts[ $uid ]->c : 0,
        );
    }
    return $out;
}

/* =============================================================
 * 7. AJAX — CREATE TOPIC
 * ============================================================= */
function tw_forum_ajax_new_topic() {
    if ( ! is_user_logged_in() ) {
        wp_send_json_error( array( 'message' => 'Please log in to start a discussion.' ) );
    }
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'tw_forum_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Security check failed. Please refresh and try again.' ) );
    }

    $title   = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
    $content = isset( $_POST['content'] ) ? wp_kses_post( wp_unslash( $_POST['content'] ) ) : '';
    $cat_id  = isset( $_POST['category'] ) ? absint( $_POST['category'] ) : 0;

    if ( strlen( $title ) < 5 ) {
        wp_send_json_error( array( 'message' => 'Please give your topic a clearer title (at least 5 characters).' ) );
    }
    if ( strlen( wp_strip_all_tags( $content ) ) < 10 ) {
        wp_send_json_error( array( 'message' => 'Please add a bit more detail to your post.' ) );
    }

    $post_id = wp_insert_post( array(
        'post_type'    => 'forum_topic',
        'post_title'   => $title,
        'post_content' => $content,
        'post_status'  => 'publish',          // topics post instantly; replies are moderated
        'post_author'  => get_current_user_id(),
        'comment_status'=> 'open',
    ), true );

    if ( is_wp_error( $post_id ) ) {
        wp_send_json_error( array( 'message' => 'Could not create your topic. Please try again.' ) );
    }

    if ( $cat_id && term_exists( $cat_id, 'forum_category' ) ) {
        wp_set_object_terms( $post_id, array( $cat_id ), 'forum_category' );
    }

    wp_send_json_success( array(
        'message'  => 'Your topic is live!',
        'redirect' => get_permalink( $post_id ),
    ) );
}
add_action( 'wp_ajax_tw_forum_new_topic', 'tw_forum_ajax_new_topic' );
add_action( 'wp_ajax_nopriv_tw_forum_new_topic', 'tw_forum_ajax_new_topic' );

/* =============================================================
 * 8. AJAX — LIKE TOPIC / REPLY (toggle)
 * ============================================================= */
function tw_forum_ajax_like() {
    if ( ! is_user_logged_in() ) {
        wp_send_json_error( array( 'message' => 'Log in to like posts.' ) );
    }
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'tw_forum_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Security check failed.' ) );
    }

    $type = isset( $_POST['type'] ) ? sanitize_key( $_POST['type'] ) : 'topic';
    $id   = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
    $uid  = get_current_user_id();
    if ( ! $id ) { wp_send_json_error( array( 'message' => 'Invalid item.' ) ); }

    if ( $type === 'reply' ) {
        $liked = get_comment_meta( $id, '_tw_reply_liked_by', true );
        $liked = is_array( $liked ) ? $liked : array();
        $pos   = array_search( $uid, $liked, true );
        if ( $pos !== false ) { unset( $liked[ $pos ] ); $liked = array_values( $liked ); $is_liked = false; }
        else { $liked[] = $uid; $is_liked = true; }
        update_comment_meta( $id, '_tw_reply_liked_by', $liked );
        update_comment_meta( $id, '_tw_reply_likes', count( $liked ) );
        wp_send_json_success( array( 'liked' => $is_liked, 'count' => count( $liked ) ) );
    } else {
        $liked = get_post_meta( $id, '_tw_topic_liked_by', true );
        $liked = is_array( $liked ) ? $liked : array();
        $pos   = array_search( $uid, $liked, true );
        if ( $pos !== false ) { unset( $liked[ $pos ] ); $liked = array_values( $liked ); $is_liked = false; }
        else { $liked[] = $uid; $is_liked = true; }
        update_post_meta( $id, '_tw_topic_liked_by', $liked );
        update_post_meta( $id, '_tw_topic_likes', count( $liked ) );
        wp_send_json_success( array( 'liked' => $is_liked, 'count' => count( $liked ) ) );
    }
}
add_action( 'wp_ajax_tw_forum_like', 'tw_forum_ajax_like' );

/* =============================================================
 * 9. AJAX — MARK SOLVED (topic author or admin only)
 * ============================================================= */
function tw_forum_ajax_toggle_solved() {
    if ( ! is_user_logged_in() ) { wp_send_json_error( array( 'message' => 'Not allowed.' ) ); }
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'tw_forum_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Security check failed.' ) );
    }
    $id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
    if ( ! $id ) { wp_send_json_error( array( 'message' => 'Invalid topic.' ) ); }

    $can = ( (int) get_post_field( 'post_author', $id ) === get_current_user_id() ) || current_user_can( 'moderate_comments' );
    if ( ! $can ) { wp_send_json_error( array( 'message' => 'Only the topic starter can mark this solved.' ) ); }

    $now = ! tw_forum_is_solved( $id );
    update_post_meta( $id, '_tw_topic_solved', $now ? 1 : 0 );
    wp_send_json_success( array( 'solved' => $now ) );
}
add_action( 'wp_ajax_tw_forum_toggle_solved', 'tw_forum_ajax_toggle_solved' );

/* =============================================================
 * 10. ADMIN — pinned / solved meta box + columns
 * ============================================================= */
function tw_forum_meta_box() {
    add_meta_box( 'tw_forum_flags', 'Tribe Flags', 'tw_forum_meta_box_render', 'forum_topic', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'tw_forum_meta_box' );

function tw_forum_meta_box_render( $post ) {
    wp_nonce_field( 'tw_forum_flags_save', 'tw_forum_flags_nonce' );
    $pinned = tw_forum_is_pinned( $post->ID );
    $solved = tw_forum_is_solved( $post->ID );
    echo '<p><label><input type="checkbox" name="tw_topic_pinned" value="1" ' . checked( $pinned, true, false ) . '> <i class="fi-rr-thumbtack" aria-hidden="true"></i> Pin / feature this topic</label></p>';
    echo '<p><label><input type="checkbox" name="tw_topic_solved" value="1" ' . checked( $solved, true, false ) . '> <i class="fi-rr-check-circle" aria-hidden="true"></i> Mark as solved</label></p>';
    echo '<p style="color:#666;font-size:12px">Views: ' . esc_html( tw_forum_get_views( $post->ID ) ) . ' · Likes: ' . esc_html( tw_forum_topic_like_count( $post->ID ) ) . '</p>';
}
function tw_forum_meta_box_save( $post_id ) {
    if ( ! isset( $_POST['tw_forum_flags_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tw_forum_flags_nonce'] ) ), 'tw_forum_flags_save' ) ) { return; }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
    if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }
    update_post_meta( $post_id, '_tw_topic_pinned', isset( $_POST['tw_topic_pinned'] ) ? 1 : 0 );
    update_post_meta( $post_id, '_tw_topic_solved', isset( $_POST['tw_topic_solved'] ) ? 1 : 0 );
}
add_action( 'save_post_forum_topic', 'tw_forum_meta_box_save' );

/* =============================================================
 * 11. ENQUEUE forum JS + localized data
 * ============================================================= */
function tw_forum_enqueue() {
    if ( is_singular( 'forum_topic' ) || is_post_type_archive( 'forum_topic' ) || is_tax( 'forum_category' ) ) {
        $path = get_template_directory() . '/assets/js/tw-forum.js';
        $ver  = file_exists( $path ) ? filemtime( $path ) : '1.0';
        wp_enqueue_script( 'tw-forum', get_template_directory_uri() . '/assets/js/tw-forum.js', array(), $ver, true );
        wp_localize_script( 'tw-forum', 'twForum', array(
            'ajax'      => admin_url( 'admin-ajax.php' ),
            'nonce'     => wp_create_nonce( 'tw_forum_nonce' ),
            'loggedIn'  => is_user_logged_in(),
            'loginUrl'  => home_url( '/login/' ),
        ) );
    }
}
add_action( 'wp_enqueue_scripts', 'tw_forum_enqueue' );

/* =============================================================
 * 12. HELPERS for templates
 * ============================================================= */
function tw_forum_category_list() {
    return get_terms( array( 'taxonomy' => 'forum_category', 'hide_empty' => false ) );
}
function tw_forum_cat_icon( $term_id ) {
    $icon = get_term_meta( $term_id, 'tw_cat_icon', true );
    return $icon ?: '<i class="fi-rr-comment" aria-hidden="true"></i>';
}
/** Human "x ago" for a post/comment date. */
function tw_forum_ago( $time ) {
    return human_time_diff( $time, current_time( 'timestamp' ) ) . ' ago';
}
/** URL to a member's tribe profile (their topics). Falls back to the profile page. */
function tw_forum_member_url( $user_id ) {
    return add_query_arg( 'member', $user_id, get_post_type_archive_link( 'forum_topic' ) );
}

/**
 * Render a single reply (comment) for wp_list_comments on the topic page.
 * Matches the Tribe card styling with avatar, meta and a like button.
 */
function tw_forum_render_reply( $comment, $args, $depth ) {
    $cid      = $comment->comment_ID;
    $uid      = (int) $comment->user_id;
    $name     = $uid ? ( get_userdata( $uid )->display_name ?: $comment->comment_author ) : $comment->comment_author;
    $likes    = tw_forum_reply_like_count( $cid );
    $liked    = tw_forum_user_liked_reply( $cid );
    $is_op    = $uid && ( (int) get_post_field( 'post_author', $comment->comment_post_ID ) === $uid );
    ?>
    <li <?php comment_class( 'tribe-reply', $comment ); ?> id="reply-<?php echo esc_attr( $cid ); ?>">
        <div class="tribe-reply-inner">
            <a class="tribe-reply-avatar" href="<?php echo esc_url( $uid ? tw_forum_member_url( $uid ) : '#' ); ?>">
                <?php echo get_avatar( $comment, 42, '', '', array( 'class' => 'tribe-reply-avatar-img' ) ); ?>
            </a>
            <div class="tribe-reply-body">
                <div class="tribe-reply-head">
                    <strong class="tribe-reply-name"><?php echo esc_html( $name ); ?></strong>
                    <?php if ( $is_op ) : ?><span class="tribe-reply-op">Original poster</span><?php endif; ?>
                    <span class="tribe-dot">·</span>
                    <span class="tribe-reply-time"><?php echo esc_html( tw_forum_ago( strtotime( $comment->comment_date_gmt . ' UTC' ) ) ); ?></span>
                </div>
                <div class="tribe-reply-text"><?php comment_text(); ?></div>
                <div class="tribe-reply-actions">
                    <button type="button"
                            class="tribe-like-btn tribe-like-sm <?php echo $liked ? 'is-liked' : ''; ?>"
                            data-type="reply" data-id="<?php echo esc_attr( $cid ); ?>">
                        <span class="tribe-like-icon"><?php echo $liked ? '<i class="fi-rr-heart" aria-hidden="true"></i>' : '<i class="fi-rr-heart" aria-hidden="true"></i>'; ?></span>
                        <span class="tribe-like-count"><?php echo esc_html( $likes ); ?></span>
                    </button>
                    <?php
                    comment_reply_link( array_merge( $args, array(
                        'depth'     => $depth,
                        'max_depth' => $args['max_depth'],
                        'reply_text'=> '↪ Reply',
                        'before'    => '<span class="tribe-reply-link">',
                        'after'     => '</span>',
                    ) ) );
                    ?>
                </div>
            </div>
        </div>
    <?php
    // note: no closing </li> — WordPress appends it after nested children
}
