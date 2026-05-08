@extends('layouts.admin', ['title' => 'Games'])

@section('content')
<div x-data="adminGames()">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="font-[var(--font-family-display)] text-2xl lg:text-3xl font-bold text-[var(--color-gc-text)]">Games</h1>
            <p class="text-sm text-[var(--color-gc-text-secondary)] mt-1">Kelola katalog game yang tersedia</p>
        </div>
        <button @click="showAddModal = true" class="gc-btn gc-btn-primary gc-btn-md">
            <svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Game
        </button>
    </div>

    {{-- Search & Filters --}}
    <div class="gc-card p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <svg class="gc-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" x-model="search" @input="filterGames()" placeholder="Cari game..." class="gc-search-input h-10">
            </div>
            <select x-model="filterCategory" @change="filterGames()" class="h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)]">
                <option value="">Semua Kategori</option>
                <option value="moba">MOBA</option>
                <option value="battle_royale">Battle Royale</option>
                <option value="rpg">RPG</option>
                <option value="fps">FPS</option>
                <option value="mmorpg">MMORPG</option>
                <option value="casual">Casual</option>
            </select>
            <select x-model="filterStatus" @change="filterGames()" class="h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)]">
                <option value="">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>
            </select>
        </div>
    </div>

    {{-- Games Table --}}
    <div class="gc-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--color-gc-border)] bg-[var(--color-gc-bg-subtle)]">
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Icon</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Nama</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium gc-hide-mobile">Slug</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium gc-hide-mobile">Kategori</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Produk</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Status</th>
                        <th class="text-right p-4 text-[var(--color-gc-text-secondary)] font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 1; $i <= 12; $i++)
                    @php
                        $games = [
                            ['name' => 'Mobile Legends', 'slug' => 'mobile-legends', 'category' => 'MOBA', 'products' => rand(50, 500)],
                            ['name' => 'Free Fire', 'slug' => 'free-fire', 'category' => 'Battle Royale', 'products' => rand(30, 300)],
                            ['name' => 'Genshin Impact', 'slug' => 'genshin-impact', 'category' => 'RPG', 'products' => rand(20, 200)],
                            ['name' => 'PUBG Mobile', 'slug' => 'pubg-mobile', 'category' => 'Battle Royale', 'products' => rand(40, 400)],
                            ['name' => 'Valorant', 'slug' => 'valorant', 'category' => 'FPS', 'products' => rand(10, 100)],
                            ['name' => 'Honkai Star Rail', 'slug' => 'honkai-star-rail', 'category' => 'RPG', 'products' => rand(15, 150)],
                            ['name' => 'Roblox', 'slug' => 'roblox', 'category' => 'Casual', 'products' => rand(60, 600)],
                            ['name' => 'Call of Duty Mobile', 'slug' => 'cod-mobile', 'category' => 'FPS', 'products' => rand(20, 200)],
                            ['name' => 'Ragnarok Origin', 'slug' => 'ragnarok-origin', 'category' => 'MMORPG', 'products' => rand(10, 80)],
                            ['name' => 'Fortnite', 'slug' => 'fortnite', 'category' => 'Battle Royale', 'products' => rand(5, 50)],
                            ['name' => 'League of Legends', 'slug' => 'lol', 'category' => 'MOBA', 'products' => rand(8, 40)],
                            ['name' => 'Minecraft', 'slug' => 'minecraft', 'category' => 'Casual', 'products' => rand(5, 30)],
                        ];
                        $game = $games[$i - 1];
                        $isActive = $i % 5 !== 0;
                    @endphp
                    <tr class="border-b border-[var(--color-gc-border)] hover:bg-[var(--color-gc-hover)] transition-colors">
                        <td class="p-4">
                            <div class="w-10 h-10 rounded-lg bg-[var(--color-gc-bg-subtle)] flex items-center justify-center">
                                <svg class="w-6 h-6 text-[var(--color-gc-text-tertiary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="6" rx="2"/><path d="M6 12h4"/><path d="M8 10v4"/><line x1="16" y1="12" x2="16" y2="12.01"/><line x1="18" y1="10" x2="18" y2="10.01"/><line x1="18" y1="14" x2="18" y2="14.01"/><path d="M8 2v4"/><path d="M16 2v4"/></svg>
                            </div>
                        </td>
                        <td class="p-4 font-medium text-[var(--color-gc-text)]">{{ $game['name'] }}</td>
                        <td class="p-4 gc-hide-mobile font-mono text-xs text-[var(--color-gc-text-secondary)]">{{ $game['slug'] }}</td>
                        <td class="p-4 gc-hide-mobile"><span class="gc-badge bg-[var(--color-gc-primary)]/10 text-[var(--color-gc-primary)]">{{ $game['category'] }}</span></td>
                        <td class="p-4 text-[var(--color-gc-text)]">{{ $game['products'] }}</td>
                        <td class="p-4">
                            <button @click="games[{{ $i-1 }}].active = !games[{{ $i-1 }}].active" :class="games[{{ $i-1 }}].active ? 'bg-[var(--color-gc-accent)]' : 'bg-[var(--color-gc-border)]'" class="relative w-11 h-6 rounded-full transition-colors cursor-pointer" :aria-label="games[{{ $i-1 }}].active ? 'Nonaktifkan' : 'Aktifkan'">
                                <span :class="games[{{ $i-1 }}].active ? 'translate-x-5' : 'translate-x-0.5'" class="absolute top-0.5 left-0 w-5 h-5 bg-white rounded-full shadow transition-transform"></span>
                            </button>
                        </td>
                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button @click="editGame({{ $i - 1 }})" class="gc-btn gc-btn-ghost gc-btn-icon" aria-label="Edit">
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

    {{-- Add/Edit Game Modal --}}
    <div x-show="showAddModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.prevent="showAddModal = false">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showAddModal = false"></div>
        <div x-show="showAddModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="gc-modal-panel max-w-lg relative bg-[var(--color-gc-bg-elevated)]">
            <div class="gc-modal-header">
                <h3 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)]" x-text="editingGame ? 'Edit Game' : 'Tambah Game'">Tambah Game</h3>
                <button @click="showAddModal = false" class="gc-btn-icon gc-btn-ghost" aria-label="Close">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="gc-modal-body space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Nama Game <span class="text-[var(--color-gc-error)]">*</span></label>
                    <input type="text" x-model="form.name" placeholder="Contoh: Mobile Legends" class="w-full h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)] focus:ring-2 focus:ring-[var(--color-gc-primary)]/20">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Slug</label>
                    <input type="text" x-model="form.slug" placeholder="mobile-legends" class="w-full h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] font-mono focus:outline-none focus:border-[var(--color-gc-primary)] focus:ring-2 focus:ring-[var(--color-gc-primary)]/20">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Kategori</label>
                    <select x-model="form.category" class="w-full h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)]">
                        <option value="moba">MOBA</option>
                        <option value="battle_royale">Battle Royale</option>
                        <option value="rpg">RPG</option>
                        <option value="fps">FPS</option>
                        <option value="mmorpg">MMORPG</option>
                        <option value="casual">Casual</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Icon Game</label>
                    <div class="relative border-2 border-dashed border-[var(--color-gc-border)] rounded-xl p-6 text-center hover:border-[var(--color-gc-primary)] transition-colors cursor-pointer">
                        <svg class="w-8 h-8 mx-auto text-[var(--color-gc-text-tertiary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        <p class="text-sm text-[var(--color-gc-text-secondary)] mt-2">Upload icon</p>
                        <p class="text-xs text-[var(--color-gc-text-tertiary)]">PNG/SVG, 1:1 ratio</p>
                    </div>
                </div>
            </div>
            <div class="gc-modal-footer">
                <button @click="showAddModal = false" class="gc-btn gc-btn-ghost gc-btn-md">Batal</button>
                <button @click="showAddModal = false" class="gc-btn gc-btn-primary gc-btn-md" x-text="editingGame ? 'Simpan' : 'Tambah Game'">Tambah Game</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
Alpine.data('adminGames', () => ({
    search: '',
    filterCategory: '',
    filterStatus: '',
    showAddModal: false,
    editingGame: null,
    form: { name: '', slug: '', category: 'moba' },
    games: Array.from({length: 12}, (_, i) => ({ active: (i + 1) % 5 !== 0 })),
    editGame(index) {
        this.editingGame = index;
        this.showAddModal = true;
    },
    filterGames() {},
}));
</script>
@endpush
@endsection