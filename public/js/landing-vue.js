(function () {
  if (!window.Vue) return;

  var raw = document.getElementById('lp-page-data');
  if (!raw) return;

  var page;
  try {
    page = JSON.parse(raw.textContent || '{}');
  } catch (e) {
    console.error('Landing page payload parse failed', e);
    return;
  }

  var appEl = document.getElementById('lp-vue-app');
  if (!appEl) return;

  window.Vue.createApp({
    data: function () {
      return {
        page: page,
        activeShot: 0,
        lightbox: null,
        openFaq: 0,
        touchStartX: null,
        reviewFilter: ''
      };
    },
    computed: {
      galleryMedia: function () {
        var images = this.page.gallery || [];
        var items = images.map(function (url) {
          return { type: 'image', url: url, thumb: url };
        });
        if (this.page.videoUrl) {
          var poster = items[0] ? items[0].url : (this.page.heroShot || '');
          var videoItem = { type: 'video', url: this.page.videoUrl, thumb: poster };
          if (items.length) {
            items.splice(1, 0, videoItem);
          } else {
            items.push(videoItem);
          }
        }
        return items;
      },
      activeItem: function () {
        return this.galleryMedia[this.activeShot] || null;
      },
      secondaryTarget: function () {
        if (this.page.videoUrl || this.page.youtubeId) return 'demo';
        if (this.galleryMedia.length) return 'showcase';
        return 'pricing';
      },
      secondaryLabel: function () {
        if (this.page.videoUrl || this.page.youtubeId) return this.page.i18n.watchDemo;
        if (this.galleryMedia.length) return this.page.i18n.seeScreens;
        return this.page.i18n.seePricing;
      },
      solutionPoints: function () {
        var cards = this.page.featureCards || [];
        return cards.slice(0, 3).map(function (card) {
          return card.title;
        });
      },
      heroShotUrl: function () {
        var item = this.activeItem;
        if (item && item.type === 'image') return item.url;
        if (item && item.type === 'video') return item.thumb || null;
        return this.page.heroShot || null;
      },
      reviewThemes: function () {
        var items = (this.page.reviews && this.page.reviews.items) || [];
        var seen = {};
        var themes = [];
        items.forEach(function (item) {
          var theme = (item.theme || '').trim();
          if (theme && !seen[theme]) {
            seen[theme] = true;
            themes.push(theme);
          }
        });
        return themes;
      },
      filteredReviews: function () {
        var items = (this.page.reviews && this.page.reviews.items) || [];
        var filter = this.reviewFilter;
        if (!filter) return items;
        return items.filter(function (item) {
          return item.theme === filter;
        });
      }
    },
    methods: {
      formatMoney: function (value) {
        var n = Number(value || 0);
        var whole = Math.abs(n - Math.round(n)) < 0.00001;
        return n.toLocaleString(undefined, {
          minimumFractionDigits: whole ? 0 : 2,
          maximumFractionDigits: whole ? 0 : 2
        });
      },
      scrollTo: function (id) {
        var el = document.getElementById(id);
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
      },
      shotCount: function () {
        return this.galleryMedia.length;
      },
      nextShot: function () {
        var total = this.shotCount();
        if (total) this.activeShot = (this.activeShot + 1) % total;
      },
      prevShot: function () {
        var total = this.shotCount();
        if (total) this.activeShot = (this.activeShot - 1 + total) % total;
      },
      onTouchStart: function (event) {
        this.touchStartX = event.changedTouches[0].clientX;
      },
      onTouchEnd: function (event) {
        if (this.touchStartX === null) return;
        var delta = event.changedTouches[0].clientX - this.touchStartX;
        this.touchStartX = null;
        if (Math.abs(delta) < 45) return;
        var forward = delta < 0;
        if (this.page.rtl) forward = !forward;
        if (forward) {
          this.nextShot();
        } else {
          this.prevShot();
        }
      },
      toggleFaq: function (idx) {
        this.openFaq = this.openFaq === idx ? null : idx;
      },
      onKeydown: function (event) {
        if (this.lightbox === null) return;
        if (event.key === 'Escape') this.lightbox = null;
        var nextKey = this.page.rtl ? 'ArrowLeft' : 'ArrowRight';
        var prevKey = this.page.rtl ? 'ArrowRight' : 'ArrowLeft';
        if (event.key === nextKey) this.lightbox = (this.lightbox + 1) % this.shotCount();
        if (event.key === prevKey) this.lightbox = (this.lightbox - 1 + this.shotCount()) % this.shotCount();
      },
      observeReveals: function () {
        var nodes = appEl.querySelectorAll('[data-reveal]');

        if (!('IntersectionObserver' in window)) {
          Array.prototype.forEach.call(nodes, function (node) { node.classList.add('in'); });
          return;
        }

        var observer = new IntersectionObserver(function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              entry.target.classList.add('in');
              observer.unobserve(entry.target);
            }
          });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

        Array.prototype.forEach.call(nodes, function (node) { observer.observe(node); });
      },
      openBuy: function () {
        if (typeof window.trackBuyNowClick === 'function') window.trackBuyNowClick();
        if (typeof window.showPaymentModal === 'function') window.showPaymentModal();
      },
      openHeroLightbox: function () {
        if (this.shotCount()) this.lightbox = this.activeShot;
      },
      starString: function (n) {
        n = Math.max(0, Math.min(5, parseInt(n, 10) || 0));
        return Array(n + 1).join('★') + Array(6 - n).join('☆');
      }
    },
    mounted: function () {
      var self = this;
      this.$nextTick(function () { self.observeReveals(); });
      document.addEventListener('keydown', this.onKeydown);
    },
    beforeUnmount: function () {
      document.removeEventListener('keydown', this.onKeydown);
    }
  }).mount('#lp-vue-app');
})();
