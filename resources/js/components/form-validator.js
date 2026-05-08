export function formValidator(rules = {}) {
    return {
        rules: typeof rules === 'string' ? JSON.parse(rules) : rules,
        values: {},
        errors: {},
        touched: {},
        isValid: false,
        isSubmitting: false,

        init() {
            this.initializeValues();
            this.$watch('values', () => this.validateIfTouched(), { deep: true });
        },

        initializeValues() {
            for (const field in this.rules) {
                this.values[field] = '';
                this.errors[field] = '';
                this.touched[field] = false;
            }
        },

        validateIfTouched() {
            for (const field in this.touched) {
                if (this.touched[field]) {
                    this.validateField(field);
                }
            }
            this.checkValidity();
        },

        validateField(field) {
            const value = this.values[field] ?? '';
            const fieldRules = this.rules[field];
            if (!fieldRules) return true;

            this.errors[field] = '';

            for (const rule of fieldRules) {
                const error = this.applyRule(rule, field, value);
                if (error) {
                    this.errors[field] = error;
                    return false;
                }
            }
            return true;
        },

        applyRule(rule, field, value) {
            const trimmed = String(value).trim();

            if (rule.type === 'required' && !trimmed) {
                return rule.message || `${field} wajib diisi`;
            }
            if (rule.type === 'email' && trimmed && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(trimmed)) {
                return rule.message || 'Format email tidak valid';
            }
            if (rule.type === 'phone' && trimmed && !/^(\+62|62|0)[0-9]{8,13}$/.test(trimmed.replace(/[\s-]/g, ''))) {
                return rule.message || 'Format nomor telepon tidak valid';
            }
            if (rule.type === 'minLength' && trimmed.length < rule.value) {
                return rule.message || `Minimal ${rule.value} karakter`;
            }
            if (rule.type === 'maxLength' && trimmed.length > rule.value) {
                return rule.message || `Maksimal ${rule.value} karakter`;
            }
            if (rule.type === 'password' && trimmed && !/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/.test(trimmed)) {
                return rule.message || 'Password minimal 8 karakter, mengandung huruf besar, huruf kecil, dan angka';
            }
            if (rule.type === 'match' && trimmed !== this.values[rule.field]) {
                return rule.message || `Tidak cocok dengan ${rule.field}`;
            }
            if (rule.type === 'pattern' && trimmed && !rule.value.test(trimmed)) {
                return rule.message || rule.message || 'Format tidak valid';
            }
            return null;
        },

        blur(field) {
            this.touched[field] = true;
            this.validateField(field);
            this.checkValidity();
        },

        validateAll() {
            let allValid = true;
            for (const field in this.rules) {
                this.touched[field] = true;
                if (!this.validateField(field)) {
                    allValid = false;
                }
            }
            this.checkValidity();
            return allValid;
        },

        checkValidity() {
            this.isValid = Object.values(this.errors).every(e => e === '') &&
                           Object.values(this.touched).some(t => t === true) &&
                           Object.values(this.values).some(v => String(v).trim() !== '');
        },

        getError(field) {
            return this.errors[field] || '';
        },

        hasError(field) {
            return this.touched[field] && this.errors[field] !== '';
        },

        handleSubmit(callback) {
            if (!this.validateAll()) return;
            this.isSubmitting = true;
            Promise.resolve(callback(this.values)).finally(() => {
                this.isSubmitting = false;
            });
        },
    };
}