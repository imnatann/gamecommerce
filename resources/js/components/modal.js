export function modal(initialOpen = false) {
    return {
        open: initialOpen,
        scrollPosition: 0,

        init() {
            this.$watch('open', (isOpen) => {
                if (isOpen) {
                    this.scrollPosition = window.scrollY;
                    document.body.style.overflow = 'hidden';
                    document.body.style.position = 'fixed';
                    document.body.style.top = `-${this.scrollPosition}px`;
                    document.body.style.width = '100%';
                    document.body.style.overscrollBehavior = 'none';
                } else {
                    document.body.style.overflow = '';
                    document.body.style.position = '';
                    document.body.style.top = '';
                    document.body.style.width = '';
                    document.body.style.overscrollBehavior = '';
                    window.scrollTo(0, this.scrollPosition);
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.open) {
                    this.close();
                }
            });
        },

        show() {
            this.open = true;
        },

        close() {
            this.open = false;
        },

        onBackdropClick(event) {
            if (event.target === event.currentTarget) {
                this.close();
            }
        },

        onKeydown(event) {
            if (event.key === 'Escape') {
                this.close();
            }
        },

        destroy() {
            document.body.style.overflow = '';
            document.body.style.position = '';
            document.body.style.top = '';
            document.body.style.width = '';
            document.body.style.overscrollBehavior = '';
        },
    };
}