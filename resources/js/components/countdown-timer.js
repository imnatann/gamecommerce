export function countdownTimer(endTime) {
    return {
        endTime: new Date(endTime).getTime(),
        days: 0,
        hours: 0,
        minutes: 0,
        seconds: 0,
        expired: false,
        interval: null,

        init() {
            this.tick();
            this.interval = setInterval(() => this.tick(), 1000);

            this.$watch('expired', (val) => {
                if (val && this.interval) {
                    clearInterval(this.interval);
                }
            });
        },

        tick() {
            const now = Date.now();
            const diff = this.endTime - now;

            if (diff <= 0) {
                this.days = 0;
                this.hours = 0;
                this.minutes = 0;
                this.seconds = 0;
                this.expired = true;
                return;
            }

            this.days = Math.floor(diff / 86400000);
            this.hours = Math.floor((diff % 86400000) / 3600000);
            this.minutes = Math.floor((diff % 3600000) / 60000);
            this.seconds = Math.floor((diff % 60000) / 1000);
        },

        pad(n) {
            return String(n).padStart(2, '0');
        },

        destroy() {
            if (this.interval) {
                clearInterval(this.interval);
            }
        },
    };
}