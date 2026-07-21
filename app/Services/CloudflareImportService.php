<?php

namespace App\Services;

use App\Models\Domain;
use App\Models\DomainImportLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class CloudflareImportService
{
    /**
     * Hasil proses import terakhir.
     */
    protected array $result = [
        'total_records' => 0,
        'new_domains' => 0,
        'updated_domains' => 0,
        'deactivated_domains' => 0,
        'reactivated_domains' => 0,
        'new_domain_names' => [],
        'deactivated_domain_names' => [],
        'reactivated_domain_names' => [],
        'updated_domain_names' => [],
        'errors' => [],
    ];

    /**
     * Proses import file Excel Cloudflare dan jalankan diff engine.
     *
     * @param string $filePath Path ke file Excel yang diupload
     * @param string $originalFilename Nama asli file
     * @return array Hasil import
     */
    public function import(string $filePath, string $originalFilename): array
    {
        $this->resetResult();

        try {
            // 1. Baca data dari Excel
            $excelData = $this->readExcelData($filePath);

            if ($excelData->isEmpty()) {
                $this->result['errors'][] = 'File Excel kosong atau format tidak dikenali.';
                return $this->result;
            }

            $this->result['total_records'] = $excelData->count();

            // 2. Jalankan diff engine dalam transaction
            DB::transaction(function () use ($excelData) {
                $this->executeDiffEngine($excelData);
            });

            // 3. Catat log import
            $this->logImport($originalFilename);

        } catch (\Exception $e) {
            Log::error('Cloudflare Import Error: ' . $e->getMessage(), [
                'file' => $originalFilename,
                'trace' => $e->getTraceAsString(),
            ]);
            $this->result['errors'][] = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        return $this->result;
    }

    /**
     * Baca data dari file Excel Cloudflare.
     * Mendukung berbagai variasi nama kolom yang umum di ekspor Cloudflare.
     */
    protected function readExcelData(string $filePath): Collection
    {
        $rawData = Excel::toCollection(null, $filePath)->first();

        if ($rawData === null || $rawData->isEmpty()) {
            return collect();
        }

        // Ambil header dari baris pertama
        $headers = $rawData->first()->map(function ($header) {
            return strtolower(trim((string) $header));
        })->toArray();

        // Map kolom Excel ke kolom database
        $columnMap = $this->mapColumns($headers);

        if (!isset($columnMap['name'])) {
            throw new \Exception('Kolom "Name" tidak ditemukan di file Excel. Kolom yang ditemukan: ' . implode(', ', $headers));
        }

        // Parse data (skip header row) dengan tracking untuk merged cells
        $parsedData = [];
        $lastOpd = '';
        $lastAppName = '';
        $lastAppDesc = '';
        $lastCategory = '';

        foreach ($rawData->skip(1) as $row) {
            $data = [];
            foreach ($columnMap as $dbField => $excelIndex) {
                $data[$dbField] = trim((string) ($row[$excelIndex] ?? ''));
            }

            // Skip jika nama domain kosong
            if (empty($data['name'])) {
                continue;
            }

            // Handle merged cells: jika kosong, ambil nilai dari baris sebelumnya
            if (isset($columnMap['opd'])) {
                if (!empty($data['opd'])) $lastOpd = $data['opd'];
                else $data['opd'] = $lastOpd;
            }

            if (isset($columnMap['app_name'])) {
                if (!empty($data['app_name'])) $lastAppName = $data['app_name'];
                else $data['app_name'] = $lastAppName;
            }

            if (isset($columnMap['app_description'])) {
                if (!empty($data['app_description'])) $lastAppDesc = $data['app_description'];
                else $data['app_description'] = $lastAppDesc;
            }

            if (isset($columnMap['category'])) {
                if (!empty($data['category'])) $lastCategory = $data['category'];
                else $data['category'] = $lastCategory;
            }

            $parsedData[] = $data;
        }

        return collect($parsedData);
    }

    /**
     * Map nama kolom Excel ke kolom database.
     * Mendukung variasi nama kolom yang lazim di ekspor Cloudflare DNS.
     */
    protected function mapColumns(array $headers): array
    {
        $mapping = [];
        $synonyms = [
            'name' => ['name', 'hostname', 'domain', 'subdomain', 'record name', 'dns name'],
            'opd' => ['opd', 'organisasi perangkat daerah', 'dinas', 'instansi'],
            'app_name' => ['app name', 'aplikasi', 'nama aplikasi', 'application'],
            'app_description' => ['app description', 'deskripsi aplikasi', 'keterangan aplikasi', 'keterangan'],
            'type' => ['type', 'record type', 'dns type', 'tipe'],
            'content' => ['content', 'value', 'data', 'target', 'ip address', 'ip', 'address', 'destination'],
            'ttl' => ['ttl', 'time to live'],
            'proxied' => ['proxied', 'proxy', 'proxy status', 'cf proxy', 'cloudflare proxy'],
            'category' => ['category', 'kategori'],
            'status' => ['status'],
        ];

        foreach ($synonyms as $dbField => $possibleNames) {
            foreach ($possibleNames as $possibleName) {
                $index = array_search($possibleName, $headers);
                if ($index !== false) {
                    $mapping[$dbField] = $index;
                    break;
                }
            }
        }

        return $mapping;
    }

    /**
     * ⭐ DIFF ENGINE — Logika utama perbandingan data Excel vs Database.
     *
     * Algoritma:
     * 1. Ambil semua domain dari Excel → $excelDomainNames
     * 2. Ambil semua domain aktif di DB → $activeDbDomains
     * 3. DOMAIN BARU: ada di Excel tapi tidak di DB → Insert
     * 4. DOMAIN HILANG: ada di DB (aktif) tapi tidak di Excel → Inactive + inactive_at
     * 5. DOMAIN KEMBALI: ada di Excel tapi sebelumnya inactive di DB → Reactivate
     * 6. DOMAIN DIPERBARUI: ada di keduanya tapi data berubah → Update
     */
    protected function executeDiffEngine(Collection $excelData): void
    {
        $now = Carbon::now();
        $excelDomainNames = $excelData->pluck('name')->map(fn($n) => strtolower($n))->unique();

        // Ambil semua domain di database (aktif dan tidak aktif)
        $allDbDomains = Domain::all()->keyBy(fn($d) => strtolower($d->name));
        $activeDbDomainNames = $allDbDomains->filter(fn($d) => $d->status === Domain::STATUS_ACTIVE)
            ->keys();
        $inactiveDbDomainNames = $allDbDomains->filter(fn($d) => $d->status === Domain::STATUS_INACTIVE)
            ->keys();

        // --- 1. DOMAIN BARU: ada di Excel, belum ada di DB sama sekali ---
        $newDomainNames = $excelDomainNames->diff($allDbDomains->keys());
        foreach ($newDomainNames as $domainName) {
            $excelRow = $excelData->first(fn($r) => strtolower($r['name']) === $domainName);
            if ($excelRow) {
                $domain = Domain::create([
                    'name' => $excelRow['name'],
                    'opd' => $excelRow['opd'] ?? null,
                    'app_name' => $excelRow['app_name'] ?? null,
                    'app_description' => $excelRow['app_description'] ?? null,
                    'type' => $excelRow['type'] ?? 'A',
                    'content' => $excelRow['content'] ?? null,
                    'ttl' => isset($excelRow['ttl']) && is_numeric($excelRow['ttl']) ? (int) $excelRow['ttl'] : null,
                    'proxied' => $this->parseProxied($excelRow['proxied'] ?? ''),
                    'category' => $excelRow['category'] ?? null,
                    'status' => $excelRow['status'] ?? Domain::STATUS_ACTIVE,
                    'imported_at' => $now,
                ]);

                // Auto-detect category
                $category = $domain->autoDetectCategory();
                if ($category) {
                    $domain->update(['category' => $category]);
                }

                $this->result['new_domains']++;
                $opdName = $excelRow['opd'] ?? 'OPD Tidak Diketahui';
                $this->result['new_domain_names'][] = $excelRow['name'] . ' (' . $opdName . ')';
            }
        }

        // --- 2. DOMAIN HILANG: aktif di DB, tapi tidak ada di Excel → INACTIVE + inactive_at ---
        $missingDomainNames = $activeDbDomainNames->diff($excelDomainNames);
        foreach ($missingDomainNames as $domainName) {
            $domain = $allDbDomains->get($domainName);
            if ($domain) {
                $domain->update([
                    'status' => Domain::STATUS_INACTIVE,
                    'inactive_at' => $now,
                ]);

                $this->result['deactivated_domains']++;
                $opdName = $domain->opd ?? 'OPD Tidak Diketahui';
                $this->result['deactivated_domain_names'][] = $domain->name . ' (' . $opdName . ')';
            }
        }

        // --- 3. DOMAIN KEMBALI: ada di Excel, sebelumnya inactive di DB → REACTIVATE ---
        $returningDomainNames = $excelDomainNames->intersect($inactiveDbDomainNames);
        foreach ($returningDomainNames as $domainName) {
            $domain = $allDbDomains->get($domainName);
            $excelRow = $excelData->first(fn($r) => strtolower($r['name']) === $domainName);

            if ($domain && $excelRow) {
                $domain->update([
                    'status' => $excelRow['status'] ?? Domain::STATUS_ACTIVE,
                    'inactive_at' => null,
                    'content' => $excelRow['content'] ?? $domain->content,
                    'type' => $excelRow['type'] ?? $domain->type,
                    'ttl' => isset($excelRow['ttl']) && is_numeric($excelRow['ttl']) ? (int) $excelRow['ttl'] : $domain->ttl,
                    'proxied' => isset($excelRow['proxied']) ? $this->parseProxied($excelRow['proxied']) : $domain->proxied,
                    'opd' => $excelRow['opd'] ?? $domain->opd,
                    'app_name' => $excelRow['app_name'] ?? $domain->app_name,
                    'app_description' => $excelRow['app_description'] ?? $domain->app_description,
                    'category' => $excelRow['category'] ?? $domain->category,
                    'imported_at' => $now,
                ]);

                $this->result['reactivated_domains']++;
                $opdName = $excelRow['opd'] ?? $domain->opd ?? 'OPD Tidak Diketahui';
                $this->result['reactivated_domain_names'][] = $domain->name . ' (' . $opdName . ')';
            }
        }

        // --- 4. DOMAIN DIPERBARUI: ada di keduanya (aktif), tapi data berubah ---
        $existingActiveNames = $excelDomainNames->intersect($activeDbDomainNames);
        foreach ($existingActiveNames as $domainName) {
            $domain = $allDbDomains->get($domainName);
            $excelRow = $excelData->first(fn($r) => strtolower($r['name']) === $domainName);

            if ($domain && $excelRow) {
                $changes = [];
                $newContent = $excelRow['content'] ?? '';
                $newType = $excelRow['type'] ?? '';
                $newTtl = isset($excelRow['ttl']) && is_numeric($excelRow['ttl']) ? (int) $excelRow['ttl'] : null;
                $newProxied = isset($excelRow['proxied']) ? $this->parseProxied($excelRow['proxied']) : null;

                $newOpd = $excelRow['opd'] ?? '';
                $newAppName = $excelRow['app_name'] ?? '';
                $newAppDescription = $excelRow['app_description'] ?? '';
                $newCategory = $excelRow['category'] ?? '';
                $newStatus = $excelRow['status'] ?? '';

                if (!empty($newContent) && $newContent !== $domain->content) {
                    $changes['content'] = $newContent;
                }
                if (!empty($newType) && $newType !== $domain->type) {
                    $changes['type'] = $newType;
                }
                if ($newTtl !== null && $newTtl !== $domain->ttl) {
                    $changes['ttl'] = $newTtl;
                }
                if ($newProxied !== null && $newProxied !== $domain->proxied) {
                    $changes['proxied'] = $newProxied;
                }
                if (!empty($newOpd) && $newOpd !== $domain->opd) {
                    $changes['opd'] = $newOpd;
                }
                if (!empty($newAppName) && $newAppName !== $domain->app_name) {
                    $changes['app_name'] = $newAppName;
                }
                if (!empty($newAppDescription) && $newAppDescription !== $domain->app_description) {
                    $changes['app_description'] = $newAppDescription;
                }
                if (!empty($newCategory) && $newCategory !== $domain->category) {
                    $changes['category'] = $newCategory;
                }
                if (!empty($newStatus) && $newStatus !== $domain->status) {
                    $changes['status'] = $newStatus;
                }

                if (!empty($changes)) {
                    $changes['imported_at'] = $now;
                    $domain->update($changes);

                    $this->result['updated_domains']++;
                    $opdName = $excelRow['opd'] ?? $domain->opd ?? 'OPD Tidak Diketahui';
                    $this->result['updated_domain_names'][] = $domain->name . ' (' . $opdName . ')';
                } else {
                    // Hanya update timestamp import meskipun tidak ada perubahan data
                    $domain->update(['imported_at' => $now]);
                }
            }
        }
    }

    /**
     * Parse nilai "proxied" dari berbagai format.
     */
    protected function parseProxied(string $value): bool
    {
        $value = strtolower(trim($value));
        return in_array($value, ['true', 'yes', '1', 'ya', 'on', 'proxied']);
    }

    /**
     * Catat log import ke database.
     */
    protected function logImport(string $filename): void
    {
        DomainImportLog::create([
            'filename' => $filename,
            'imported_by' => Auth::id(),
            'total_records' => $this->result['total_records'],
            'new_domains' => $this->result['new_domains'],
            'updated_domains' => $this->result['updated_domains'],
            'deactivated_domains' => $this->result['deactivated_domains'],
            'reactivated_domains' => $this->result['reactivated_domains'],
            'summary' => [
                'new' => $this->result['new_domain_names'],
                'deactivated' => $this->result['deactivated_domain_names'],
                'reactivated' => $this->result['reactivated_domain_names'],
                'updated' => $this->result['updated_domain_names'],
                'errors' => $this->result['errors'],
            ],
        ]);
    }

    /**
     * Reset hasil import.
     */
    protected function resetResult(): void
    {
        $this->result = [
            'total_records' => 0,
            'new_domains' => 0,
            'updated_domains' => 0,
            'deactivated_domains' => 0,
            'reactivated_domains' => 0,
            'new_domain_names' => [],
            'deactivated_domain_names' => [],
            'reactivated_domain_names' => [],
            'updated_domain_names' => [],
            'errors' => [],
        ];
    }
}
