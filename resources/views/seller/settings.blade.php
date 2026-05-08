@extends('layouts.seller', ['title' => 'Pengaturan Toko'])

@section('content')
<div x-data="sellerSettings()" class="max-w-3xl">
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="font-[var(--font-family-display)] text-2xl lg:text-3xl font-bold text-[var(--color-gc-text)]">Pengaturan Toko</h1>
        <p class="text-sm text-[var(--color-gc-text-secondary)] mt-1">Kelola profil dan pengaturan toko Anda</p>
    </div>

    <form @submit.prevent="saveSettings()" class="space-y-6">
        @csrf

        {{-- Shop Profile --}}
        <div class="gc-card p-6">
            <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)] mb-4">Profil Toko</h2>
            <div class="space-y-4">
                {{-- Avatar --}}
                <div>
                    <label class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-2">Foto Toko</label>
                    <div class="flex items-center gap-4">
                        <div x-data="{ preview: '' }" class="relative">
                            <div class="w-20 h-20 rounded-2xl bg-[var(--color-gc-bg-subtle)] border-2 border-dashed border-[var(--color-gc-border)] flex items-center justify-center overflow-hidden" :class="preview ? 'border-[var(--color-gc-primary)]' : ''">
                                <img x-show="preview" :src="preview" class="w-full h-full object-cover" alt="Preview">
                                <svg x-show="!preview" class="w-8 h-8 text-[var(--color-gc-text-tertiary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                            <button type="button" @click="$refs.avatarInput.click()" class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-[var(--color-gc-primary)] text-white flex items-center justify-center shadow-lg">
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                            </button>
                            <input type="file" x-ref="avatarInput" @change="preview = URL.createObjectURL($event.target.files[0])" name="avatar" accept="image/*" class="hidden">
                        </div>
                        <div>
                            <p class="text-sm font-medium text-[var(--color-gc-text)]">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-[var(--color-gc-text-tertiary)]">JPG, PNG, max 2MB</p>
                        </div>
                    </div>
                </div>

                {{-- Shop Name --}}
                <div>
                    <label for="shop_name" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Nama Toko <span class="text-[var(--color-gc-error)]">*</span></label>
                    <input type="text" id="shop_name" x-model="form.shop_name" name="shop_name" required placeholder="Nama toko Anda" class="w-full h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)] focus:ring-2 focus:ring-[var(--color-gc-primary)]/20" value="{{ Auth::user()->name ?? '' }}">
                </div>

                {{-- Shop Description --}}
                <div>
                    <label for="shop_description" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Deskripsi Toko</label>
                    <textarea id="shop_description" x-model="form.shop_description" name="shop_description" rows="4" placeholder="Ceritakan tentang toko Anda..." class="w-full px-3 py-2 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)] focus:ring-2 focus:ring-[var(--color-gc-primary)]/20 resize-none" maxlength="500"></textarea>
                    <p class="text-xs text-[var(--color-gc-text-tertiary)] mt-1" x-text="(form.shop_description || '').length + '/500 karakter'"></p>
                </div>
            </div>
        </div>

        {{-- Shop Hours & Response --}}
        <div class="gc-card p-6">
            <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)] mb-4">Jam Operasional</h2>
            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="open_time" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Jam Buka</label>
                        <input type="time" id="open_time" x-model="form.open_time" name="open_time" class="w-full h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)] focus:ring-2 focus:ring-[var(--color-gc-primary)]/20" value="08:00">
                    </div>
                    <div>
                        <label for="close_time" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Jam Tutup</label>
                        <input type="time" id="close_time" x-model="form.close_time" name="close_time" class="w-full h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)] focus:ring-2 focus:ring-[var(--color-gc-primary)]/20" value="22:00">
                    </div>
                </div>
                <div>
                    <label for="response_time" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Waktu Respon Rata-rata</label>
                    <select id="response_time" x-model="form.response_time" name="response_time" class="w-full h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)]">
                        <option value="minutes">Dalam hitungan menit</option>
                        <option value="1hour">Dalam 1 jam</option>
                        <option value="few_hours">Dalam beberapa jam</option>
                        <option value="1day">Dalam 1 hari</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Notification Preferences --}}
        <div class="gc-card p-6">
            <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)] mb-4">Notifikasi</h2>
            <div class="space-y-4">
                <label class="flex items-center justify-between p-3 rounded-xl hover:bg-[var(--color-gc-hover)] transition-colors cursor-pointer">
                    <div>
                        <div class="text-sm font-medium text-[var(--color-gc-text)]">Pesanan Baru</div>
                        <div class="text-xs text-[var(--color-gc-text-tertiary)]">Notifikasi saat ada pesanan masuk</div>
                    </div>
                    <div class="relative">
                        <input type="checkbox" x-model="form.notify_new_order" name="notify_new_order" class="sr-only peer">
                        <div @click="form.notify_new_order = !form.notify_new_order" :class="form.notify_new_order ? 'bg-[var(--color-gc-primary)]' : 'bg-[var(--color-gc-border)]'" class="w-11 h-6 rounded-full transition-colors cursor-pointer"></div>
                        <div :class="form.notify_new_order ? 'translate-x-5' : 'translate-x-0.5'" class="absolute top-0.5 left-0 w-5 h-5 bg-white rounded-full shadow transition-transform"></div>
                    </div>
                </label>
                <label class="flex items-center justify-between p-3 rounded-xl hover:bg-[var(--color-gc-hover)] transition-colors cursor-pointer">
                    <div>
                        <div class="text-sm font-medium text-[var(--color-gc-text)]">Pembayaran Diterima</div>
                        <div class="text-xs text-[var(--color-gc-text-tertiary)]">Notifikasi saat pembayaran dikonfirmasi</div>
                    </div>
                    <div class="relative">
                        <input type="checkbox" x-model="form.notify_payment" name="notify_payment" class="sr-only peer">
                        <div @click="form.notify_payment = !form.notify_payment" :class="form.notify_payment ? 'bg-[var(--color-gc-primary)]' : 'bg-[var(--color-gc-border)]'" class="w-11 h-6 rounded-full transition-colors cursor-pointer"></div>
                        <div :class="form.notify_payment ? 'translate-x-5' : 'translate-x-0.5'" class="absolute top-0.5 left-0 w-5 h-5 bg-white rounded-full shadow transition-transform"></div>
                    </div>
                </label>
                <label class="flex items-center justify-between p-3 rounded-xl hover:bg-[var(--color-gc-hover)] transition-colors cursor-pointer">
                    <div>
                        <div class="text-sm font-medium text-[var(--color-gc-text)]">Dispute Baru</div>
                        <div class="text-xs text-[var(--color-gc-text-tertiary)]">Notifikasi saat pembeli mengajukan dispute</div>
                    </div>
                    <div class="relative">
                        <input type="checkbox" x-model="form.notify_dispute" name="notify_dispute" class="sr-only peer">
                        <div @click="form.notify_dispute = !form.notify_dispute" :class="form.notify_dispute ? 'bg-[var(--color-gc-primary)]' : 'bg-[var(--color-gc-border)]'" class="w-11 h-6 rounded-full transition-colors cursor-pointer"></div>
                        <div :class="form.notify_dispute ? 'translate-x-5' : 'translate-x-0.5'" class="absolute top-0.5 left-0 w-5 h-5 bg-white rounded-full shadow transition-transform"></div>
                    </div>
                </label>
                <label class="flex items-center justify-between p-3 rounded-xl hover:bg-[var(--color-gc-hover)] transition-colors cursor-pointer">
                    <div>
                        <div class="text-sm font-medium text-[var(--color-gc-text)]">Saldo Ditarik</div>
                        <div class="text-xs text-[var(--color-gc-text-tertiary)]">Notifikasi saat pencairan diproses</div>
                    </div>
                    <div class="relative">
                        <input type="checkbox" x-model="form.notify_withdrawal" name="notify_withdrawal" class="sr-only peer">
                        <div @click="form.notify_withdrawal = !form.notify_withdrawal" :class="form.notify_withdrawal ? 'bg-[var(--color-gc-primary)]' : 'bg-[var(--color-gc-border)]'" class="w-11 h-6 rounded-full transition-colors cursor-pointer"></div>
                        <div :class="form.notify_withdrawal ? 'translate-x-5' : 'translate-x-0.5'" class="absolute top-0.5 left-0 w-5 h-5 bg-white rounded-full shadow transition-transform"></div>
                    </div>
                </label>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('seller.dashboard') }}" class="gc-btn gc-btn-ghost gc-btn-md">Batal</a>
            <button type="submit" class="gc-btn gc-btn-primary gc-btn-md">
                <svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
Alpine.data('sellerSettings', () => ({
    form: {
        shop_name: '',
        shop_description: '',
        open_time: '08:00',
        close_time: '22:00',
        response_time: '1hour',
        notify_new_order: true,
        notify_payment: true,
        notify_dispute: true,
        notify_withdrawal: true,
    },
    saveSettings() {
        this.$el.closest('form').submit();
    },
}));
</script>
@endpush
@endsection