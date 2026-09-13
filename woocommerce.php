<?php
/**
 * WooCommerce Wrapper Template
 */

get_header(); ?>

<?php didbarttar_breadcrumb(); ?>

<section class="woocommerce-section section">
    <div class="container">
        <?php woocommerce_content(); ?>
    </div>
</section>

<?php get_footer(); ?>