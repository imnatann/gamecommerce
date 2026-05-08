@extends('layouts.admin', ['title' => 'Manajemen Banner'])

@section('content')
<div x-data="adminBanners()" x-init="initReorder()">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="font-[var(--font-family-display)] text-2xl lg:text-3xl font-bold text-[var(--color-gc-text)]">Banner</h1>
            <p class="text-sm text-[var(--color-gc-text-secondary)] mt-1">Kelola banner promosi di homepage</p>
        </div>
        <button @click="showAddModal = true" class="gc-btn gc-btn-primary gc-btn-md">
            <svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Banner
        </button>
    </div>

    {{-- Banners Table --}}
    <div class="gc-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--color-gc-border)] bg-[var(--color-gc-bg-subtle)]">
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Banner</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium gc-hide-mobile">Posisi</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium gc-hide-mobile">Periode</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Urutan</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Status</th>
                        <th class="text-right p-4 text-[var(--color-gc-text-secondary)] font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody id="banners-table-body">
                    @for($i = 1; $i <= 6; $i++)
                    @php
                        $positions = ['Hero', 'Sidebar', 'Flash Sale', 'Category', 'Hero', 'Category'];
                        $titles = ['GG Deal 2025', 'Promo MLBB', 'Flash Sale Minggu Ini', 'Top Up Termurah', 'New Game Release', 'Valentine Sale'];
                        $isActive = $i <= 4;
                    @endphp
                    <tr class="border-b border-[var(--color-gc-border)] hover:bg-[var(--color-gc-hover)] transition-colors" data-id="{{ $i }}">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <button class="drag-handle gc-btn gc-btn-ghost gc-btn-icon p-0 cursor-grab active:cursor-grabbing" aria-label="Drag to reorder">
                                    <svg class="w-4 h-4 text-[var(--color-gc-text-tertiary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="5" r="1"/><circle cx="9" cy="12" r="1"/><circle cx="9" cy="19" r="1"/><circle cx="15" cy="5" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="15" cy="19" r="1"/></svg>
                                </button>
                                <div class="w-24 h-14 rounded-lg bg-[var(--color-gc-bg-subtle)] border border-[var(--color-gc-border)] flex items-center justify-center flex-shrink-0 overflow-hidden">
                                    <div class="w-full h-full bg-gradient-to-r from-[var(--color-gc-primary)] to-[var(--color-gc-accent)]/50 flex items-center justify-center">
                                        <span class="text-white text-xs font-bold">{{ $titles[$i-1] }}</span>
                                    </div>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-[var(--color-gc-text)]">{{ $titles[$i-1] }}</div>
                                    <div class="text-xs text-[var(--color-gc-text-tertiary)] truncate">{{ $positions[$i-1] }} Banner</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 gc-hide-mobile"><span class="gc-badge bg-[var(--color-gc-primary)]/10 text-[var(--color-gc-primary)]">{{ $positions[$i-1] }}</span></td>
                        <td class="p-4 gc-hide-mobile text-[var(--color-gc-text-secondary)] text-xs">{{ now()->subDays(rand(0, 10))->format('d M') }} - {{ now()->addDays(rand(5, 30))->format('d M Y') }}</td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button class="gc-btn gc-btn-ghost gc-btn-icon p-1" aria-label="Move up">
                                    <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
                                </button>
                                <span class="text-sm font-medium text-[var(--color-gc-text)] w-6 text-center">{{ $i }}</span>
                                <button class="gc-btn gc-btn-ghost gc-btn-icon p-1" aria-label="Move down">
                                    <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                                </button>
                            </div>
                        </td>
                        <td class="p-4">
                            <button @click="$data.active = !$data.active" x-data="{ active: {{ $isActive ? 'true' : 'false' }} }" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors" :class="active ? 'bg-[var(--color-gc-accent)]' : 'bg-[var(--color-gc-border)]'" role="switch" :aria-checked="active.toString()">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform" :class="active ? 'translate-x-6' : 'translate-x-0.5'"></span>
                            </button>
                        </td>
                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button class="gc-btn gc-btn-ghost gc-btn-icon" aria-label="Edit">
                                    <svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 21l.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                </button>
                                <button class="gc-btn gc-btn-ghost gc-btn-icon text-[var(--color-gc-error)]" aria-label="Delete">
                                    <svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add/Edit Banner Modal --}}
    <div x-show="showAddModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.prevent="showAddModal = false">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showAddModal = false"></div>
        <div x-show="showAddModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="gc-modal-panel max-w-lg relative bg-[var(--color-gc-bg-elevated)]">
            <div class="gc-modal-header">
                <h3 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)]" x-text="editingBanner ? 'Edit Banner' : 'Tambah Banner'">Tambah Banner</h3>
                <button @click="showAddModal = false; editingBanner = null" class="gc-btn-icon gc-btn-ghost" aria-label="Close">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="gc-modal-body space-y-4">
                <div>
                    <label for="banner_title" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Judul <span class="text-[var(--color-gc-error)]">*</span></label>
                    <input type="text" id="banner_title" x-model="form.title" class="w-full h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)] focus:ring-2 focus:ring-[var(--color-gc-primary)]/20" placeholder="Contoh: Flash Sale Akhir Tahun">
                </div>
                <div>
                    <label for="banner_subtitle" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Subtitle</label>
                    <input type="text" id="banner_subtitle" x-model="form.subtitle" class="w-full h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)] focus:ring-2 focus:ring-[var(--color-gc-primary)]/20" placeholder="Contoh: Diskon hingga 50% untuk semua game">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Gambar Banner</label>
                    <div x-data="{ preview: '' }" class="border-2 border-dashed border-[var(--color-gc-border)] rounded-xl p-6 text-center hover:border-[var(--color-gc-primary)] transition-colors cursor-pointer" @click="$refs.bannerInput.click()">
                        <div x-show="!preview" class="space-y-2">
                            <svg class="w-10 h-10 mx-auto text-[var(--color-gc-text-tertiary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            <p class="text-sm text-[var(--color-gc-text-secondary)]">Upload gambar banner</p>
                            <p class="text-xs text-[var(--color-gc-text-tertiary)]">Rekomendasi 1200x400px, max 2MB</p>
                        </div>
                        <img x-show="preview" :src="preview" class="max-h-40 mx-auto rounded-lg object-contain" alt="Preview">
                        <input type="file" x-ref="bannerInput" @change="preview = URL.createObjectURL($event.target.files[0])" name="image" accept="image/*" class="hidden">
                    </div>
                </div>
                <div>
                    <label for="banner_link" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Link Tujuan</label>
                    <input type="url" id="banner_link" x-model="form.link" class="w-full h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)] focus:ring-2 focus:ring-[var(--color-gc-primary)]/20" placeholder="https://example.com/promo">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="banner_position" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Posisi</label>
                        <select id="banner_position" x-model="form.position" class="w-full h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)]">
                            <option value="hero">Hero</option>
                            <option value="sidebar">Sidebar</option>
                            <option value="flash_sale">Flash Sale</option>
                            <option value="category">Category</option>
                        </select>
                    </div>
                    <div>
                        <label for="banner_sort" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Urutan</label>
                        <input type="number" id="banner_sort" x-model="form.sort" min="1" class="w-full h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)] focus:ring-2 focus:ring-[var(--color-gc-primary)]/20" value="1">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="banner_start" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Tanggal Mulai</label>
                        <input type="date" id="banner_start" x-model="form.start_date" class="w-full h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)]">
                    </div>
                    <div>
                        <label for="banner_end" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Tanggal Selesai</label>
                        <input type="date" id="banner_end" x-model="form.end_date" class="w-full h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)]">
                    </div>
                </div>
            </div>
            <div class="gc-modal-footer">
                <button @click="showAddModal = false; editingBanner = null" class="gc-btn gc-btn-ghost gc-btn-md">Batal</button>
                <button @click="showAddModal = false" class="gc-btn gc-btn-primary gc-btn-md">Simpan</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
<script>
Alpine.data('adminBanners', () => ({
    showAddModal: false,
    editingBanner: null,
    form: {
        title: '',
        subtitle: '',
        link: '',
        position: 'hero',
        sort: 1,
        start_date: '',
        end_date: '',
    },
    initReorder() {
        this.$nextTick(() => {
            const tbody = document.getElementById('banners-table-body');
            if (tbody) {
                Sortable.create(tbody, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'opacity-50',
                    onEnd: function(evt) {
                        const items = [];
                        tbody.querySelectorAll('tr').forEach((row, index) => {
                            items.push({ id: row.dataset.id, sort_order: index + 1 });
                        });
                        fetch('{{ route("admin.banners.reorder") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({ orders: items }),
                        });
                    }
                });
            }
        });
    }
}));
</script>
@endpush
@endsection