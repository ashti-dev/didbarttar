<?php
/**
 * Product Card Template - WooCommerce
 */

global $product;
if (!is_a($product, 'WC_Product')) return;
?>

<article <?php post_class('prod-card'); ?> data-reveal="up">
    <div class="prod-image">
        <a href="<?php the_permalink(); ?>">
            <?php
            if (has_post_thumbnail()) {
                the_post_thumbnail('medium_large', ['loading' => 'lazy']);
            } else {
                echo wc_placeholder_img('medium_large');
            }
            ?>
        </a>
        
        <?php
        // Badge
        if ($product->is_on_sale()) {
            echo '<span class="prod-badge badge-sale">تخفیف</span>';
        } elseif ($product->is_featured()) {
            echo '<span class="prod-badge badge-hot">ویژه</span>';
        }
        
        // Quick view button
        echo '<button class="wc-quick-view" data-product-id="' . $product->get_id() . '" aria-label="نمایش سریع">';
        echo '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
        echo '</button>';
        ?>
    </div>
    
    <div class="prod-body">
        <div class="prod-cat">
            <?php echo wc_get_product_category_list($product->get_id(), ', '); ?>
        </div>
        
        <h3 class="prod-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        
        <?php if ($product->get_short_description()) : ?>
            <p class="prod-desc"><?php echo wp_trim_words($product->get_short_description(), 15); ?></p>
        <?php endif; ?>
        
        <div class="prod-footer">
            <div class="prod-price">
                <?php echo $product->get_price_html(); ?>
            </div>
            
            <?php if ($product->is_purchasable() && $product->is_in_stock()) : ?>
                <a href="<?php echo esc_url($product->add_to_cart_url()); ?>" 
                   class="btn btn-primary wc-add-to-cart"
                   data-product_id="<?php echo $product->get_id(); ?>"
                   data-quantity="1">
                    افزودن
                </a>
            <?php else : ?>
                <span class="btn btn-outline" style="opacity:0.6;cursor:not-allowed;">ناموجود</span>
            <?php endif; ?>
        </div>
    </div>
</article>