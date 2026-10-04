/**
 * Hero Visual Slider — توران تجارت
 * Vanilla JS | RTL-aware | WordPress ready | No dependencies
 *
 * Based on: Yaghoub Tayebi / KhanWP slider kit
 * Adapted for torantejarat theme class names.
 * ---------------------------------------------------------------
 * این اسکریپت:
 *  - .torantejarat-hero-visual را پیدا می‌کند
 *  - تصویر ثابت .torantejarat-hero-device را به اسلایدر تبدیل می‌کند
 *  - شماره‌گذاری .torantejarat-visual-label را زنده نگه می‌دارد
 *  - اتوپلی با progress bar دارد
 *  - touch/swipe و keyboard را پشتیبانی می‌کند
 *
 * data attributes روی .torantejarat-hero-visual:
 *   data-hero-slides             — JSON اسلایدها (src/alt/caption/link/linkText/label)
 *   data-hero-slider-autoplay="5000"   — ms، پیش‌فرض: ۵۰۰۰
 *   data-hero-slider-loop="true"       — پیش‌فرض: true
 */

(function () {
  'use strict';

  var IS_RTL = document.documentElement.dir === 'rtl' ||
    document.documentElement.lang === 'fa' ||
    document.documentElement.lang.indexOf('fa') === 0;

  /* ──────────────────────────────────────────────
     داده پیش‌فرض (fallback اگر data-hero-slides نباشد)
  ────────────────────────────────────────────── */
  var FALLBACK_SLIDES = [];

  /* ──────────────────────────────────────────────
     SVG آیکون‌ها
  ────────────────────────────────────────────── */
  var ICON_ARROW_RTL_PREV = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m14 7-5 5 5 5M9 12h12"/></svg>';
  var ICON_ARROW_RTL_NEXT = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m10 7 5 5-5 5M15 12H3"/></svg>';

  /* ──────────────────────────────────────────────
     کلاس اصلی
  ────────────────────────────────────────────── */
  function HeroSlider(visual, slidesData) {
    this.visual      = visual;
    this.slidesData  = slidesData;
    this.total       = slidesData.length;
    this.currentIdx  = 0;
    this.autoplayMs  = parseInt(visual.dataset.heroSliderAutoplay, 10) || 7000;
    this.loop        = visual.dataset.heroSliderLoop !== 'false'; /* پیش‌فرض true */
    this.timer       = null;
    this.progressRaf = null;

    if (this.total < 1) return;

    this._build();
    this._bindEvents();
    this._goTo(0, false);
    this.visual.classList.add('slider-ready');
    this._startAutoplay();
  }

  HeroSlider.prototype = {

    /* ── ساخت DOM ── */
    _build: function () {
      var self = this;

      /* ۱. تصویر ثابت قبلی را حذف کن */
      var oldImg = this.visual.querySelector('.torantejarat-hero-device');
      if (oldImg) oldImg.remove();

      /* ۲. کپشن ثابت قبلی را حذف کن (توسط اسلایدها مدیریت می‌شود) */
      var oldCaption = this.visual.querySelector('.torantejarat-visual-caption');
      if (oldCaption) oldCaption.remove();

      /* ۳. Track بساز */
      this.track = document.createElement('div');
      this.track.className = 'hero-slider-track';
      this.track.setAttribute('aria-live', 'polite');
      this.track.setAttribute('aria-atomic', 'false');

      /* ۴. اسلایدها را بساز */
      this.slideEls = this.slidesData.map(function (data, i) {
        var slide = document.createElement('div');
        slide.className = 'hero-slide';
        slide.setAttribute('role', 'group');
        slide.setAttribute('aria-roledescription', 'اسلاید');
        slide.setAttribute('aria-label', (i + 1) + ' از ' + self.total);

        /* تصویر — لینک به صفحه محصول (بدون متن کپشن) */
        var imgLink = document.createElement('a');
        imgLink.href = data.link;
        imgLink.setAttribute('aria-label', data.alt);

        var img = document.createElement('img');
        img.className = 'torantejarat-hero-device';
        img.src       = data.src;
        img.alt       = '';
        img.width     = 1020;
        img.height    = 1020;
        if (i === 0) img.setAttribute('fetchpriority', 'high');
        else         img.setAttribute('loading', 'lazy');

        imgLink.appendChild(img);
        slide.appendChild(imgLink);
        self.track.appendChild(slide);
        return slide;
      });

      /* ۵. Track را به visual اضافه کن (قبل از عناصر موجود) */
      this.visual.insertBefore(this.track, this.visual.firstChild);

      /* ۶. نقاط */
      this.dotsWrap = document.createElement('div');
      this.dotsWrap.className = 'hero-slider-dots';
      this.dotsWrap.setAttribute('role', 'tablist');
      this.dotsWrap.setAttribute('aria-label', 'انتخاب دستگاه');
      this.dots = this.slidesData.map(function (_, i) {
        var dot = document.createElement('button');
        dot.className = 'hero-dot';
        dot.setAttribute('role', 'tab');
        dot.setAttribute('aria-label', 'دستگاه ' + (i + 1));
        dot.setAttribute('aria-selected', i === 0 ? 'true' : 'false');
        dot.addEventListener('click', (function (idx) {
          return function () {
            self._stopAutoplay();
            self._goTo(idx);
            self._startAutoplay();
          };
        })(i));
        self.dotsWrap.appendChild(dot);
        return dot;
      });
      this.visual.appendChild(this.dotsWrap);

      /* ۷. دکمه‌های ناوبری */
      this.btnPrev = document.createElement('button');
      this.btnPrev.className = 'hero-slider-nav hero-slider-nav-prev';
      this.btnPrev.setAttribute('aria-label', 'دستگاه قبلی');
      this.btnPrev.innerHTML = ICON_ARROW_RTL_PREV;
      this.btnPrev.addEventListener('click', function () {
        self._stopAutoplay();
        self._step(-1);
        self._startAutoplay();
      });

      this.btnNext = document.createElement('button');
      this.btnNext.className = 'hero-slider-nav hero-slider-nav-next';
      this.btnNext.setAttribute('aria-label', 'دستگاه بعدی');
      this.btnNext.innerHTML = ICON_ARROW_RTL_NEXT;
      this.btnNext.addEventListener('click', function () {
        self._stopAutoplay();
        self._step(1);
        self._startAutoplay();
      });

      this.visual.appendChild(this.btnPrev);
      this.visual.appendChild(this.btnNext);

      /* ۸. Progress bar */
      this.progressEl = document.createElement('div');
      this.progressEl.className = 'hero-progress';
      this.progressBar = document.createElement('div');
      this.progressBar.className = 'hero-progress-bar';
      this.progressEl.appendChild(this.progressBar);
      this.visual.appendChild(this.progressEl);

      /* ۹. ذخیره رفرنس visual-label برای آپدیت شماره */
      this.labelEl = this.visual.querySelector('.torantejarat-visual-label');
      this.labelCount = this.labelEl
        ? this.labelEl.querySelector('span:last-child')
        : null;
    },

    /* ── رفتن به ایندکس ── */
    _goTo: function (idx, animate) {
      if (animate === undefined) animate = true;
      idx = ((idx % this.total) + this.total) % this.total; /* loop-safe */
      this.currentIdx = idx;

      /* Transform — RTL: مثبت */
      var offset = idx * 100;
      var transform = IS_RTL
        ? 'translateX(' + offset + '%)'
        : 'translateX(-' + offset + '%)';

      if (!animate) {
        this.track.style.transition = 'none';
        this.track.style.transform  = transform;
        /* force reflow */
        void this.track.offsetHeight;
        this.track.style.transition = '';
      } else {
        this.track.style.transform = transform;
      }

      /* آپدیت کلاس‌های اسلاید */
      this.slideEls.forEach(function (slide, i) {
        slide.classList.toggle('is-active', i === idx);
        slide.setAttribute('aria-hidden', i !== idx ? 'true' : 'false');
      });

      /* آپدیت نقاط */
      this.dots.forEach(function (dot, i) {
        var active = i === idx;
        dot.classList.toggle('is-active', active);
        dot.setAttribute('aria-selected', active ? 'true' : 'false');
      });

      /* آپدیت visual-label */
      this._updateLabel(idx);

      /* آپدیت دکمه‌ها */
      if (!this.loop) {
        this.btnPrev.disabled = idx <= 0;
        this.btnNext.disabled = idx >= this.total - 1;
      }

      /* ریست + شروع مجدد progress bar (اگر اتوپلی روشن است) */
      this._resetProgress();
      if (this.timer) {
        this._startProgress();
      }
    },

    /* ── حرکت نسبی ── */
    _step: function (dir) {
      var next = this.currentIdx + dir;
      if (this.loop) {
        next = ((next % this.total) + this.total) % this.total;
      } else {
        next = Math.max(0, Math.min(next, this.total - 1));
      }
      this._goTo(next);
    },

    /* ── آپدیت visual-label ── */
    _updateLabel: function (idx) {
      if (!this.labelEl) return;
      var data = this.slidesData[idx];
      var num  = String(idx + 1).padStart(2, '0');
      var tot  = String(this.total).padStart(2, '0');

      /* آپدیت text label — پیدا کردن text node بین status-dot و span آخر */
      var nodes = this.labelEl.childNodes;
      for (var i = 0; i < nodes.length; i++) {
        if (nodes[i].nodeType === Node.TEXT_NODE) {
          nodes[i].textContent = ' ' + data.label + ' ';
          break;
        }
      }

      /* آپدیت شماره "01 / 04" */
      if (this.labelCount) {
        this.labelCount.textContent = num + ' / ' + tot;
      }
    },

    /* ── Progress bar با requestAnimationFrame ── */
    _resetProgress: function () {
      if (this.progressRaf) {
        cancelAnimationFrame(this.progressRaf);
        this.progressRaf = null;
      }
      this.progressBar.style.transition = 'none';
      this.progressBar.style.width = '0%';
    },

    _startProgress: function () {
      var self = this;
      var duration = this.autoplayMs;
      var startTime = null;

      function step(timestamp) {
        if (!startTime) startTime = timestamp;
        var elapsed = timestamp - startTime;
        var pct = Math.min((elapsed / duration) * 100, 100);
        self.progressBar.style.width = pct + '%';
        if (elapsed < duration) {
          self.progressRaf = requestAnimationFrame(step);
        }
      }

      /* کوچک‌ترین تاخیر برای اعمال transition=none */
      requestAnimationFrame(function () {
        startTime = null;
        self.progressRaf = requestAnimationFrame(step);
      });
    },

    /* ── Autoplay ── */
    _startAutoplay: function () {
      var self = this;
      /* گارد: جلوگیری از تایمر موازی (عامل پرش زودهنگام اسلاید) */
      this._stopAutoplay();
      this._startProgress();
      this.timer = setInterval(function () {
        self._step(1);
      }, this.autoplayMs);
    },

    _stopAutoplay: function () {
      if (this.timer) {
        clearInterval(this.timer);
        this.timer = null;
      }
      this._resetProgress();
    },

    /* ── رویدادها ── */
    _bindEvents: function () {
      var self = this;

      /* hover: pause */
      this.visual.addEventListener('mouseenter', function () {
        self._stopAutoplay();
      });
      this.visual.addEventListener('mouseleave', function () {
        self._startAutoplay();
      });

      /* focus: pause */
      this.visual.addEventListener('focusin', function () {
        self._stopAutoplay();
      });
      this.visual.addEventListener('focusout', function (e) {
        if (!self.visual.contains(e.relatedTarget)) {
          self._startAutoplay();
        }
      });

      /* keyboard */
      this.visual.setAttribute('tabindex', '0');
      this.visual.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowRight') {
          e.preventDefault();
          self._stopAutoplay();
          self._step(IS_RTL ? -1 : 1);
          self._startAutoplay();
        }
        if (e.key === 'ArrowLeft') {
          e.preventDefault();
          self._stopAutoplay();
          self._step(IS_RTL ? 1 : -1);
          self._startAutoplay();
        }
      });

      /* touch / swipe */
      this._bindTouch();
    },

    _bindTouch: function () {
      var self = this;
      var startX = 0, diffX = 0, isDragging = false;
      var THRESHOLD = 50;

      this.track.addEventListener('touchstart', function (e) {
        startX = e.touches[0].clientX;
        diffX  = 0;
        isDragging = true;
        self._stopAutoplay();
      }, { passive: true });

      this.track.addEventListener('touchmove', function (e) {
        if (!isDragging) return;
        diffX = e.touches[0].clientX - startX;
      }, { passive: true });

      this.track.addEventListener('touchend', function () {
        if (!isDragging) return;
        isDragging = false;
        var dir = IS_RTL ? -1 : 1;
        if (diffX > THRESHOLD)       self._step(-1 * dir);
        else if (diffX < -THRESHOLD) self._step(1 * dir);
        self._startAutoplay();
      });
    },

  };

  /* ──────────────────────────────────────────────
     خواندن دیتای اسلایدها از data attribute (PHP) با fallback
  ────────────────────────────────────────────── */
  function readSlidesData(visual) {
    var raw = visual.getAttribute('data-hero-slides');
    if (raw) {
      try {
        var parsed = JSON.parse(raw);
        if (Array.isArray(parsed) && parsed.length) {
          return parsed;
        }
      } catch (e) { /* fallback پایین */ }
    }
    return FALLBACK_SLIDES;
  }

  /* ──────────────────────────────────────────────
     مقداردهی خودکار
  ────────────────────────────────────────────── */
  function init() {
    var visual = document.querySelector('.torantejarat-hero-visual');
    if (visual && !visual._heroSlider) {
      var slides = readSlidesData(visual);
      if (slides.length) {
        visual._heroSlider = new HeroSlider(visual, slides);
      }
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  window.HeroSlider = HeroSlider;

})();
