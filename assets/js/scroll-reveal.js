class ScrollReveal {
  constructor(options = {}) {
    this.options = Object.assign(
      {
        threshold: 0.12,
        rootMargin: "0px 0px -60px 0px",
        distance: "40px",
        duration: 800,
        once: true,
      },
      options,
    );
    this.observer = null;
    this.init();
  }

  init() {
    if (!("IntersectionObserver" in window)) {
      document
        .querySelectorAll("[data-reveal]")
        .forEach((el) => el.classList.add("revealed"));
      return;
    }

    this.observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("revealed");
            if (this.options.once) this.observer.unobserve(entry.target);
          } else if (!this.options.once) {
            entry.target.classList.remove("revealed");
          }
        });
      },
      {
        threshold: this.options.threshold,
        rootMargin: this.options.rootMargin,
      },
    );

    document
      .querySelectorAll("[data-reveal]")
      .forEach((el) => this.observer.observe(el));
  }

  observe(el) {
    if (el) this.observer.observe(el);
  }
}

document.addEventListener("DOMContentLoaded", () => {
  window.scrollReveal = new ScrollReveal();
});
