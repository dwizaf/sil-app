<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PseWebhookController extends Controller
{
    public function store(Request $request)
    {
        // Simple authentication token to prevent unauthorized posts
        $token = $request->header('X-Webhook-Token');
        if ($token !== config('app.webhook_token', 'sil-secret-token')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            $data = $request->validate([
                'instansi' => 'nullable|string',
                'opd' => 'nullable|string',
                'app_name' => 'required|string',
                'version' => 'nullable|string',
                'sector' => 'nullable|string',
                'contact_person' => 'nullable|string',
                'contact_phone' => 'nullable|string',
                'url' => 'nullable|string',
                'dns_or_ip' => 'nullable|string',
                'description' => 'nullable|string',
                'server_location' => 'nullable|string',
                'risk_category' => 'nullable|string',
                'data_classification' => 'nullable|string',
                'personal_data_processed' => 'nullable|string',
                'req_security' => 'nullable|boolean',
                'req_protection' => 'nullable|boolean',
                'req_personal_data' => 'nullable|boolean',
                'req_feasibility' => 'nullable|boolean',
                'req_spbe' => 'nullable|boolean',
            ]);
            
            // Set default status for new submissions from GForm
            $data['status'] = 'proses';

            $pse = \App\Models\Pse::create($data);

            return response()->json([
                'message' => 'Data PSE berhasil diterima dan disimpan.',
                'data' => $pse
            ], 201);
            
        } catch (\Exception $e) {
            \Log::error('PSE Webhook Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal memproses data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
