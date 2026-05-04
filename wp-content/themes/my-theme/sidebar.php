<aside class="sidebar">
    <div class="sidebar-widget">
        <h3>About Us</h3>
        <p>Welcome to our travel blog! We share amazing travel experiences, tips, and destination guides to help you plan your next adventure.</p>
    </div>

    <div class="sidebar-widget">
        <h3>Search</h3>
        <?php get_search_form(); ?>
    </div>

    <div class="sidebar-widget">
        <h3>Categories</h3>
        <ul>
            <?php
            $categories = get_categories();
            foreach ($categories as $category) {
                echo '<li><a href="' . get_category_link($category->term_id) . '">' . $category->name . ' (' . $category->count . ')</a></li>';
            }
            ?>
        </ul>
    </div>

    <div class="sidebar-widget">
        <h3>Recent Posts</h3>
        <ul>
            <?php
            $recent_posts = wp_get_recent_posts(array(
                'numberposts' => 5,
                'post_status' => 'publish'
            ));
            foreach ($recent_posts as $post) {
                echo '<li><a href="' . get_permalink($post['ID']) . '">' . $post['post_title'] . '</a></li>';
            }
            wp_reset_postdata();
            ?>
        </ul>
    </div>

    <div class="sidebar-widget">
        <h3>Tags</h3>
        <div class="tag-cloud">
            <?php wp_tag_cloud(); ?>
        </div>
    </div>
</aside>