<?php get_header(); ?>

<?php didbarttar_breadcrumb(); ?>

<div class="container" style="padding:40px 24px 80px;max-width:800px;">
    <?php while (have_posts()): the_post(); ?>
        <h1><?php the_title(); ?></h1>
        <div class="entry-content" style="margin-top:32px;"><?php the_content(); ?></div>
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>