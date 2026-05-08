export function cartCount() {
    return {
        count: 0,
        loading: false,

        async init() {
            await this.fetchCount();
            window.addEventListener('cart:updated', () => this.fetchCount());
        },

        async fetchCount() {
            this.loading = true;
            try {
                const res = await fetch('/api/cart/count', {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await res.json();
                this.count = data.count ?? 0;
            } catch {
                this.count = 0;
            }
            this.loading = false;
        },

        get hasItems() {
            return this.count > 0;
        },

        get displayCount() {
            return this.count > 99 ? '99+' : String(this.count);
        },
    };
}