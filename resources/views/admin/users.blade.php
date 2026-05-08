@extends('layouts.admin', ['title' => 'Pengguna'])

@section('content')
<div x-data="adminUsers()">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="font-[var(--font-family-display)] text-2xl lg:text-3xl font-bold text-[var(--color-gc-text)]">Pengguna</h1>
            <p class="text-sm text-[var(--color-gc-text-secondary)] mt-1">Kelola semua pengguna platform</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="gc-card p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <svg class="gc-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" x-model="search" placeholder="Cari nama atau email..." class="gc-search-input h-10">
            </div>
            <select x-model="filterRole" class="h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)]">
                <option value="">Semua Role</option>
                <option value="buyer">Buyer</option>
                <option value="seller">Seller</option>
                <option value="admin">Admin</option>
            </select>
            <select x-model="filterKYC" class="h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)]">
                <option value="">Semua KYC</option>
                <option value="verified">Terverifikasi</option>
                <option value="pending">Pending</option>
                <option value="rejected">Ditolak</option>
                <option value="none">Belum Verifikasi</option>
            </select>
            <select x-model="filterStatus" class="h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)]">
                <option value="">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="banned">Banned</option>
            </select>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="gc-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--color-gc-border)] bg-[var(--color-gc-bg-subtle)]">
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">User</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium gc-hide-mobile">Email</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Role</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium gc-hide-mobile">KYC</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium gc-hide-mobile">Tgl Daftar</th>
                        <th class="text-right p-4 text-[var(--color-gc-text-secondary)] font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 1; $i <= 15; $i++)
                    @php
                        $names = ['Ahmad Fauzi', 'Siti Nurhaliza', 'Budi Santoso', 'Rina Wati', 'Joko Widodo', 'Maya Putri', 'Rizky Pratama', 'Dewi Lestari', 'Fajar Nugroho', 'Anisa Rahma', 'Tono Sugiarto', 'Lina Marlina', 'Eko Prasetyo', 'Fitri Handayani', 'Agus Setiawan'];
                        $roles = ['buyer', 'seller', 'buyer', 'seller', 'buyer', 'seller', 'buyer', 'admin'];
                        $kycStatuses = ['verified', 'pending', 'verified', 'rejected', 'verified', 'pending', 'none', 'verified'];
                        $role = $roles[$i % 8];
                        $kyc = $kycStatuses[$i % 8];
                    @endphp
                    <tr class="border-b border-[var(--color-gc-border)] hover:bg-[var(--color-gc-hover)] transition-colors">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-[var(--color-gc-primary)] flex items-center justify-center text-white text-sm font-semibold">{{ strtoupper(substr($names[$i-1], 0, 2)) }}</div>
                                <div>
                                    <div class="text-sm font-medium text-[var(--color-gc-text)]">{{ $names[$i-1] }}</div>
                                    @if($role === 'seller')
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3 h-3 text-[var(--color-gc-primary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                                        <span class="text-xs text-[var(--color-gc-primary)]">Verified Seller</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="p-4 gc-hide-mobile text-[var(--color-gc-text-secondary)]">{{ strtolower(str_replace(' ', '.', $names[$i-1])) }}@email.com</td>
                        <td class="p-4">
                            @if($role === 'admin')
                            <span class="gc-badge bg-[var(--color-gc-error)]/10 text-[var(--color-gc-error)]">Admin</span>
                            @elseif($role === 'seller')
                            <span class="gc-badge bg-[var(--color-gc-primary)]/10 text-[var(--color-gc-primary)]">Seller</span>
                            @else
                            <span class="gc-badge bg-[var(--color-gc-info)]/10 text-[var(--color-gc-info)]">Buyer</span>
                            @endif
                        </td>
                        <td class="p-4 gc-hide-mobile">
                            @if($kyc === 'verified')
                            <span class="gc-badge bg-[var(--color-gc-accent)]/10 text-[var(--color-gc-accent)]">Verified</span>
                            @elseif($kyc === 'pending')
                            <span class="gc-badge bg-[var(--color-gc-warning)]/10 text-[var(--color-gc-warning)]">Pending</span>
                            @elseif($kyc === 'rejected')
                            <span class="gc-badge bg-[var(--color-gc-error)]/10 text-[var(--color-gc-error)]">Ditolak</span>
                            @else
                            <span class="gc-badge bg-[var(--color-gc-bg-hover)] text-[var(--color-gc-text-tertiary)]">—</span>
                            @endif
                        </td>
                        <td class="p-4 gc-hide-mobile text-[var(--color-gc-text-secondary)]">{{ now()->subDays(rand(1, 365))->format('d M Y') }}</td>
                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end gap-1" x-data="{ open: false }">
                                <button @click="open = !open" class="gc-btn gc-btn-ghost gc-btn-icon" aria-label="More actions">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 bg-[var(--color-gc-bg-elevated)] border border-[var(--color-gc-border)] rounded-xl shadow-2xl z-10 py-1 min-w-[160px]">
                                    <a href="#" class="block px-4 py-2 text-sm text-[var(--color-gc-text)] hover:bg-[var(--color-gc-hover)] transition-colors">Lihat Detail</a>
                                    @if($kyc === 'pending')
                                    <a href="#" class="block px-4 py-2 text-sm text-[var(--color-gc-accent)] hover:bg-[var(--color-gc-accent)]/10 transition-colors">Verifikasi KYC</a>
                                    @endif
                                    <a href="#" class="block px-4 py-2 text-sm text-[var(--color-gc-info)] hover:bg-[var(--color-gc-info)]/10 transition-colors">Reset Password</a>
                                    <div class="border-t border-[var(--color-gc-border)] my-1"></div>
                                    <button class="w-full text-left px-4 py-2 text-sm text-[var(--color-gc-error)] hover:bg-[var(--color-gc-error)]/10 transition-colors">Ban User</button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-[var(--color-gc-border)]">
            <x-pagination />
        </div>
    </div>

    {{-- KYC Verification Modal --}}
    <div x-show="showKYCModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.prevent="showKYCModal = false">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showKYCModal = false"></div>
        <div x-show="showKYCModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="gc-modal-panel max-w-lg relative bg-[var(--color-gc-bg-elevated)]">
            <div class="gc-modal-header">
                <h3 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)]">Verifikasi KYC</h3>
                <button @click="showKYCModal = false" class="gc-btn-icon gc-btn-ghost" aria-label="Close">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="gc-modal-body space-y-4">
                <div class="flex items-center gap-4 p-4 rounded-xl bg-[var(--color-gc-bg-subtle)]">
                    <div class="w-12 h-12 rounded-full bg-[var(--color-gc-primary)] flex items-center justify-center text-white font-semibold">AF</div>
                    <div>
                        <div class="font-medium text-[var(--color-gc-text)]">Ahmad Fauzi</div>
                        <div class="text-sm text-[var(--color-gc-text-secondary)]">ahmad.fauzi@email.com</div>
                        <div class="text-xs text-[var(--color-gc-text-tertiary)]">Terdaftar: 15 Jan 2024</div>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-[var(--color-gc-text)] mb-2">Dokumen Identitas</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="aspect-[4/3] bg-[var(--color-gc-bg-subtle)] rounded-lg border border-[var(--color-gc-border)] border-dashed flex items-center justify-center">
                            <div class="text-center text-[var(--color-gc-text-tertiary)]">
                                <svg class="w-8 h-8 mx-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                <p class="text-xs mt-1">KTP</p>
                            </div>
                        </div>
                        <div class="aspect-[4/3] bg-[var(--color-gc-bg-subtle)] rounded-lg border border-[var(--color-gc-border)] border-dashed flex items-center justify-center">
                            <div class="text-center text-[var(--color-gc-text-tertiary)]">
                                <svg class="w-8 h-8 mx-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                <p class="text-xs mt-1">Selfie</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="gc-modal-footer">
                <button @click="showKYCModal = false" class="gc-btn gc-btn-ghost gc-btn-md">Batal</button>
                <button @click="showKYCModal = false" class="gc-btn gc-btn-destructive gc-btn-md">Tolak</button>
                <button @click="showKYCModal = false" class="gc-btn gc-btn-accent gc-btn-md">Verifikasi</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
Alpine.data('adminUsers', () => ({
    search: '',
    filterRole: '',
    filterKYC: '',
    filterStatus: '',
    showKYCModal: false,
}));
</script>
@endpush
@endsection