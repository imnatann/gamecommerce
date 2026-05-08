export function imageGallery(initialImages = []) {
    return {
        images: Array.isArray(initialImages) ? initialImages : JSON.parse(initialImages),
        mainImage: '',
        mainIndex: 0,

        init() {
            if (this.images.length > 0) {
                this.mainImage = this.images[0];
                this.mainIndex = 0;
            }

            this.$watch('mainIndex', (idx) => {
                if (idx >= 0 && idx < this.images.length) {
                    this.mainImage = this.images[idx];
                }
            });
        },

        selectThumbnail(index) {
            if (index >= 0 && index < this.images.length) {
                this.mainIndex = index;
                this.mainImage = this.images[index];
            }
        },

        next() {
            const nextIndex = (this.mainIndex + 1) % this.images.length;
            this.selectThumbnail(nextIndex);
        },

        prev() {
            const prevIndex = (this.mainIndex - 1 + this.images.length) % this.images.length;
            this.selectThumbnail(prevIndex);
        },
    };
}