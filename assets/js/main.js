// Header scroll effect
const header = document.getElementById("site-header");
if (header) {
  let lastScroll = 0;
  window.addEventListener(
    "scroll",
    () => {
      const y = window.scrollY;
      header.classList.toggle("scrolled", y > 50);
      lastScroll = y;
    },
    { passive: true },
  );
}

// Mobile menu
const menuToggle = document.getElementById("menu-toggle");
const navMenu = document.getElementById("nav-menu");
if (menuToggle && navMenu) {
  menuToggle.addEventListener("click", () => {
    const active = navMenu.classList.toggle("active");
    menuToggle.setAttribute("aria-expanded", active);
  });
  navMenu
    .querySelectorAll("a")
    .forEach((a) =>
      a.addEventListener("click", () => navMenu.classList.remove("active")),
    );
}

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach((link) => {
  link.addEventListener("click", (e) => {
    const target = document.querySelector(link.getAttribute("href"));
    if (target) {
      e.preventDefault();
      target.scrollIntoView({ behavior: "smooth", block: "start" });
    }
  });
});

// Counter animation for stats
function animateCounter(el, target, duration = 2000) {
  const start = 0;
  const startTime = performance.now();
  const update = (now) => {
    const progress = Math.min((now - startTime) / duration, 1);
    const eased = 1 - Math.pow(1 - progress, 3);
    el.textContent = Math.floor(eased * target).toLocaleString("fa-IR");
    if (progress < 1) requestAnimationFrame(update);
  };
  requestAnimationFrame(update);
}

const statObserver = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting && !entry.target.dataset.animated) {
        entry.target.dataset.animated = "true";
        const target = parseInt(entry.target.dataset.target);
        animateCounter(entry.target, target);
      }
    });
  },
  { threshold: 0.5 },
);

document
  .querySelectorAll(".stat-number[data-target]")
  .forEach((el) => statObserver.observe(el));

// Contact form AJAX
const contactForm = document.getElementById("contact-form");
if (contactForm) {
  contactForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const btn = contactForm.querySelector('button[type="submit"]');
    const originalText = btn.textContent;
    btn.textContent = "در حال ارسال...";
    btn.disabled = true;

    try {
      const res = await fetch(didbarttar.ajaxUrl, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
          action: "did_contact",
          nonce: didbarttar.nonce,
          name: contactForm.name.value,
          phone: contactForm.phone.value,
          message: contactForm.message.value,
        }),
      });
      const data = await res.json();
      if (data.success) {
        alert("✅ " + data.data.message);
        contactForm.reset();
      } else {
        alert("❌ " + data.data.message);
      }
    } catch (err) {
      alert("خطا در ارتباط با سرور");
    } finally {
      btn.textContent = originalText;
      btn.disabled = false;
    }
  });
}
// Product Tabs
document.querySelectorAll(".ps-tab-btn").forEach((btn) => {
  btn.addEventListener("click", () => {
    const tabId = btn.dataset.tab;
    const tabs = btn.closest(".ps-tabs");

    tabs
      .querySelectorAll(".ps-tab-btn")
      .forEach((b) => b.classList.remove("active"));
    tabs
      .querySelectorAll(".ps-tab-content")
      .forEach((c) => c.classList.remove("active"));

    btn.classList.add("active");
    document.getElementById("tab-" + tabId).classList.add("active");
  });
});
