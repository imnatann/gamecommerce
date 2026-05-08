import './bootstrap';

import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import persist from '@alpinejs/persist';

Alpine.plugin(focus);
Alpine.plugin(persist);

Alpine.data('searchAutocomplete', () => ({
    query: '',
    results: [],
    isOpen: false,
    selectedIndex: -1,
    async search() {
        if (this.query.length < 2) { this.isOpen = false; this.results = []; return; }
        try {
            const res = await fetch(`/api/search?q=${encodeURIComponent(this.query)}&limit=8`);
            this.results = await res.json();
            this.isOpen = this.results.length > 0;
            this.selectedIndex = -1;
        } catch { this.isOpen = false; }
    },
    select(item) { window.location.href = item.url; },
    navigateUp() { if (this.selectedIndex > 0) this.selectedIndex--; },
    navigateDown() { if (this.selectedIndex < this.results.length - 1) this.selectedIndex++; },
    enterSelected() { if (this.selectedIndex >= 0 && this.results[this.selectedIndex]) this.select(this.results[this.selectedIndex]); },
    close() { setTimeout(() => { this.isOpen = false; }, 200); }
}));

Alpine.data('countdownTimer', (endTime) => ({
    endTime: new Date(endTime).getTime(),
    days: 0, hours: 0, minutes: 0, seconds: 0,
    expired: false,
    tick() {
        const diff = this.endTime - Date.now();
        if (diff <= 0) { this.expired = true; return; }
        this.days = Math.floor(diff / 86400000);
        this.hours = Math.floor((diff % 86400000) / 3600000);
        this.minutes = Math.floor((diff % 3600000) / 60000);
        this.seconds = Math.floor((diff % 60000) / 1000);
    },
    init() { setInterval(() => this.tick(), 1000); this.tick(); }
}));

Alpine.data('cartManager', () => ({
    count: 0,
    total: 0,
    async add(productId, quantity = 1) {
        try {
            const res = await fetch('/api/cart/add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
                body: JSON.stringify({ product_id: productId, quantity })
            });
            const data = await res.json();
            this.count = data.cart_count;
            this.total = data.cart_total;
            this.showToast('Added to cart!');
        } catch { this.showToast('Failed to add to cart', 'error'); }
    },
    showToast(message, type = 'success') {
        this.$dispatch('notify', { message, type });
    }
}));

Alpine.data('themeToggle', () => ({
    dark: localStorage.getItem('theme') === 'dark' || !localStorage.getItem('theme'),
    init() {
        document.documentElement.setAttribute('data-theme', this.dark ? 'dark' : 'light');
    },
    toggle() {
        this.dark = !this.dark;
        localStorage.setItem('theme', this.dark ? 'dark' : 'light');
        document.documentElement.setAttribute('data-theme', this.dark ? 'dark' : 'light');
    }
}));

Alpine.data('heroBanner', () => ({
    currentSlide: 0,
    slides: [],
    autoplayInterval: null,
    next() { this.currentSlide = (this.currentSlide + 1) % this.slides.length; },
    prev() { this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length; },
    goTo(index) { this.currentSlide = index; },
    startAutoplay() { this.autoplayInterval = setInterval(() => this.next(), 5000); },
    stopAutoplay() { clearInterval(this.autoplayInterval); },
    init() { this.startAutoplay(); }
}));

Alpine.data('modal', () => ({
    open: false,
    show() { this.open = true; document.body.style.overflow = 'hidden'; },
    hide() { this.open = false; document.body.style.overflow = ''; },
    init() { this.$watch('open', (val) => { if (!val) document.body.style.overflow = ''; }); }
}));

Alpine.data('mobileMenu', () => ({
    isOpen: false,
    toggle() { this.isOpen = !this.isOpen; },
    close() { this.isOpen = false; }
}));

Alpine.data('sellerSidebar', () => ({
    mobileOpen: false,
    toggle() { this.mobileOpen = !this.mobileOpen; },
    close() { this.mobileOpen = false; }
}));

Alpine.start();