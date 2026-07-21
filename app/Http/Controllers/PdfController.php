<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function downloadImportLog($id)
    {
        $log = \App\Models\DomainImportLog::with('importer')->findOrFail($id);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.import-report', compact('log'));
        
        return $pdf->download('Laporan_Import_Domain_' . $log->created_at->format('Y-m-d_H-i') . '.pdf');
    }
}
