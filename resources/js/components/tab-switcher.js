export function tabSwitcher(defaultTab = '', tabs = []) {
    return {
        activeTab: defaultTab || (tabs.length > 0 ? tabs[0] : ''),
        tabs: Array.isArray(tabs) ? tabs : JSON.parse(tabs),

        init() {
            if (!this.activeTab && this.tabs.length > 0) {
                this.activeTab = this.tabs[0];
            }

            const hash = window.location.hash.replace('#', '');
            if (hash && this.tabs.includes(hash)) {
                this.activeTab = hash;
            }
        },

        selectTab(tab) {
            this.activeTab = tab;
            window.location.hash = tab;
        },

        isActive(tab) {
            return this.activeTab === tab;
        },
    };
}