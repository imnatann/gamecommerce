export function checkout() {
    return {
        step: 1,
        paymentMethod: '',
        voucherCode: '',
        voucherDiscount: 0,
        voucherApplied: false,
        voucherError: '',
        subtotal: 0,
        adminFee: 0,
        total: 0,
        isSubmitting: false,

        init() {
            this.subtotal = Number(this.$el?.dataset?.subtotal || 0);
            this.recalculate();
        },

        setPaymentMethod(method) {
            this.paymentMethod = method;
            this.adminFee = this.calculateAdminFee(method);
            this.recalculate();
        },

        calculateAdminFee(method) {
            const fees = {
                bank_transfer: 0,
                bca_va: 4000,
                bni_va: 4000,
                bri_va: 4000,
                mandiri_va: 4000,
                gopay: 0,
                ovo: 0,
                dana: 0,
                shopeepay: 0,
                qris: 0,
                credit_card: Math.round(this.subtotal * 0.03),
            };
            return fees[method] ?? 0;
        },

        async applyVoucher() {
            if (!this.voucherCode.trim()) return;
            this.voucherError = '';
            this.voucherApplied = false;

            try {
                const res = await fetch('/api/voucher/apply', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify({ code: this.voucherCode, subtotal: this.subtotal }),
                });

                const data = await res.json();

                if (data.success) {
                    this.voucherDiscount = data.discount || 0;
                    this.voucherApplied = true;
                } else {
                    this.voucherError = data.message || 'Kode voucher tidak valid';
                    this.voucherDiscount = 0;
                    this.voucherApplied = false;
                }
            } catch {
                this.voucherError = 'Gagal menerapkan voucher. Coba lagi.';
            }

            this.recalculate();
        },

        removeVoucher() {
            this.voucherCode = '';
            this.voucherDiscount = 0;
            this.voucherApplied = false;
            this.voucherError = '';
            this.recalculate();
        },

        recalculate() {
            this.total = this.subtotal + this.adminFee - this.voucherDiscount;
            if (this.total < 0) this.total = 0;
        },

        get formattedSubtotal() {
            return this.formatCurrency(this.subtotal);
        },

        get formattedAdminFee() {
            return this.formatCurrency(this.adminFee);
        },

        get formattedDiscount() {
            return this.formatCurrency(this.voucherDiscount);
        },

        get formattedTotal() {
            return this.formatCurrency(this.total);
        },

        formatCurrency(amount) {
            return 'Rp ' + Math.round(amount).toLocaleString('id-ID');
        },

        async submitOrder() {
            if (!this.paymentMethod) return;
            this.isSubmitting = true;

            try {
                const res = await fetch('/checkout/process', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify({
                        payment_method: this.paymentMethod,
                        voucher_code: this.voucherApplied ? this.voucherCode : null,
                        subtotal: this.subtotal,
                        admin_fee: this.adminFee,
                        discount: this.voucherDiscount,
                        total: this.total,
                    }),
                });

                const data = await res.json();
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            } catch {
                alert('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                this.isSubmitting = false;
            }
        },
    };
}