<?php
/**
 * Shop / Product Archive Template
 */

get_header();
?>

<?php didbarttar_breadcrumb(); ?>

<section class="wc-archive section">
    <div class="container">
        <header class="wc-archive-header" data-reveal="up">
            <span class="section-label">فروشگاه</span>
            <h1 class="wc-archive-title"><?php woocommerce_page_title(); ?></h1>
            <?php if (get_the_archive_description()) : ?>
                <p class="wc-archive-desc"><?php echo get_the_archive_description(); ?></p>
            <?php endif; ?>
        </header>

        <div class="wc-archive-layout">
            <!-- Sidebar -->
            <aside class="wc-sidebar" data-reveal="left">
                <?php dynamic_sidebar('woocommerce-sidebar'); ?>
            </aside>

            <!-- Content -->
            <div class="wc-content" data-reveal="right">
                <div class="wc-toolbar">
                    <div class="wc-result-count">
                        <?php woocommerce_result_count(); ?>
                    </div>
                    <div class="wc-ordering">
                        <?php woocommerce_catalog_ordering(); ?>
                    </div>
                </div>

                <?php
                if (woocommerce_product_loop()) {
                    woocommerce_product_loop_start();
                    while (have_posts()) : the_post();
                        wc_get_template_part('content', 'product');
                    endwhile;
                    woocommerce_product_loop_end();
                    
                    woocommerce_pagination();
                } else {
                    do_action('woocommerce_no_products_found');
                }
                ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>