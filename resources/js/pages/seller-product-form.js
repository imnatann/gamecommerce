export function sellerProductForm() {
    return {
        productType: '',
        name: '',
        description: '',
        price: '',
        originalPrice: '',
        stock: '',
        server: '',
        region: '',
        images: [],
        imagePreviews: [],
        errors: {},
        isSubmitting: false,
        maxImages: 5,

        serverOptions: [],
        regionOptions: [],

        productTypeConfig: {
            topup: {
                label: 'Top Up',
                fields: { server: true, region: true, stock: true, originalPrice: false, images: true },
                maxImages: 5,
            },
            game_key: {
                label: 'Game Key',
                fields: { server: false, region: true, stock: true, originalPrice: false, images: true },
                maxImages: 5,
            },
            akun: {
                label: 'Akun',
                fields: { server: false, region: false, stock: false, originalPrice: true, images: true },
                maxImages: 8,
            },
            item: {
                label: 'Item',
                fields: { server: true, region: true, stock: true, originalPrice: false, images: true },
                maxImages: 5,
            },
            voucher: {
                label: 'Voucher',
                fields: { server: false, region: true, stock: true, originalPrice: true, images: true },
                maxImages: 3,
            },
        },

        get config() {
            return this.productTypeConfig[this.productType] || null;
        },

        get showServer() {
            return this.config?.fields?.server ?? false;
        },

        get showRegion() {
            return this.config?.fields?.region ?? false;
        },

        get showStock() {
            return this.config?.fields?.stock ?? false;
        },

        get showOriginalPrice() {
            return this.config?.fields?.originalPrice ?? false;
        },

        async init() {
            await this.loadServers();
            await this.loadRegions();
        },

        async loadServers() {
            try {
                const res = await fetch('/api/servers', { headers: { 'Accept': 'application/json' } });
                this.serverOptions = await res.json();
            } catch {
                this.serverOptions = [];
            }
        },

        async loadRegions() {
            try {
                const res = await fetch('/api/regions', { headers: { 'Accept': 'application/json' } });
                this.regionOptions = await res.json();
            } catch {
                this.regionOptions = [];
            }
        },

        handleImageUpload(event) {
            const files = event.target.files;
            if (!files) return;

            const maxFiles = this.config?.maxImages ?? this.maxImages;

            for (const file of files) {
                if (this.images.length >= maxFiles) break;

                this.images.push(file);
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreviews.push(e.target.result);
                };
                reader.readAsDataURL(file);
            }

            event.target.value = '';
        },

        removeImage(index) {
            this.images.splice(index, 1);
            this.imagePreviews.splice(index, 1);
        },

        formatPriceInput() {
            const numeric = this.price.replace(/\D/g, '');
            this.price = numeric ? Number(numeric).toLocaleString('id-ID') : '';
        },

        get rawPrice() {
            return Number(this.price.replace(/\D/g, '')) || 0;
        },

        get rawOriginalPrice() {
            return Number(this.originalPrice.replace(/\D/g, '')) || 0;
        },

        validate() {
            this.errors = {};

            if (!this.name.trim()) this.errors.name = 'Nama produk wajib diisi';
            if (!this.productType) this.errors.productType = 'Tipe produk wajib dipilih';
            if (!this.rawPrice || this.rawPrice <= 0) this.errors.price = 'Harga wajib diisi';

            if (this.showStock && (!this.stock || Number(this.stock) <= 0)) {
                this.errors.stock = 'Stok wajib diisi';
            }
            if (this.showServer && !this.server) {
                this.errors.server = 'Server wajib dipilih';
            }
            if (this.showOriginalPrice && this.rawOriginalPrice > 0 && this.rawOriginalPrice <= this.rawPrice) {
                this.errors.originalPrice = 'Harga asal harus lebih besar dari harga jual';
            }

            return Object.keys(this.errors).length === 0;
        },

        async submit() {
            if (!this.validate()) return;
            this.isSubmitting = true;

            try {
                const formData = new FormData();
                formData.append('name', this.name);
                formData.append('description', this.description);
                formData.append('product_type', this.productType);
                formData.append('price', this.rawPrice);
                if (this.showOriginalPrice) formData.append('original_price', this.rawOriginalPrice);
                if (this.showStock) formData.append('stock', this.stock);
                if (this.showServer) formData.append('server', this.server);
                if (this.showRegion) formData.append('region', this.region);
                this.images.forEach((img, i) => formData.append(`images[${i}]`, img));

                const res = await fetch('/seller/products', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await res.json();
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            } catch {
                this.errors.submit = 'Gagal menyimpan produk. Coba lagi.';
            } finally {
                this.isSubmitting = false;
            }
        },
    };
}