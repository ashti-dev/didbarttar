<?php // index.php, page.php, single.php - همه یکسان
get_header(); ?>
<div class="container" style="padding:120px 24px 80px;max-width:800px;">
    <?php while (have_posts()): the_post(); ?>
        <h1><?php the_title(); ?></h1>
        <div class="entry-content" style="margin-top:32px;"><?php the_content(); ?></div>
    <?php endwhile; ?>
</div>
<?php get_footer(); ?>