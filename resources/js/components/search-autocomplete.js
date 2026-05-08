export function searchAutocomplete() {
    return {
        query: '',
        gameResults: [],
        productResults: [],
        isOpen: false,
        selectedIndex: -1,
        recentSearches: [],
        showRecent: true,
        debounceTimer: null,

        init() {
            this.recentSearches = JSON.parse(localStorage.getItem('gc_recent_searches') || '[]');
            this.showRecent = this.recentSearches.length > 0;

            this.$refs.searchInput?.addEventListener('blur', () => {
                setTimeout(() => this.close(), 200);
            });

            document.addEventListener('keydown', (e) => {
                if (!this.isOpen) return;
                if (e.key === 'Escape') this.close();
            });
        },

        get allItems() {
            return [
                ...this.gameResults.map(r => ({ ...r, type: 'game' })),
                ...this.productResults.map(r => ({ ...r, type: 'product' })),
            ];
        },

        onInput() {
            clearTimeout(this.debounceTimer);
            if (this.query.length < 2) {
                this.isOpen = false;
                this.gameResults = [];
                this.productResults = [];
                this.showRecent = this.recentSearches.length > 0 && this.query.length === 0;
                return;
            }
            this.debounceTimer = setTimeout(() => this.search(), 300);
        },

        async search() {
            try {
                const res = await fetch(`/api/search?q=${encodeURIComponent(this.query)}&limit=5`);
                const data = await res.json();
                this.gameResults = data.games || [];
                this.productResults = data.products || [];
                this.isOpen = this.gameResults.length > 0 || this.productResults.length > 0;
                this.selectedIndex = -1;
            } catch {
                this.gameResults = [];
                this.productResults = [];
            }
        },

        select(item) {
            this.saveRecent(item.name || item.title);
            window.location.href = item.url;
        },

        navigateUp() {
            if (!this.isOpen) return;
            this.selectedIndex = Math.max(-1, this.selectedIndex - 1);
        },

        navigateDown() {
            if (!this.isOpen) return;
            this.selectedIndex = Math.min(this.allItems.length - 1, this.selectedIndex + 1);
        },

        enterSelect() {
            if (this.selectedIndex >= 0 && this.selectedIndex < this.allItems.length) {
                this.select(this.allItems[this.selectedIndex]);
            } else if (this.query.length >= 2) {
                this.saveRecent(this.query);
                window.location.href = `/search?q=${encodeURIComponent(this.query)}`;
            }
        },

        saveRecent(term) {
            if (!term) return;
            this.recentSearches = [term, ...this.recentSearches.filter(s => s !== term)].slice(0, 5);
            localStorage.setItem('gc_recent_searches', JSON.stringify(this.recentSearches));
        },

        clearRecent() {
            this.recentSearches = [];
            localStorage.removeItem('gc_recent_searches');
            this.showRecent = false;
        },

        removeRecent(index) {
            this.recentSearches.splice(index, 1);
            localStorage.setItem('gc_recent_searches', JSON.stringify(this.recentSearches));
            this.showRecent = this.recentSearches.length > 0;
        },

        close() {
            this.isOpen = false;
            this.selectedIndex = -1;
        },

        open() {
            if (this.query.length >= 2 && (this.gameResults.length > 0 || this.productResults.length > 0)) {
                this.isOpen = true;
            } else if (this.recentSearches.length > 0) {
                this.showRecent = true;
            }
        },
    };
}