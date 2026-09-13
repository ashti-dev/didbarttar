<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0F1E30">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a href="#main-content" class="skip-link" style="position:absolute;top:-100px;right:0;background:#E8680A;color:#fff;padding:10px 20px;z-index:9999;">پرش به محتوا</a>

<header class="site-header" id="site-header">
    <div class="header-inner">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="logo" aria-label="دید برتر - صفحه اصلی">
            <span class="logo-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/>
                </svg>
            </span>
            <span>دید برتر</span>
        </a>
        
        <nav aria-label="منوی اصلی">
            <ul class="nav-menu" id="nav-menu">
                <?php
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'container' => false,
                    'items_wrap' => '%3$s',
                    'fallback_cb' => 'didbarttar_default_menu',
                ]);
                ?>
            </ul>
        </nav>
        
        <div class="nav-cta">
            <a href="tel:<?php echo esc_attr(get_theme_mod('did_phone', '02188000000')); ?>" class="btn btn-ghost" style="padding:8px 16px;font-size:13px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                تماس
            </a>
            <button class="menu-toggle" id="menu-toggle" aria-label="باز کردن منو" aria-expanded="false">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
        </div>
        <!-- جستجوی زنده -->
        <div class="header-search" id="header-search">
        <button type="button" class="search-toggle" id="search-toggle" 
                aria-label="باز کردن جستجو" aria-expanded="false" aria-controls="search-panel">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
        </button>

        <div class="search-panel" id="search-panel" hidden>
            <form class="search-form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <svg class="search-form-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="search" id="live-search-input" name="s" 
                    placeholder="جستجوی محصول، خدمات، مقاله..." 
                    autocomplete="off" aria-label="جستجو در سایت">
                <button type="submit" class="search-submit" aria-label="ارسال جستجو">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                    </svg>
                </button>
            </form>
            <div class="search-results" id="search-results" role="listbox" aria-label="نتایج جستجو" hidden></div>
        </div>
        </div>
    </div>
</header>

<main id="main-content"></main>