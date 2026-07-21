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
        // Ambil daftar OPD dari database
        $opdList = OpdDrive::query()
            ->orderBy('opd_name', 'asc')
            ->pluck('opd_name')
            ->toArray();

        return view('pse.submit-pse', compact('opdList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'instansi' => 'nullable|string|max:255',
            'opd' => 'required|string|max:255',
            'app_name' => 'required|string|max:255',
            'url' => 'nullable|url|max:255',
            'dns_or_ip' => 'nullable|string|max:255',
            'version' => 'nullable|string|max:255',
            'sector' => 'required|in:publik,privat',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'risk_category' => 'nullable|in:tinggi,menengah,rendah',
            'data_classification' => 'nullable|in:sangat_rahasia,rahasia,terbatas,biasa',
            'server_location' => 'nullable|in:dalam_negeri,luar_negeri',
            'personal_data_processed' => 'nullable|string|max:255',
            'req_security' => 'nullable|boolean',
            'req_protection' => 'nullable|boolean',
            'req_personal_data' => 'nullable|boolean',
            'req_feasibility' => 'nullable|boolean',
            'req_spbe' => 'nullable|boolean',
            'status' => 'nullable|string',
            'tdpse_number' => 'nullable|string|max:255',
            'registration_date' => 'nullable|date',
            'badge_link' => 'nullable|url|max:255',
            'certificate_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'badge_image' => 'nullable|image|mimes:png,jpg,jpeg|max:5120',
        ]);

        // Tangani upload berkas Sertifikat PDF
        if ($request->hasFile('certificate_pdf')) {
            $path = $request->file('certificate_pdf')->store('pse-certificates', 'public');
            $validated['certificate_pdf'] = $path;
        }

        // Tangani upload berkas Gambar Badge
        if ($request->hasFile('badge_image')) {
            $path = $request->file('badge_image')->store('pse-certificates', 'public');
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
