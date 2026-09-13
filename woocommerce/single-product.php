<?php
/**
 * Single Product Template - دید برتر
 */

get_header();

while (have_posts()) : the_post();
    global $product;
    ?>
    
    <?php didbarttar_breadcrumb(); ?>
    
    <section class="wc-single section">
        <div class="container">
            <div class="wc-single-grid" data-reveal="up">
                
                <!-- گالری تصاویر -->
                <div class="wc-single-gallery">
                    <?php
                    if (has_post_thumbnail()) {
                        $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
                        echo '<div class="wc-main-image">';
                        echo '<a href="' . esc_url($image[0]) . '" class="wc-image-zoom" data-lightbox="product">';
                        the_post_thumbnail('large', ['loading' => 'eager']);
                        echo '</a>';
                        echo '</div>';
                    }
                    
                    // Thumbnails
                    $attachment_ids = $product->get_gallery_image_ids();
                    if ($attachment_ids) {
                        echo '<div class="wc-thumbnails">';
                        foreach ($attachment_ids as $id) {
                            $thumb = wp_get_attachment_image_src($id, 'thumbnail');
                            $full = wp_get_attachment_image_src($id, 'large');
                            echo '<a href="' . esc_url($full[0]) . '" class="wc-thumb" data-lightbox="product">';
                            echo wp_get_attachment_image($id, 'thumbnail');
                            echo '</a>';
                        }
                        echo '</div>';
                    }
                    ?>
                </div>

                <!-- اطلاعات محصول -->
                <div class="wc-single-info">
                    <?php
                    // دسته‌بندی
                    $categories = wc_get_product_category_list($product->get_id(), ', ');
                    if ($categories) {
                        echo '<div class="wc-categories">' . $categories . '</div>';
                    }
                    
                    // عنوان
                    echo '<h1 class="wc-title">' . get_the_title() . '</h1>';
                    
                    // SKU
                    if ($product->get_sku()) {
                        echo '<div class="wc-sku">کد: <span>' . esc_html($product->get_sku()) . '</span></div>';
                    }
                    
                    // امتیاز
                    if ($product->get_average_rating() > 0) {
                        echo '<div class="wc-rating">';
                        echo wc_get_rating_html($product->get_average_rating(), $product->get_review_count());
                        echo '<span class="wc-review-count">(' . $product->get_review_count() . ' نظر)</span>';
                        echo '</div>';
                    }
                    
                    // قیمت
                    echo '<div class="wc-price">' . $product->get_price_html() . '</div>';
                    
                    // توضیح کوتاه
                    echo '<div class="wc-short-description">' . apply_filters('woocommerce_short_description', $product->get_short_description()) . '</div>';
                    
                    // فرم افزودن به سبد
                    woocommerce_template_single_add_to_cart();
                    
                    // Meta
                    echo '<div class="wc-product-meta">';
                    echo '<div class="wc-meta-item"><strong>دسته:</strong> ' . wc_get_product_category_list($product->get_id()) . '</div>';
                    if ($product->get_tag_ids()) {
                        echo '<div class="wc-meta-item"><strong>برچسب:</strong> ' . wc_get_product_tag_list($product->get_id()) . '</div>';
                    }
                    echo '</div>';
                    
                    // Trust Badges (از هوک)
                    do_action('woocommerce_single_product_summary', 35);
                    ?>
                </div>
            </div>

            <!-- Tabs -->
            <div class="wc-tabs-wrapper" data-reveal="up">
                <?php
                woocommerce_output_product_data_tabs();
                ?>
            </div>

            <!-- محصولات مرتبط -->
            <?php woocommerce_output_related_products(); ?>
            
            <!-- محصولات اخیر -->
            <?php
            echo '<div class="wc-recent-products">';
            echo '<h2 class="section-title">محصولات اخیر</h2>';
            echo do_shortcode('[recent_products per_page="3" columns="3"]');
            echo '</div>';
            ?>
        </div>
    </section>
    
    <?php
endwhile;

get_footer();