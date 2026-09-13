<?php
class Didbarttar_Meta_Boxes {
    public function __construct() {
        add_action('add_meta_boxes', [$this, 'register']);
        add_action('save_post_did_testimonial', [$this, 'save_testimonial']);
    }

    public function register() {
        add_meta_box('did_testimonial_meta', 'اطلاعات نظر مشتری', [$this, 'testimonial_cb'], 'did_testimonial', 'normal', 'high');
    }

    public function testimonial_cb($post) {
        wp_nonce_field('did_testimonial_nonce', 'did_testimonial_nonce');
        $role   = get_post_meta($post->ID, '_did_testimonial_role', true);
        $rating = get_post_meta($post->ID, '_did_testimonial_rating', true) ?: 5;
        echo '<p><label>سمت / عنوان</label><br><input type="text" class="widefat" name="_did_testimonial_role" value="' . esc_attr($role) . '"></p>';
        echo '<p><label>امتیاز (۱ تا ۵)</label><br><input type="number" min="1" max="5" name="_did_testimonial_rating" value="' . esc_attr($rating) . '"></p>';
    }

    public function save_testimonial($post_id) {
        if (!isset($_POST['did_testimonial_nonce']) || !wp_verify_nonce($_POST['did_testimonial_nonce'], 'did_testimonial_nonce')) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;
        if (isset($_POST['_did_testimonial_role']))   update_post_meta($post_id, '_did_testimonial_role', sanitize_text_field($_POST['_did_testimonial_role']));
        if (isset($_POST['_did_testimonial_rating'])) update_post_meta($post_id, '_did_testimonial_rating', min(5, max(1, absint($_POST['_did_testimonial_rating']))));
    }
}
new Didbarttar_Meta_Boxes();