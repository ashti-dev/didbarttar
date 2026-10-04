/**
 * Product Slider — شلنگ‌بین
 * Vanilla JS | RTL-aware | WordPress ready | No dependencies
 *
 * Yaghoub Tayebi / KhanWP
 * ---------------------------------------------------------------
 * استفاده:
 *   <div class="product-slider-wrap" data-slider>
 *     <div class="product-slider-track">
 *       <div class="product-slide">...</div>
 *       ...
 *     </div>
 *   </div>
 *
 * Data attributes (اختیاری روی wrapper):
 *   data-slides-desktop="3"   — تعداد اسلاید در دسکتاپ (پیش‌فرض: 3)
 *   data-slides-tablet="2"    — تبلت (پیش‌فرض: 2)
 *   data-slides-mobile="1"    — موبایل (پیش‌فرض: 1)
 *   data-autoplay="5000"      — اوتوپلی (ms) — 0 = غیرفعال (پیش‌فرض: 0)
 *   data-loop="true"          — لوپ (پیش‌فرض: false)
 */

(function () {
  'use strict';

  /* ── تشخیص RTL ── */
  var IS_RTL = document.documentElement.dir === 'rtl' ||
    document.documentElement.lang === 'fa' ||
    document.documentElement.lang === 'ar';

  /* ── Breakpoints ── */
  var BP_TABLET  = 1024;
  var BP_MOBILE  = 640;

  /* ── ایجاد SVG آیکون فلش ── */
  function arrowSvg(direction) {
    /* در RTL جهت فلش‌ها برعکس می‌شه */
    var d = IS_RTL
      ? (direction === 'prev' ? 'm14 7-5 5 5 5M9 12h12' : 'm10 7 5 5-5 5M15 12H3')
      : (direction === 'prev' ? 'm10 7 5 5-5 5M15 12H3' : 'm14 7-5 5 5 5M9 12h12');
    return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" ' +
      'stroke="currentColor" stroke-width="2" stroke-linecap="round" ' +
      'stroke-linejoin="round" aria-hidden="true"><path d="' + d + '"/></svg>';
  }

  /* ═══════════════════════════════════════════════════════════
     کلاس اصلی اسلایدر
  ═══════════════════════════════════════════════════════════ */
  function ProductSlider(wrapper) {
    this.wrapper     = wrapper;
    this.track       = wrapper.querySelector('.product-slider-track');
    this.slides      = Array.from(wrapper.querySelectorAll('.product-slide'));
    this.currentIdx  = 0;
    this.autoplayTimer = null;
    this.resizeTimer   = null;

    /* خوندن تنظیمات از data attributes */
    this.config = {
      slidesDesktop : parseInt(wrapper.dataset.slidesDesktop, 10) || 3,
      slidesTablet  : parseInt(wrapper.dataset.slidesTablet,  10) || 2,
      slidesMobile  : parseInt(wrapper.dataset.slidesMobile,  10) || 1,
      autoplay      : parseInt(wrapper.dataset.autoplay,      10) || 0,
      loop          : wrapper.dataset.loop === 'true',
    };

    if (!this.track || !this.slides.length) return;

    this._init();
  }

  ProductSlider.prototype = {

    /* ── مقداردهی اولیه ── */
    _init: function () {
      this._buildControls();
      this._measure();
      this._goTo(0, false);
      this._bindEvents();
      this.wrapper.classList.add('slider-ready');
      if (this.config.autoplay > 0) this._startAutoplay();
    },

    /* ── ساخت دکمه‌ها و نقاط ── */
    _buildControls: function () {
      var self = this;

      /* دکمه قبلی */
      this.btnPrev = document.createElement('button');
      this.btnPrev.className = 'slider-nav slider-nav-prev';
      this.btnPrev.setAttribute('aria-label', 'دستگاه قبلی');
      this.btnPrev.innerHTML = arrowSvg('prev');
      this.btnPrev.addEventListener('click', function () { self._step(-1); });

      /* دکمه بعدی */
      this.btnNext = document.createElement('button');
      this.btnNext.className = 'slider-nav slider-nav-next';
      this.btnNext.setAttribute('aria-label', 'دستگاه بعدی');
      this.btnNext.innerHTML = arrowSvg('next');
      this.btnNext.addEventListener('click', function () { self._step(1); });

      /* ردیف نقاط */
      this.dotsWrap = document.createElement('div');
      this.dotsWrap.className = 'slider-dots';
      this.dotsWrap.setAttribute('role', 'tablist');
      this.dotsWrap.setAttribute('aria-label', 'انتخاب دستگاه');

      this.wrapper.appendChild(this.btnPrev);
      this.wrapper.appendChild(this.btnNext);
      this.wrapper.appendChild(this.dotsWrap);
    },

    /* ── محاسبه اندازه‌ها (هنگام load و resize) ── */
    _measure: function () {
      var vw = this.wrapper.offsetWidth;

      /* تعداد اسلاید مرئی بر اساس پنجره */
      if (window.innerWidth <= BP_MOBILE) {
        this.visible = this.config.slidesMobile;
      } else if (window.innerWidth <= BP_TABLET) {
        this.visible = this.config.slidesTablet;
      } else {
        this.visible = this.config.slidesDesktop;
      }

      /* مطمئن می‌شیم visible از تعداد کل اسلاید بیشتر نشه */
      this.visible = Math.min(this.visible, this.slides.length);

      /* عرض هر اسلاید = عرض wrapper / تعداد مرئی */
      this.slideWidth = 100 / this.visible;

      /* اعمال CSS variable */
      this.slides.forEach(function (slide) {
        slide.style.setProperty('--slide-width', this.slideWidth + '%');
      }, this);

      /* حداکثر ایندکس قابل دسترس */
      this.maxIdx = this.slides.length - this.visible;

      /* بازسازی نقاط */
      this._buildDots();

      /* اصلاح ایندکس فعلی اگر از محدوده خارج شده */
      if (this.currentIdx > this.maxIdx) {
        this._goTo(this.maxIdx, false);
      } else {
        this._goTo(this.currentIdx, false);
      }
    },

    /* ── ساخت / بازسازی نقاط ── */
    _buildDots: function () {
      var self = this;
      this.dotsWrap.innerHTML = '';
      this.dots = [];

      var totalDots = this.maxIdx + 1;
      for (var i = 0; i < totalDots; i++) {
        var dot = document.createElement('button');
        dot.className = 'slider-dot';
        dot.setAttribute('role', 'tab');
        dot.setAttribute('aria-label', 'دستگاه ' + (i + 1));
        dot.setAttribute('aria-selected', i === 0 ? 'true' : 'false');
        dot.dataset.idx = i;
        dot.addEventListener('click', (function (idx) {
          return function () { self._goTo(idx); };
        })(i));
        this.dotsWrap.appendChild(dot);
        this.dots.push(dot);
      }
    },

    /* ── رفتن به ایندکس مشخص ── */
    _goTo: function (idx, animate) {
      if (animate === undefined) animate = true;

      /* محدود کردن به محدوده مجاز */
      idx = Math.max(0, Math.min(idx, this.maxIdx));
      this.currentIdx = idx;

      /* محاسبه مقدار transform
         در RTL باید translate مثبت باشد (برعکس LTR) */
      var offset = idx * this.slideWidth;
      var transform = IS_RTL
        ? 'translateX(' + offset + '%)'
        : 'translateX(-' + offset + '%)';

      this.track.style.transform = transform;

      /* به‌روزرسانی نقاط */
      this.dots.forEach(function (dot, i) {
        var active = i === idx;
        dot.classList.toggle('is-active', active);
        dot.setAttribute('aria-selected', active ? 'true' : 'false');
      });

      /* به‌روزرسانی دکمه‌ها */
      this._updateNavState();
    },

    /* ── حرکت به اندازه یک قدم ── */
    _step: function (dir) {
      var next = this.currentIdx + dir;

      if (this.config.loop) {
        if (next < 0) next = this.maxIdx;
        if (next > this.maxIdx) next = 0;
      }

      this._goTo(next);
      /* ریست اوتوپلی هنگام کلیک دستی */
      if (this.config.autoplay > 0) {
        this._stopAutoplay();
        this._startAutoplay();
      }
    },

    /* ── وضعیت دکمه‌های ناوبری ── */
    _updateNavState: function () {
      if (this.config.loop) {
        this.btnPrev.disabled = false;
        this.btnNext.disabled = false;
      } else {
        this.btnPrev.disabled = this.currentIdx <= 0;
        this.btnNext.disabled = this.currentIdx >= this.maxIdx;
      }
    },

    /* ── اوتوپلی ── */
    _startAutoplay: function () {
      var self = this;
      this.autoplayTimer = setInterval(function () {
        var next = self.currentIdx + 1;
        if (next > self.maxIdx) next = 0;
        self._goTo(next);
      }, this.config.autoplay);
    },

    _stopAutoplay: function () {
      if (this.autoplayTimer) {
        clearInterval(this.autoplayTimer);
        this.autoplayTimer = null;
      }
    },

    /* ── رویدادها ── */
    _bindEvents: function () {
      var self = this;

      /* Resize با debounce */
      window.addEventListener('resize', function () {
        clearTimeout(self.resizeTimer);
        self.resizeTimer = setTimeout(function () {
          self._measure();
        }, 180);
      });

      /* کیبورد */
      this.wrapper.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowRight') {
          e.preventDefault();
          /* RTL: فلش راست = قبلی */
          self._step(IS_RTL ? -1 : 1);
        }
        if (e.key === 'ArrowLeft') {
          e.preventDefault();
          /* RTL: فلش چپ = بعدی */
          self._step(IS_RTL ? 1 : -1);
        }
      });

      /* توقف اوتوپلی هنگام hover */
      if (this.config.autoplay > 0) {
        this.wrapper.addEventListener('mouseenter', function () {
          self._stopAutoplay();
        });
        this.wrapper.addEventListener('mouseleave', function () {
          self._startAutoplay();
        });
        /* توقف هنگام focus (accessibility) */
        this.wrapper.addEventListener('focusin', function () {
          self._stopAutoplay();
        });
        this.wrapper.addEventListener('focusout', function () {
          self._startAutoplay();
        });
      }

      /* Touch / Drag */
      this._bindTouch();
    },

    /* ── لمس و درگ (موبایل + دسکتاپ) ── */
    _bindTouch: function () {
      var self = this;
      var startX = 0;
      var startY = 0;
      var diffX  = 0;
      var isDragging = false;
      var isScrolling = null;
      var threshold  = 50; /* حداقل px برای رجیستر شدن swipe */

      function onStart(x, y) {
        startX = x;
        startY = y;
        diffX  = 0;
        isDragging = true;
        isScrolling = null;
        self.track.classList.add('is-dragging');
        if (self.config.autoplay > 0) self._stopAutoplay();
      }

      function onMove(x, y) {
        if (!isDragging) return;
        diffX = x - startX;
        var diffY = y - startY;

        /* تشخیص اینکه کاربر scroll می‌کند یا swipe */
        if (isScrolling === null) {
          isScrolling = Math.abs(diffY) > Math.abs(diffX);
        }
        if (isScrolling) return;

        /* جلوگیری از scroll صفحه هنگام swipe افقی */
        /* (فقط در touch events — در mouse events نیازی نیست) */
      }

      function onEnd() {
        if (!isDragging) return;
        isDragging = false;
        self.track.classList.remove('is-dragging');

        if (isScrolling) return;

        /* RTL: جهت swipe برعکس LTR */
        var swipeDir = IS_RTL ? -1 : 1;
        if (diffX > threshold) {
          self._step(-1 * swipeDir);
        } else if (diffX < -threshold) {
          self._step(1 * swipeDir);
        }

        if (self.config.autoplay > 0) self._startAutoplay();
      }

      /* Touch events */
      this.track.addEventListener('touchstart', function (e) {
        onStart(e.touches[0].clientX, e.touches[0].clientY);
      }, { passive: true });

      this.track.addEventListener('touchmove', function (e) {
        onMove(e.touches[0].clientX, e.touches[0].clientY);
      }, { passive: true });

      this.track.addEventListener('touchend', onEnd);

      /* Mouse drag events */
      this.track.addEventListener('mousedown', function (e) {
        e.preventDefault();
        onStart(e.clientX, e.clientY);

        var onMouseMove = function (e) { onMove(e.clientX, e.clientY); };
        var onMouseUp   = function () {
          onEnd();
          document.removeEventListener('mousemove', onMouseMove);
          document.removeEventListener('mouseup',   onMouseUp);
        };

        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup',   onMouseUp);
      });

      /* جلوگیری از drag تصاویر هنگام mouse drag */
      this.track.querySelectorAll('img, a').forEach(function (el) {
        el.addEventListener('dragstart', function (e) { e.preventDefault(); });
      });
    },

    /* ── پاکسازی (برای حذف اسلایدر) ── */
    destroy: function () {
      this._stopAutoplay();
      this.wrapper.classList.remove('slider-ready');
      if (this.btnPrev) this.btnPrev.remove();
      if (this.btnNext) this.btnNext.remove();
      if (this.dotsWrap) this.dotsWrap.remove();
      this.track.style.transform = '';
      this.slides.forEach(function (slide) {
        slide.style.removeProperty('--slide-width');
      });
    },
  };

  /* ═══════════════════════════════════════════════════════════
     مقداردهی خودکار همه اسلایدرها در صفحه
  ═══════════════════════════════════════════════════════════ */
  function initAllSliders() {
    var wrappers = document.querySelectorAll('[data-slider]');
    wrappers.forEach(function (wrapper) {
      /* جلوگیری از دوبار init */
      if (wrapper._sliderInstance) return;
      wrapper._sliderInstance = new ProductSlider(wrapper);
    });
  }

  /* اجرا پس از آماده شدن DOM */
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAllSliders);
  } else {
    initAllSliders();
  }

  /* expose برای استفاده دستی اگر نیاز شد */
  window.ProductSlider = ProductSlider;
  window.initProductSliders = initAllSliders;

})();
