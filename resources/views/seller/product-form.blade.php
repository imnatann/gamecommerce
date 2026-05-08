@extends('layouts.seller', ['title' => $product->id ? 'Edit Produk' : 'Tambah Produk'])

@section('content')
<div x-data="productForm({{ json_encode($product ?? null) }}, {{ json_encode($games ?? []) }})">
    {{-- Page Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('seller.products.index') }}" class="gc-btn gc-btn-ghost gc-btn-icon" aria-label="Back">
            <svg class="gc-icon-md" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
        </a>
        <div>
            <h1 class="font-[var(--font-family-display)] text-2xl lg:text-3xl font-bold text-[var(--color-gc-text)]" x-text="isEdit ? 'Edit Produk' : 'Tambah Produk'">Tambah Produk</h1>
            <p class="text-sm text-[var(--color-gc-text-secondary)]" x-text="isEdit ? 'Perbarui informasi produk Anda' : 'Isi detail produk baru'">Isi detail produk baru</p>
        </div>
    </div>

    <form @submit.prevent="submitForm()" class="space-y-6">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Basic Info --}}
                <div class="gc-card p-6">
                    <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)] mb-4">Informasi Dasar</h2>
                    <div class="space-y-4">
                        {{-- Game Selection --}}
                        <div>
                            <label class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Game <span class="text-[var(--color-gc-error)]">*</span></label>
                            <div class="relative" x-data="{ gameSearch: '', gameDropdown: false }">
                                <input type="text" x-model="gameSearch" @focus="gameDropdown = true" @input="filterGames(); gameDropdown = true" placeholder="Cari game..." class="w-full h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)] focus:ring-2 focus:ring-[var(--color-gc-primary)]/20" value="{{ $product->game->name ?? '' }}">
                                <input type="hidden" name="game_id" x-model="form.game_id">
                                <div x-show="gameDropdown" @click.away="gameDropdown = false" class="gc-search-dropdown">
                                    <template x-for="game in filteredGames" :key="game.id">
                                        <button type="button" @click="selectGame(game); gameSearch = game.name; gameDropdown = false" class="gc-search-result-item">
                                            <img :src="game.icon" :alt="game.name" class="w-8 h-8 rounded-lg object-cover bg-[var(--color-gc-bg-subtle)]">
                                            <span class="text-sm text-[var(--color-gc-text)]" x-text="game.name"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Product Type --}}
                        <div>
                            <label class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Tipe Produk <span class="text-[var(--color-gc-error)]">*</span></label>
                            <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                                <template x-for="pt in productTypes" :key="pt.value">
                                    <button type="button" @click="form.type = pt.value" :class="form.type === pt.value ? 'border-[var(--color-gc-primary)] bg-[var(--color-gc-primary)]/10 text-[var(--color-gc-primary)]' : 'border-[var(--color-gc-border)] text-[var(--color-gc-text-secondary)] hover:border-[var(--color-gc-border-hover)]'" class="px-3 py-2.5 rounded-xl border text-sm font-medium transition-all text-center">
                                        <span x-text="pt.icon" class="block text-lg mb-0.5"></span>
                                        <span x-text="pt.label" class="block text-xs"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- Product Name --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Nama Produk <span class="text-[var(--color-gc-error)]">*</span></label>
                            <input type="text" id="name" x-model="form.name" name="name" required placeholder="Contoh: 86 Diamond Mobile Legends" class="w-full h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)] focus:ring-2 focus:ring-[var(--color-gc-primary)]/20" value="{{ $product->name ?? '' }}">
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Deskripsi</label>
                            <textarea id="description" name="description" rows="4" placeholder="Deskripsi detail produk..." class="w-full px-3 py-2 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)] focus:ring-2 focus:ring-[var(--color-gc-primary)]/20 resize-none">{{ $product->description ?? '' }}</textarea>
                        </div>

                        {{-- Server & Region --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-show="showServerFields">
                            <div>
                                <label for="server" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Server</label>
                                <input type="text" id="server" name="server" placeholder="Contoh: Indonesia" class="w-full h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)] focus:ring-2 focus:ring-[var(--color-gc-primary)]/20" value="{{ $product->server ?? '' }}">
                            </div>
                            <div>
                                <label for="region" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Region</label>
                                <input type="text" id="region" name="region" placeholder="Contoh: SEA" class="w-full h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)] focus:ring-2 focus:ring-[var(--color-gc-primary)]/20" value="{{ $product->region ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Required Info (dynamic based on type) --}}
                <div class="gc-card p-6" x-show="form.type">
                    <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)] mb-4">
                        <span x-text="requiredInfoTitle">Informasi yang Dibutuhkan</span>
                    </h2>
                    <p class="text-sm text-[var(--color-gc-text-tertiary)] mb-4">Field yang harus diisi pembeli saat checkout</p>
                    <div class="space-y-3">
                        <template x-for="(field, index) in requiredFields" :key="index">
                            <div class="flex items-center gap-3 p-3 rounded-xl bg-[var(--color-gc-bg-subtle)] border border-[var(--color-gc-border)]">
                                <div class="flex-1">
                                    <input type="text" x-model="field.label" :name="'required_fields[' + index + '][label]'" placeholder="Nama field..." class="w-full h-8 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-lg text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)]">
                                </div>
                                <select x-model="field.type" :name="'required_fields[' + index + '][type]'" class="h-8 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-lg text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)]">
                                    <option value="text">Teks</option>
                                    <option value="number">Nomor</option>
                                    <option value="select">Pilihan</option>
                                </select>
                                <div class="flex items-center">
                                    <input type="checkbox" x-model="field.required" :name="'required_fields[' + index + '][required]'" class="rounded border-[var(--color-gc-border)] bg-[var(--color-gc-bg-card)]">
                                    <span class="text-xs text-[var(--color-gc-text-tertiary)] ml-1">Wajib</span>
                                </div>
                                <button type="button" @click="removeRequiredField(index)" class="text-[var(--color-gc-error)] hover:text-[var(--color-gc-error)]/80 transition-colors">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </div>
                        </template>
                        <button type="button" @click="addRequiredField()" class="gc-btn gc-btn-outline gc-btn-sm w-full">
                            <svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Tambah Field
                        </button>
                    </div>
                </div>

                {{-- Delivery & Auto-Delivery Data --}}
                <div class="gc-card p-6">
                    <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)] mb-4">Pengiriman & Stok</h2>
                    <div class="space-y-4">
                        {{-- Delivery Type --}}
                        <div>
                            <label class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Jenis Pengiriman <span class="text-[var(--color-gc-error)]">*</span></label>
                            <div class="grid grid-cols-3 gap-3">
                                <button type="button" @click="form.delivery_type = 'instant'" :class="form.delivery_type === 'instant' ? 'border-[var(--color-gc-primary)] bg-[var(--color-gc-primary)]/10 text-[var(--color-gc-primary)]' : 'border-[var(--color-gc-border)] text-[var(--color-gc-text-secondary)]'" class="p-3 rounded-xl border text-center transition-all">
                                    <svg class="w-6 h-6 mx-auto mb-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                                    <span class="text-xs font-medium">Instan</span>
                                </button>
                                <button type="button" @click="form.delivery_type = 'manual'" :class="form.delivery_type === 'manual' ? 'border-[var(--color-gc-primary)] bg-[var(--color-gc-primary)]/10 text-[var(--color-gc-primary)]' : 'border-[var(--color-gc-border)] text-[var(--color-gc-text-secondary)]'" class="p-3 rounded-xl border text-center transition-all">
                                    <svg class="w-6 h-6 mx-auto mb-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <span class="text-xs font-medium">Manual</span>
                                </button>
                                <button type="button" @click="form.delivery_type = 'login'" :class="form.delivery_type === 'login' ? 'border-[var(--color-gc-primary)] bg-[var(--color-gc-primary)]/10 text-[var(--color-gc-primary)]' : 'border-[var(--color-gc-border)] text-[var(--color-gc-text-secondary)]'" class="p-3 rounded-xl border text-center transition-all">
                                    <svg class="w-6 h-6 mx-auto mb-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                    <span class="text-xs font-medium">Login</span>
                                </button>
                            </div>
                        </div>

                        {{-- Stock --}}
                        <div>
                            <label for="stock" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Stok</label>
                            <div class="flex items-center gap-3">
                                <button type="button" @click="form.stock > 0 && form.stock--" class="gc-btn gc-btn-outline gc-btn-icon">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                </button>
                                <input type="number" id="stock" x-model="form.stock" name="stock" min="0" class="w-24 h-10 text-center bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)]" value="{{ $product->stock ?? 0 }}">
                                <button type="button" @click="form.stock++" class="gc-btn gc-btn-outline gc-btn-icon">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                </button>
                                <label class="flex items-center gap-2 text-sm text-[var(--color-gc-text-secondary)] ml-2">
                                    <input type="checkbox" x-model="form.unlimited_stock" name="unlimited_stock" class="rounded border-[var(--color-gc-border)] bg-[var(--color-gc-bg-card)]">
                                    Stok tidak terbatas
                                </label>
                            </div>
                        </div>

                        {{-- Product Data (for auto-delivery serial keys/vouchers) --}}
                        <div x-show="form.delivery_type === 'instant'">
                            <label class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Data Produk (Serial/Voucher)</label>
                            <p class="text-xs text-[var(--color-gc-text-tertiary)] mb-2">Satu per baris. Akan dikirim otomatis ke pembeli.</p>
                            <textarea x-model="form.serial_data" name="serial_data" rows="6" placeholder="CODE-001&#10;CODE-002&#10;CODE-003" class="w-full px-3 py-2 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] font-mono focus:outline-none focus:border-[var(--color-gc-primary)] focus:ring-2 focus:ring-[var(--color-gc-primary)]/20 resize-none"></textarea>
                            <p class="text-xs text-[var(--color-gc-text-tertiary)] mt-1" x-text="form.serial_data.split('\n').filter(l => l.trim()).length + ' kode tersedia'"></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Pricing --}}
                <div class="gc-card p-6">
                    <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)] mb-4">Harga</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="price" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Harga Jual <span class="text-[var(--color-gc-error)]">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-[var(--color-gc-text-tertiary)]">Rp</span>
                                <input type="number" id="price" name="price" x-model="form.price" required min="0" class="w-full h-10 pl-9 pr-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)] focus:ring-2 focus:ring-[var(--color-gc-primary)]/20" value="{{ $product->price ?? '' }}">
                            </div>
                        </div>
                        <div>
                            <label for="original_price" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Harga Asli (Coret)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-[var(--color-gc-text-tertiary)]">Rp</span>
                                <input type="number" id="original_price" name="original_price" x-model="form.original_price" min="0" class="w-full h-10 pl-9 pr-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)] focus:ring-2 focus:ring-[var(--color-gc-primary)]/20" value="{{ $product->original_price ?? '' }}">
                            </div>
                            <p class="text-xs text-[var(--color-gc-text-tertiary)] mt-1">Kosongkan jika tidak ada diskon</p>
                        </div>
                        <div x-show="form.original_price && form.price && Number(form.original_price) > Number(form.price)" class="p-3 rounded-xl bg-[var(--color-gc-warning)]/10 border border-[var(--color-gc-warning)]/20">
                            <div class="text-sm font-bold text-[var(--color-gc-warning)]">
                                Diskon <span x-text="Math.round((1 - form.price / form.original_price) * 100) + '%'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Image Upload --}}
                <div class="gc-card p-6">
                    <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)] mb-4">Gambar Produk</h2>
                    <div x-data="{ preview: '{{ $product->image ?? '' }}' }" class="space-y-3">
                        <div @dragover.prevent @drop.prevent="handleDrop($event)" class="relative border-2 border-dashed border-[var(--color-gc-border)] rounded-xl p-6 text-center hover:border-[var(--color-gc-primary)] transition-colors cursor-pointer" @click="$refs.fileInput.click()">
                            <div x-show="!preview" class="space-y-2">
                                <svg class="w-10 h-10 mx-auto text-[var(--color-gc-text-tertiary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                <p class="text-sm text-[var(--color-gc-text-secondary)]">Drag & drop atau klik untuk upload</p>
                                <p class="text-xs text-[var(--color-gc-text-tertiary)]">PNG, JPG, WebP (max 2MB)</p>
                            </div>
                            <div x-show="preview" class="relative">
                                <img :src="preview" class="max-h-40 mx-auto rounded-lg object-contain" alt="Preview">
                                <button type="button" @click="preview = ''" class="absolute top-2 right-2 w-6 h-6 bg-[var(--color-gc-error)] text-white rounded-full flex items-center justify-center">
                                    <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </div>
                            <input type="file" x-ref="fileInput" @change="preview = URL.createObjectURL($event.target.files[0])" name="image" accept="image/*" class="hidden">
                        </div>
                    </div>
                </div>

                {{-- Status --}}
                <div class="gc-card p-6">
                    <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)] mb-4">Status</h2>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 p-3 rounded-xl cursor-pointer" :class="form.status === 'active' ? 'bg-[var(--color-gc-accent)]/10 border border-[var(--color-gc-accent)]' : 'bg-[var(--color-gc-bg-subtle)] border border-[var(--color-gc-border)]'" @click="form.status = 'active'">
                            <input type="radio" x-model="form.status" value="active" name="status" class="text-[var(--color-gc-accent)]">
                            <div>
                                <div class="text-sm font-medium text-[var(--color-gc-text)]">Aktif</div>
                                <div class="text-xs text-[var(--color-gc-text-tertiary)]">Produk bisa dilihat pembeli</div>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl cursor-pointer" :class="form.status === 'draft' ? 'bg-[var(--color-gc-bg-hover)] border border-[var(--color-gc-primary)]' : 'bg-[var(--color-gc-bg-subtle)] border border-[var(--color-gc-border)]'" @click="form.status = 'draft'">
                            <input type="radio" x-model="form.status" value="draft" name="status" class="text-[var(--color-gc-primary)]">
                            <div>
                                <div class="text-sm font-medium text-[var(--color-gc-text)]">Draft</div>
                                <div class="text-xs text-[var(--color-gc-text-tertiary)]">Simpan sebagai draft</div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex gap-3">
                    <button type="submit" class="gc-btn gc-btn-primary gc-btn-md gc-btn-full">
                        <svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        <span x-text="isEdit ? 'Simpan Perubahan' : 'Buat Produk'">Buat Produk</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
