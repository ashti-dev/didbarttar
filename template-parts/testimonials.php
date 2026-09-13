<section class="section">
    <div class="container">
        <div style="text-align:center;margin-bottom:20px;">
            <span class="section-label" data-reveal="up">نظرات مشتریان</span>
            <h2 class="section-title" style="margin:0 auto;" data-reveal="up" data-delay="100">چه می‌گویند؟</h2>
        </div>
        
        <div class="test-grid">
            <?php
            $tests = new WP_Query(['post_type' => 'did_testimonial', 'posts_per_page' => 3]);
            if ($tests->have_posts()):
                while ($tests->have_posts()): $tests->the_post();
                    $role = get_post_meta(get_the_ID(), '_did_testimonial_role', true);
                    $rating = (int) get_post_meta(get_the_ID(), '_did_testimonial_rating', true) ?: 5;
            ?>
            <div class="test-card" data-reveal="up">
                <span class="test-quote">"</span>
                <div class="test-stars"><?php echo str_repeat('★', $rating) . str_repeat('☆', 5 - $rating); ?></div>
                <p class="test-text"><?php echo wp_trim_words(get_the_content(), 30); ?></p>
                <div class="test-author">
                    <div class="test-avatar"><?php echo mb_substr(get_the_title(), 0, 1); ?></div>
                    <div>
                        <div class="test-name"><?php the_title(); ?></div>
                        <div class="test-role"><?php echo esc_html($role); ?></div>
                    </div>
                </div>
            </div>
            <?php endwhile; wp_reset_postdata(); endif; ?>
        </div>
    </div>
</section>