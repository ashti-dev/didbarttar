/**
 * Live Search — دید برتر
 * Debounce + AbortController + ناوبری کیبورد + Highlight
 */
(function () {
  "use strict";

  const toggle = document.getElementById("search-toggle");
  const panel = document.getElementById("search-panel");
  const input = document.getElementById("live-search-input");
  const resultsBox = document.getElementById("search-results");

  if (
    !toggle ||
    !panel ||
    !input ||
    !resultsBox ||
    typeof didSearch === "undefined"
  )
    return;

  const MIN_CHARS = parseInt(didSearch.minChars, 10) || 2;
  let debounceTimer = null;
  let abortCtrl = null;
  let activeIndex = -1;

  /* ---------- باز/بسته کردن پنل ---------- */
  function openPanel() {
    panel.hidden = false;
    toggle.setAttribute("aria-expanded", "true");
    setTimeout(() => input.focus(), 60);
  }
  function closePanel() {
    panel.hidden = true;
    resultsBox.hidden = true;
    toggle.setAttribute("aria-expanded", "false");
    resultsBox.innerHTML = "";
    activeIndex = -1;
  }

  toggle.addEventListener("click", () =>
    panel.hidden ? openPanel() : closePanel(),
  );

  document.addEventListener("click", (e) => {
    if (!panel.hidden && !e.target.closest("#header-search")) closePanel();
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && !panel.hidden) {
      closePanel();
      toggle.focus();
    }
  });

  /* ---------- ورودی کاربر (Debounce) ---------- */
  input.addEventListener("input", () => {
    clearTimeout(debounceTimer);
    const term = input.value.trim();
    if (term.length < MIN_CHARS) {
      resultsBox.hidden = true;
      resultsBox.innerHTML = "";
      return;
    }
    debounceTimer = setTimeout(() => fetchResults(term), 300);
  });

  /* ---------- ناوبری کیبورد ---------- */
  input.addEventListener("keydown", (e) => {
    const items = resultsBox.querySelectorAll(".search-item");
    if (!items.length) return;

    if (e.key === "ArrowDown") {
      e.preventDefault();
      activeIndex = (activeIndex + 1) % items.length;
      setActive(items);
    } else if (e.key === "ArrowUp") {
      e.preventDefault();
      activeIndex = (activeIndex - 1 + items.length) % items.length;
      setActive(items);
    } else if (e.key === "Enter" && activeIndex > -1) {
      e.preventDefault();
      window.location.href = items[activeIndex].getAttribute("href");
    }
  });

  function setActive(items) {
    items.forEach((it, i) => it.classList.toggle("active", i === activeIndex));
    if (items[activeIndex])
      items[activeIndex].scrollIntoView({ block: "nearest" });
  }

  /* ---------- درخواست AJAX ---------- */
  async function fetchResults(term) {
    if (abortCtrl) abortCtrl.abort();
    abortCtrl = new AbortController();

    resultsBox.hidden = false;
    resultsBox.innerHTML =
      '<div class="search-loading"><span class="search-spinner"></span> در حال جستجو...</div>';

    const url =
      didSearch.ajaxUrl +
      "?action=did_live_search" +
      "&nonce=" +
      encodeURIComponent(didSearch.nonce) +
      "&term=" +
      encodeURIComponent(term);

    try {
      const res = await fetch(url, {
        signal: abortCtrl.signal,
        credentials: "same-origin",
      });
      const json = await res.json();
      if (json && json.success) render(json.data, term);
    } catch (err) {
      if (err.name !== "AbortError") {
        resultsBox.innerHTML =
          '<div class="search-empty"><p>خطا در برقراری ارتباط. دوباره تلاش کنید.</p></div>';
      }
    }
  }

  /* ---------- رندر نتایج ---------- */
  function render(data, term) {
    activeIndex = -1;
    const list = data.results || [];

    if (!list.length) {
      resultsBox.innerHTML =
        '<div class="search-empty">' +
        '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/></svg>' +
        "<p>نتیجه‌ای برای «" +
        escapeHtml(term) +
        "» یافت نشد</p>" +
        (data.allUrl
          ? '<a class="search-empty-link" href="' +
            escapeHtml(data.allUrl) +
            '">جستجو در کل سایت</a>'
          : "") +
        "</div>";
      return;
    }

    let html = '<ul class="search-list">';
    list.forEach((r) => {
      const thumb = r.image
        ? '<span class="search-thumb"><img src="' +
          escapeHtml(r.image) +
          '" alt="" loading="lazy"></span>'
        : '<span class="search-thumb"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></span>';

      html +=
        '<li><a class="search-item" href="' +
        escapeHtml(r.url) +
        '">' +
        thumb +
        '<span class="search-item-body">' +
        '<span class="search-item-title">' +
        highlight(r.title, term) +
        "</span>" +
        (r.cat
          ? '<span class="search-item-cat">' + escapeHtml(r.cat) + "</span>"
          : "") +
        "</span>" +
        '<span class="search-item-side">' +
        (r.meta
          ? '<span class="search-item-price">' + escapeHtml(r.meta) + "</span>"
          : "") +
        '<span class="search-item-label">' +
        escapeHtml(r.label) +
        "</span>" +
        "</span>" +
        "</a></li>";
    });
    html += "</ul>";

    if (data.allUrl) {
      html +=
        '<div class="search-footer"><a class="search-footer-link" href="' +
        escapeHtml(data.allUrl) +
        '">مشاهده همه نتایج برای «' +
        escapeHtml(term) +
        "»</a></div>";
    }

    resultsBox.innerHTML = html;
  }

  /* ---------- هایلایت عبارت جستجوشده ---------- */
  function highlight(text, term) {
    const safe = escapeHtml(text);
    const safeTerm = escapeHtml(term).replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    try {
      return safe.replace(
        new RegExp("(" + safeTerm + ")", "gi"),
        "<mark>$1</mark>",
      );
    } catch (e) {
      return safe;
    }
  }

  function escapeHtml(str) {
    const div = document.createElement("div");
    div.textContent = str == null ? "" : String(str);
    return div.innerHTML;
  }
})();