Alpine.data('productForm', (product, games) => ({
    isEdit: !!product?.id,
    form: {
        game_id: product?.game_id ?? '',
        type: product?.type ?? 'top_up',
        name: product?.name ?? '',
        description: product?.description ?? '',
        price: product?.price ?? '',
        original_price: product?.original_price ?? '',
        server: product?.server ?? '',
        region: product?.region ?? '',
        delivery_type: product?.delivery_type ?? 'instant',
        stock: product?.stock ?? 0,
        unlimited_stock: product?.unlimited_stock ?? false,
        status: product?.status ?? 'active',
        serial_data: product?.serial_data ?? '',
    },
    productTypes: [
        { value: 'top_up', label: 'Top Up', icon: '⚡' },
        { value: 'game_key', label: 'Game Key', icon: '🔑' },
        { value: 'item', label: 'Item', icon: '💎' },
        { value: 'akun', label: 'Akun', icon: '👤' },
        { value: 'voucher', label: 'Voucher', icon: '🎫' },
        { value: 'joki', label: 'Joki', icon: '🎮' },
        { value: 'koin', label: 'Koin', icon: '🪙' },
    ],
    requiredFields: [],
    filteredGames: games || [],
    get showServerFields() {
        return ['top_up', 'item', 'joki', 'koin'].includes(this.form.type);
    },
    get requiredInfoTitle() {
        const titles = {
            top_up: 'Data Akun Game',
            game_key: 'Kode Kunci',
            item: 'Detail Item',
            akun: 'Data Login Akun',
            voucher: 'Kode Voucher',
            joki: 'Data Akun untuk Joki',
            koin: 'Data Akun Game',
        };
        return titles[this.form.type] || 'Informasi yang Dibutuhkan';
    },
    filterGames() {},
    selectGame(game) {
        this.form.game_id = game.id;
    },
    addRequiredField() {
        this.requiredFields.push({ label: '', type: 'text', required: true });
    },
    removeRequiredField(index) {
        this.requiredFields.splice(index, 1);
    },
    submitForm() {
        this.$el.closest('form').submit();
    }
}));
</script>
@endpush
@endsection