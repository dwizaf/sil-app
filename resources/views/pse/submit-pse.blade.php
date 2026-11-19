<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pendataan Mandiri PSE | JAP - Journal of Asset Platforms</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-900 text-slate-100 min-h-screen pb-16 selection:bg-blue-600 selection:text-white">

    <!-- Header Navigation -->
    <header class="border-b border-slate-800 bg-slate-900/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div
                    class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center font-bold text-white shadow-lg shadow-blue-500/20">
                    JAP
                </div>
                <div>
                    <h1 class="text-lg font-bold text-white leading-tight">JAP - System Portal</h1>
                    <p class="text-xs text-slate-400">Pemerintah Provinsi Jawa Timur</p>
                </div>
            </div>
            <a href="{{ url('/admin/login') }}"
                class="text-xs font-semibold px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition">
                Login Admin &rarr;
            </a>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-10">

        <!-- Hero Section -->
        <div class="text-center mb-10">
            <span
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-blue-500/10 text-blue-400 border border-blue-500/20 mb-4">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                Formulir Resmi Pendataan Sistem Elektronik
            </span>
            <h2 class="text-3xl font-extrabold text-white tracking-tight sm:text-4xl">
                Pendataan Mandiri PSE & Aplikasi OPD
            </h2>
            <p class="mt-3 text-slate-400 text-sm max-w-2xl mx-auto">
                Silakan isi data Sistem Elektronik / Aplikasi milik Perangkat Daerah Anda. Data yang dikirim akan
                tersimpan dan akan diperiksa kembali oleh admin sebelum didaftarkan.
            </p>
        </div>

        @if (session('success_submission'))
            <div class="mb-8 p-6 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300">
                <div class="flex items-start space-x-4">
                    <div
                        class="w-10 h-10 rounded-full bg-emerald-500/20 flex items-center justify-center flex-shrink-0 text-emerald-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-emerald-200">Pendataan PSE Berhasil Dikirim!</h3>
                        <p class="text-sm text-emerald-300/80 mt-1">
                            Aplikasi <strong>{{ session('success_submission')['app_name'] }}</strong> milik
                            <strong>{{ session('success_submission')['opd'] }}</strong> telah diterima sistem.
                        </p>
                        <p class="text-xs text-emerald-400 mt-2 font-mono">
                            Status: <span
                                class="px-2 py-0.5 rounded bg-yellow-500/20 text-yellow-300 border border-yellow-500/30">Sedang
                                Diproses oleh Admin</span>
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-8 p-6 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300">
                <h3 class="font-semibold text-rose-200 mb-2">Mohon perbaiki kesalahan berikut:</h3>
                <ul class="list-disc pl-5 text-xs space-y-1 text-rose-300/90">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pse.submit.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <!-- Section 1: Informasi Dasar -->
            <div class="bg-slate-800/60 rounded-2xl p-6 sm:p-8 border border-slate-700/60 shadow-xl backdrop-blur-sm">
                <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-700/60">
                    <span
                        class="w-8 h-8 rounded-lg bg-blue-500/20 text-blue-400 font-bold flex items-center justify-center text-sm">1</span>
                    <h3 class="text-lg font-bold text-white">Informasi Dasar Sistem Elektronik</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="sm:col-span-2">
                        <label
                            class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Perangkat
                            Daerah / OPD Pemilik <span class="text-rose-400">*</span></label>
                        <select name="opd" required
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">-- Pilih OPD --</option>
                            @foreach ($opdList as $opdName)
                                <option value="{{ $opdName }}" {{ old('opd') == $opdName ? 'selected' : '' }}>
                                    {{ $opdName }}</option>
                            @endforeach
                            <option value="Lainnya" {{ old('opd') == 'Lainnya' ? 'selected' : '' }}>Lainnya / Unit RSUD
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Nama
                            Sistem Elektronik / Aplikasi <span class="text-rose-400">*</span></label>
                        <input type="text" name="app_name" value="{{ old('app_name') }}" required
                            placeholder="Contoh: Sistem Informasi Pelayanan Publik"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Versi
                            Aplikasi <span class="text-rose-400">*</span></label>
                        <input type="text" name="version" value="{{ old('version') }}" required
                            placeholder="Contoh: 2.1.0"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">URL /
                            Web Address <span class="text-rose-400">*</span></label>
                        <input type="text" name="url" value="{{ old('url') }}" required
                            placeholder="Contoh: majadigi.jatimprov.go.id atau https://layanan.jatimprov.go.id"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">DNS / IP
                            Address Server <span class="text-rose-400">*</span></label>
                        <input type="text" name="dns_or_ip" value="{{ old('dns_or_ip') }}" required
                            placeholder="Contoh: 103.xxx.xxx.xxx"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Sektor
                            Sistem Elektronik <span class="text-rose-400">*</span></label>
                        <select name="sector" required
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="publik" {{ old('sector', 'publik') == 'publik' ? 'selected' : '' }}>Sektor
                                Publik</option>
                            <option value="privat" {{ old('sector') == 'privat' ? 'selected' : '' }}>Sektor Privat
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Nama
                            Narahubung / PIC <span class="text-rose-400">*</span></label>
                        <input type="text" name="contact_person" value="{{ old('contact_person') }}" required
                            placeholder="Nama PIC Aplikasi"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">No.
                            Kontak Narahubung (HP / WA) <span class="text-rose-400">*</span></label>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone') }}" required
                            placeholder="081234567890"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>

                    <div class="sm:col-span-2">
                        <label
                            class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Deskripsi
                            Singkat Fungsi Aplikasi <span class="text-rose-400">*</span></label>
                        <textarea name="description" rows="3" required
                            placeholder="Jelaskan fungsi utama dan proses bisnis sistem elektronik ini..."
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section 2: Klasifikasi & Risiko -->
            <div class="bg-slate-800/60 rounded-2xl p-6 sm:p-8 border border-slate-700/60 shadow-xl backdrop-blur-sm">
                <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-700/60">
                    <span
                        class="w-8 h-8 rounded-lg bg-indigo-500/20 text-indigo-400 font-bold flex items-center justify-center text-sm">2</span>
                    <h3 class="text-lg font-bold text-white">Kategori Risiko & Klasifikasi Data</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label
                            class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Kategori
                            Risiko <span class="text-rose-400">*</span></label>
                        <select name="risk_category" required
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">--Pilih--</option>
                            <option value="rendah" {{ old('risk_category') == 'rendah' ? 'selected' : '' }}>Rendah
                            </option>
                            <option value="tinggi" {{ old('risk_category') == 'tinggi' ? 'selected' : '' }}>Tinggi
                            </option>
                            <option value="strategis" {{ old('risk_category') == 'strategis' ? 'selected' : '' }}>
                                Strategis</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Unggah
                            Dokumen Kategori Risiko SE (PDF/DOC) <span class="text-rose-400">*</span></label>
                        <input type="file" name="risk_category_doc" required accept=".pdf,.doc,.docx"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs">
                    </div>

                    <div>
                        <label
                            class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Klasifikasi
                            Data <span class="text-rose-400">*</span></label>
                        <select name="data_classification" required
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">--Pilih--</option>
                            <option value="terbuka" {{ old('data_classification') == 'terbuka' ? 'selected' : '' }}>
                                Terbuka</option>
                            <option value="terbatas" {{ old('data_classification') == 'terbatas' ? 'selected' : '' }}>
                                Terbatas</option>
                            <option value="tertutup" {{ old('data_classification') == 'tertutup' ? 'selected' : '' }}>
                                Tertutup</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Unggah
                            Dokumen Klasifikasi Data (PDF/DOC) <span class="text-rose-400">*</span></label>
                        <input type="file" name="data_classification_doc" required accept=".pdf,.doc,.docx"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Lokasi
                            Pengelolaan Server</label>
                        <select name="server_location"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="dalam_negeri"
                                {{ old('server_location', 'dalam_negeri') == 'dalam_negeri' ? 'selected' : '' }}>Dalam
                                Negeri</option>
                            <option value="luar_negeri"
                                {{ old('server_location') == 'luar_negeri' ? 'selected' : '' }}>Luar Negeri</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label
                            class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Keterangan
                            Data Pribadi Yang Diproses</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-1">
                            @foreach (['Kartu Keluarga', 'KTP', 'Lainnya', 'SIM'] as $item)
                                <label
                                    class="flex items-center space-x-2 p-2.5 rounded-lg bg-slate-900 border border-slate-700 cursor-pointer hover:border-slate-500 transition">
                                    <input type="checkbox" name="personal_data_processed[]"
                                        value="{{ $item }}"
                                        {{ is_array(old('personal_data_processed')) && in_array($item, old('personal_data_processed')) ? 'checked' : '' }}
                                        class="w-4 h-4 text-blue-600 rounded bg-slate-800 border-slate-600 focus:ring-blue-500">
                                    <span class="text-xs text-slate-200">{{ $item }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Checkbox SPBE & Keamanan -->
            <div class="bg-slate-800/60 rounded-2xl p-6 sm:p-8 border border-slate-700/60 shadow-xl backdrop-blur-sm">
                <div class="flex items-center space-x-3 mb-4 pb-4 border-b border-slate-700/60">
                    <span
                        class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 font-bold flex items-center justify-center text-sm">3</span>
                    <div>
                        <h3 class="text-lg font-bold text-white">Pemenuhan Kewajiban Keamanan & SPBE <span
                                class="text-rose-400 text-sm">*</span></h3>
                        <p class="text-xs text-slate-400">Seluruh 5 poin kewajiban di bawah ini wajib disetujui /
                            dicentang agar tombol kirim aktif.</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <label
                        class="flex items-start space-x-3 p-3 rounded-xl bg-slate-900/50 border border-slate-700/50 cursor-pointer hover:bg-slate-900 transition">
                        <input type="checkbox" name="req_security" value="1"
                            {{ old('req_security') ? 'checked' : '' }}
                            class="spbe-checkbox mt-1 w-4 h-4 text-blue-600 rounded bg-slate-800 border-slate-600 focus:ring-blue-500">
                        <span class="text-xs text-slate-300 leading-relaxed">Memastikan keamanan informasi sesuai
                            dengan ketentuan peraturan perundang-undangan; <span class="text-rose-400">*</span></span>
                    </label>

                    <label
                        class="flex items-start space-x-3 p-3 rounded-xl bg-slate-900/50 border border-slate-700/50 cursor-pointer hover:bg-slate-900 transition">
                        <input type="checkbox" name="req_protection" value="1"
                            {{ old('req_protection') ? 'checked' : '' }}
                            class="spbe-checkbox mt-1 w-4 h-4 text-blue-600 rounded bg-slate-800 border-slate-600 focus:ring-blue-500">
                        <span class="text-xs text-slate-300 leading-relaxed">Menyediakan sistem pengamanan terhadap
                            ancaman dan serangan siber; <span class="text-rose-400">*</span></span>
                    </label>

                    <label
                        class="flex items-start space-x-3 p-3 rounded-xl bg-slate-900/50 border border-slate-700/50 cursor-pointer hover:bg-slate-900 transition">
                        <input type="checkbox" name="req_personal_data" value="1"
                            {{ old('req_personal_data') ? 'checked' : '' }}
                            class="spbe-checkbox mt-1 w-4 h-4 text-blue-600 rounded bg-slate-800 border-slate-600 focus:ring-blue-500">
                        <span class="text-xs text-slate-300 leading-relaxed">Melakukan pelindungan Data Pribadi sesuai
                            ketentuan perundang-undangan; <span class="text-rose-400">*</span></span>
                    </label>

                    <label
                        class="flex items-start space-x-3 p-3 rounded-xl bg-slate-900/50 border border-slate-700/50 cursor-pointer hover:bg-slate-900 transition">
                        <input type="checkbox" name="req_feasibility" value="1"
                            {{ old('req_feasibility') ? 'checked' : '' }}
                            class="spbe-checkbox mt-1 w-4 h-4 text-blue-600 rounded bg-slate-800 border-slate-600 focus:ring-blue-500">
                        <span class="text-xs text-slate-300 leading-relaxed">Melakukan uji kelaikan Sistem Elektronik;
                            <span class="text-rose-400">*</span></span>
                    </label>

                    <label
                        class="flex items-start space-x-3 p-3 rounded-xl bg-slate-900/50 border border-slate-700/50 cursor-pointer hover:bg-slate-900 transition">
                        <input type="checkbox" name="req_spbe" value="1" {{ old('req_spbe') ? 'checked' : '' }}
                            class="spbe-checkbox mt-1 w-4 h-4 text-blue-600 rounded bg-slate-800 border-slate-600 focus:ring-blue-500">
                        <span class="text-xs text-slate-300 leading-relaxed">Memenuhi arsitektur Sistem Pemerintahan
                            Berbasis Elektronik (SPBE) Nasional & Daerah. <span class="text-rose-400">*</span></span>
                    </label>

                    <div class="pt-3 border-t border-slate-700/50">
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Unggah
                            Dokumen Pendukung Pemenuhan Kewajiban Kelaikan SPBE (PDF/DOC/ZIP) (opsional)</label>
                        <input type="file" name="supporting_doc" accept=".pdf,.doc,.docx,.zip"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs">
                    </div>
                </div>
            </div>

            <!-- Section 4: Terbitan Sertifikat & Badge Komdigi -->
            <div class="bg-slate-800/60 rounded-2xl p-6 sm:p-8 border border-slate-700/60 shadow-xl backdrop-blur-sm">
                <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-700/60">
                    <span
                        class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 font-bold flex items-center justify-center text-sm">4</span>
                    <h3 class="text-lg font-bold text-white">Informasi Penerbitan Sertifikat & Badge Komdigi</h3>
                </div>

                <div
                    class="p-4 rounded-xl bg-blue-500/10 border border-blue-500/30 text-blue-300 text-xs leading-relaxed flex items-start space-x-3">
                    <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <strong class="text-blue-200 font-semibold block mb-1">Prosedur Penerbitan Sertifikat:</strong>
                        Setelah Anda mengirimkan formulir pendataan ini, Admin JAP akan melakukan verifikasi kelaikan
                        sistem elektronik.
                        Jika telah memenuhi syarat, <strong>Sertifikat TDPSE PDF & Kode Embed Badge Resmi</strong> akan
                        diunggah oleh Admin dan <strong>dikirimkan secara otomatis via WhatsApp</strong> ke nomor
                        Narahubung (PIC) yang Anda daftarkan.
                    </div>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" id="submitBtn" disabled
                    class="w-full sm:w-auto px-8 py-4 bg-slate-700 text-slate-400 cursor-not-allowed font-bold rounded-xl shadow-lg transition text-sm flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Kirim Pendataan PSE</span>
                </button>
            </div>
        </form>

    </main>

    <footer class="max-w-5xl mx-auto px-4 mt-16 text-center text-xs text-slate-500 border-t border-slate-800 pt-8">
        &copy; {{ date('Y') }} JAP - Journal of Asset Platforms | Pemerintah Provinsi Jawa Timur.
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.spbe-checkbox');
            const submitBtn = document.getElementById('submitBtn');

            function checkFormValidity() {
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                if (allChecked) {
                    submitBtn.removeAttribute('disabled');
                    submitBtn.classList.remove('bg-slate-700', 'text-slate-400', 'cursor-not-allowed');
                    submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-500', 'text-white', 'shadow-blue-600/30',
                        'cursor-pointer');
                } else {
                    submitBtn.setAttribute('disabled', 'disabled');
                    submitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-500', 'text-white',
                        'shadow-blue-600/30', 'cursor-pointer');
                    submitBtn.classList.add('bg-slate-700', 'text-slate-400', 'cursor-not-allowed');
                }
            }

            checkboxes.forEach(cb => cb.addEventListener('change', checkFormValidity));
            checkFormValidity();
        });
    </script>

</body>

</html>
