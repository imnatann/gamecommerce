@extends('layouts.seller', ['title' => 'Verifikasi KYC'])

@section('content')
<div x-data="kycForm()" class="max-w-3xl mx-auto">

    {{-- Page header --}}
    <div class="mb-6">
        <h1 class="font-[var(--font-family-display)] text-2xl lg:text-3xl font-bold text-[var(--color-gc-text)]">
            Verifikasi KYC
        </h1>
        <p class="text-sm text-[var(--color-gc-text-secondary)] mt-1">
            Lengkapi verifikasi identitas untuk mulai berjualan di GameCommerce
        </p>
    </div>

    {{-- Status info --}}
    @if($seller->kyc_status === 'pending')
        <div class="gc-card p-4 mb-6 border border-[var(--color-gc-warning)] bg-[var(--color-gc-warning)]/10">
            <p class="text-sm text-[var(--color-gc-warning)] font-medium">
                Dokumen KYC Anda sedang diproses. Harap tunggu 1-2 hari kerja.
            </p>
        </div>
    @elseif($seller->kyc_status === 'rejected')
        <div class="gc-card p-4 mb-6 border border-[var(--color-gc-error)] bg-[var(--color-gc-error)]/10">
            <p class="text-sm text-[var(--color-gc-error)] font-medium">
                Verifikasi KYC ditolak. Silakan ajukan kembali dengan dokumen yang valid.
            </p>
        </div>
    @endif

    <form method="POST" action="{{ route('seller.kyc.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- Data Identitas --}}
        <div class="gc-card p-6 mb-4">
            <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)] mb-4">
                Data Identitas
            </h2>

            <div class="space-y-4">
                <div>
                    <label for="full_name" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1">
                        Nama Lengkap (sesuai KTP)
                    </label>
                    <input type="text" id="full_name" name="full_name"
                           value="{{ old('full_name') }}"
                           class="gc-input w-full @error('full_name') border-[var(--color-gc-error)] @enderror"
                           placeholder="Nama sesuai KTP"
                           required>
                    @error('full_name')
                        <p class="text-xs text-[var(--color-gc-error)] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="id_number" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1">
                        Nomor KTP (NIK — 16 digit)
                    </label>
                    <input type="text" id="id_number" name="id_number"
                           value="{{ old('id_number') }}"
                           class="gc-input w-full @error('id_number') border-[var(--color-gc-error)] @enderror"
                           placeholder="1234567890123456"
                           maxlength="16"
                           pattern="[0-9]{16}"
                           required>
                    @error('id_number')
                        <p class="text-xs text-[var(--color-gc-error)] mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Dokumen Foto --}}
        <div class="gc-card p-6 mb-4">
            <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)] mb-4">
                Dokumen Foto
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Foto KTP --}}
                <div>
                    <label class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1">
                        Foto KTP <span class="text-[var(--color-gc-error)]">*</span>
                    </label>
                    <div x-data="{ preview: null }"
                         class="border-2 border-dashed border-[var(--color-gc-border)] rounded-lg p-4 text-center">
                        <input type="file" name="id_photo"
                               x-ref="idPhoto"
                               @change="preview = URL.createObjectURL($event.target.files[0])"
                               accept="image/jpg,image/jpeg,image/png"
                               class="hidden"
                               required>
                        <template x-if="!preview">
                            <div @click="$refs.idPhoto.click()" class="cursor-pointer">
                                <p class="text-sm text-[var(--color-gc-text-secondary)]">Klik untuk upload foto KTP</p>
                                <p class="text-xs text-[var(--color-gc-text-muted)] mt-1">JPG, PNG, maks 5MB</p>
                            </div>
                        </template>
                        <template x-if="preview">
                            <div>
                                <img :src="preview" class="max-h-32 mx-auto rounded" alt="Preview KTP">
                                <button type="button" @click="preview = null; $refs.idPhoto.value = ''"
                                        class="text-xs text-[var(--color-gc-error)] mt-2">Hapus</button>
                            </div>
                        </template>
                    </div>
                    @error('id_photo')
                        <p class="text-xs text-[var(--color-gc-error)] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Foto Selfie --}}
                <div>
                    <label class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1">
                        Foto Selfie dengan KTP <span class="text-[var(--color-gc-error)]">*</span>
                    </label>
                    <div x-data="{ preview: null }"
                         class="border-2 border-dashed border-[var(--color-gc-border)] rounded-lg p-4 text-center">
                        <input type="file" name="selfie_photo"
                               x-ref="selfiePhoto"
                               @change="preview = URL.createObjectURL($event.target.files[0])"
                               accept="image/jpg,image/jpeg,image/png"
                               class="hidden"
                               required>
                        <template x-if="!preview">
                            <div @click="$refs.selfiePhoto.click()" class="cursor-pointer">
                                <p class="text-sm text-[var(--color-gc-text-secondary)]">Klik untuk upload foto selfie</p>
                                <p class="text-xs text-[var(--color-gc-text-muted)] mt-1">JPG, PNG, maks 5MB</p>
                            </div>
                        </template>
                        <template x-if="preview">
                            <div>
                                <img :src="preview" class="max-h-32 mx-auto rounded" alt="Preview Selfie">
                                <button type="button" @click="preview = null; $refs.selfiePhoto.value = ''"
                                        class="text-xs text-[var(--color-gc-error)] mt-2">Hapus</button>
                            </div>
                        </template>
                    </div>
                    @error('selfie_photo')
                        <p class="text-xs text-[var(--color-gc-error)] mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Data Rekening Bank --}}
        <div class="gc-card p-6 mb-4">
            <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)] mb-4">
                Rekening Bank (untuk pencairan dana)
            </h2>

            <div class="space-y-4">
                <div>
                    <label for="bank_name" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1">
                        Nama Bank
                    </label>
                    <input type="text" id="bank_name" name="bank_name"
                           value="{{ old('bank_name') }}"
                           class="gc-input w-full @error('bank_name') border-[var(--color-gc-error)] @enderror"
                           placeholder="Contoh: BCA, Mandiri, BNI"
                           required>
                    @error('bank_name')
                        <p class="text-xs text-[var(--color-gc-error)] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="bank_account" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1">
                            Nomor Rekening
                        </label>
                        <input type="text" id="bank_account" name="bank_account"
                               value="{{ old('bank_account') }}"
                               class="gc-input w-full @error('bank_account') border-[var(--color-gc-error)] @enderror"
                               placeholder="1234567890"
                               required>
                        @error('bank_account')
                            <p class="text-xs text-[var(--color-gc-error)] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bank_holder" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1">
                            Nama Pemilik Rekening
                        </label>
                        <input type="text" id="bank_holder" name="bank_holder"
                               value="{{ old('bank_holder') }}"
                               class="gc-input w-full @error('bank_holder') border-[var(--color-gc-error)] @enderror"
                               placeholder="Sesuai nama di buku tabungan"
                               required>
                        @error('bank_holder')
                            <p class="text-xs text-[var(--color-gc-error)] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- NPWP Opsional --}}
        <div class="gc-card p-6 mb-6">
            <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)] mb-2">
                NPWP <span class="text-xs font-normal text-[var(--color-gc-text-secondary)]">(Opsional)</span>
            </h2>
            <p class="text-xs text-[var(--color-gc-text-secondary)] mb-4">
                Diperlukan jika pendapatan melebihi Rp 4.8 juta/tahun (ketentuan DJP).
            </p>
            <div>
                <label for="npwp_number" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1">
                    Nomor NPWP
                </label>
                <input type="text" id="npwp_number" name="npwp_number"
                       value="{{ old('npwp_number') }}"
                       class="gc-input w-full"
                       placeholder="00.000.000.0-000.000">
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('seller.dashboard') }}"
               class="gc-btn gc-btn-ghost gc-btn-md">
                Batal
            </a>
            <button type="submit" class="gc-btn gc-btn-primary gc-btn-md">
                Kirim untuk Verifikasi
            </button>
        </div>

    </form>

</div>
@endsection

@push('scripts')
<script>
Alpine.data('kycForm', () => ({
    init() {
        // Component initialization jika diperlukan
    },
}));
</script>
@endpush
