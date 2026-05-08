export function copyButton(text = '') {
    return {
        textToCopy: text,
        copied: false,
        copyLabel: 'Salin',
        timer: null,

        async copy() {
            const text = this.textToCopy || this.$el?.dataset?.copyText || this.$refs?.copySource?.textContent || '';

            try {
                await navigator.clipboard.writeText(text);
            } catch {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
            }

            this.copied = true;
            this.copyLabel = 'Tersalin!';
            clearTimeout(this.timer);
            this.timer = setTimeout(() => {
                this.copied = false;
                this.copyLabel = 'Salin';
            }, 2000);
        },

        destroy() {
            clearTimeout(this.timer);
        },
    };
}