<?php // archive.php
get_header(); ?>
<div class="container" style="padding:120px 24px 80px;">
    <h1><?php post_type_archive_title(); ?></h1>
    <div class="prod-grid" style="margin-top:40px;">
        <?php while (have_posts()): the_post(); get_template_part('template-parts/product-card'); endwhile; ?>
    </div>
</div>
<?php get_footer(); ?>