<?php

namespace App\Http\Controllers;

use App\Models\OpdDrive;
use App\Models\Pse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PseSubmissionController extends Controller
{
    public function index()
    {
        try {
            $opdList = OpdDrive::query()
                ->orderBy('opd_name', 'asc')
                ->pluck('opd_name')
                ->toArray();
        } catch (\Throwable $e) {
            $opdList = [];
        }

        return view('pse.submit-pse', compact('opdList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'instansi' => 'nullable|string|max:255',
            'opd' => 'required|string|max:255',
            'app_name' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'dns_or_ip' => 'required|string|max:255',
            'version' => 'required|string|max:255',
            'sector' => 'required|in:publik,privat',
            'contact_person' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:255',
            'description' => 'required|string',
            'risk_category' => 'required|in:rendah,tinggi,strategis',
            'risk_category_doc' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'data_classification' => 'required|in:terbuka,terbatas,tertutup',
            'data_classification_doc' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'server_location' => 'nullable|in:dalam_negeri,luar_negeri',
            'personal_data_processed' => 'nullable|array',
            'req_security' => 'required|accepted',
            'req_protection' => 'required|accepted',
            'req_personal_data' => 'required|accepted',
            'req_feasibility' => 'required|accepted',
            'req_spbe' => 'required|accepted',
            'supporting_doc' => 'nullable|file|mimes:pdf,doc,docx,zip|max:20480',
            'status' => 'nullable|string',
            'tdpse_number' => 'nullable|string|max:255',
            'registration_date' => 'nullable|date',
            'badge_link' => 'nullable|url|max:255',
            'certificate_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'badge_image' => 'nullable|image|mimes:png,jpg,jpeg|max:5120',
        ], [
            'req_security.accepted' => 'Seluruh poin Pemenuhan Kewajiban Keamanan & SPBE wajib disetujui / dicentang.',
            'req_protection.accepted' => 'Seluruh poin Pemenuhan Kewajiban Keamanan & SPBE wajib disetujui / dicentang.',
            'req_personal_data.accepted' => 'Seluruh poin Pemenuhan Kewajiban Keamanan & SPBE wajib disetujui / dicentang.',
            'req_feasibility.accepted' => 'Seluruh poin Pemenuhan Kewajiban Keamanan & SPBE wajib disetujui / dicentang.',
            'req_spbe.accepted' => 'Seluruh poin Pemenuhan Kewajiban Keamanan & SPBE wajib disetujui / dicentang.',
        ]);

        // Upload Dokumen Pendukung Kategori Risiko SE
        if ($request->hasFile('risk_category_doc')) {
            $path = $request->file('risk_category_doc')->store('pse-documents/risk-category', 'public');
            $validated['risk_category_doc'] = $path;
        }

        // Upload Dokumen Pendukung Klasifikasi Data
        if ($request->hasFile('data_classification_doc')) {
            $path = $request->file('data_classification_doc')->store('pse-documents/data-classification', 'public');
            $validated['data_classification_doc'] = $path;
        }

        // Upload Dokumen Pendukung Kelaikan SPBE
        if ($request->hasFile('supporting_doc')) {
            $path = $request->file('supporting_doc')->store('pse-documents/supporting-docs', 'public');
            $validated['supporting_doc'] = $path;
        }

        // Tangani upload berkas Sertifikat PDF
        if ($request->hasFile('certificate_pdf')) {
            $path = $request->file('certificate_pdf')->store('pse-certificates', 'public');
            $validated['certificate_pdf'] = $path;
        }

        // Tangani upload berkas Gambar Badge
        if ($request->hasFile('badge_image')) {
            $path = $request->file('badge_image')->store('pse-badges', 'public');
            $validated['badge_image'] = $path;
        }

        // Set status default menjadi 'proses' untuk diaudit admin
        $validated['status'] = 'proses';
        $validated['instansi'] = $validated['instansi'] ?? 'Pemerintah Provinsi Jawa Timur';
        $validated['req_security'] = $request->has('req_security');
        $validated['req_protection'] = $request->has('req_protection');
        $validated['req_personal_data'] = $request->has('req_personal_data');
        $validated['req_feasibility'] = $request->has('req_feasibility');
        $validated['req_spbe'] = $request->has('req_spbe');

        $pse = Pse::create($validated);

        return redirect()->back()->with('success_submission', [
            'app_name' => $pse->app_name,
            'opd' => $pse->opd,
            'id' => $pse->id,
        ]);
    }
}
