<?php // 404.php
get_header(); ?>
<div class="container" style="padding:120px 24px;text-align:center;">
    <h1 style="font-size:120px;color:var(--color-orange);">404</h1>
    <h2>صفحه یافت نشد</h2>
    <p style="margin:20px 0 32px;">متأسفانه صفحه مورد نظر شما وجود ندارد.</p>
    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">بازگشت به خانه</a>
</div>
<?php get_footer(); ?>