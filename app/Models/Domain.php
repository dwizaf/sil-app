<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Domain extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'opd',
        'app_name',
        'app_description',
        'type',
        'content',
        'ttl',
        'proxied',
        'category',
        'status',
        'inactive_at',
        'notes',
        'imported_at',
    ];

    protected $casts = [
        'proxied' => 'boolean',
        'inactive_at' => 'datetime',
        'imported_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Constants
    |--------------------------------------------------------------------------
    */

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    const CATEGORY_PEMERINTAH = 'pemerintah';
    const CATEGORY_DOCKER = 'docker';
    const CATEGORY_STAGING = 'staging';
    const CATEGORY_PRODUCTION = 'production';
    const CATEGORY_LAINNYA = 'lainnya';

    /**
     * Daftar semua kategori yang tersedia.
     */
    public static function categories(): array
    {
        return [
            self::CATEGORY_PEMERINTAH => 'Pemerintah',
            self::CATEGORY_DOCKER => 'Docker',
            self::CATEGORY_STAGING => 'Staging',
            self::CATEGORY_PRODUCTION => 'Production',
            self::CATEGORY_LAINNYA => 'Lainnya',
        ];
    }

    /**
     * Daftar semua status yang tersedia.
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
        ];
    }

    /**
     * Daftar tipe DNS record yang umum.
     */
    public static function dnsTypes(): array
    {
        return [
            'A' => 'A',
            'AAAA' => 'AAAA',
            'CNAME' => 'CNAME',
            'MX' => 'MX',
            'TXT' => 'TXT',
            'NS' => 'NS',
            'SRV' => 'SRV',
            'CAA' => 'CAA',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope: hanya domain aktif.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope: hanya domain tidak aktif.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    /**
     * Scope: domain yang dinonaktifkan dalam rentang waktu tertentu.
     */
    public function scopeDeactivatedBetween(Builder $query, Carbon|string $start, Carbon|string $end): Builder
    {
        return $query->where('status', self::STATUS_INACTIVE)
            ->whereBetween('inactive_at', [$start, $end]);
    }

    /**
     * Scope: domain yang dibuat (didaftarkan) dalam rentang waktu tertentu.
     */
    public function scopeCreatedBetween(Builder $query, Carbon|string $start, Carbon|string $end): Builder
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    /**
     * Scope: domain yang diimpor dalam rentang waktu tertentu.
     */
    public function scopeImportedBetween(Builder $query, Carbon|string $start, Carbon|string $end): Builder
    {
        return $query->whereBetween('imported_at', [$start, $end]);
    }

    /*
    |--------------------------------------------------------------------------
    | Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Tandai domain sebagai tidak aktif dan catat timestamp-nya.
     */
    public function markAsInactive(): void
    {
        $this->update([
            'status' => self::STATUS_INACTIVE,
            'inactive_at' => now(),
        ]);
    }

    /**
     * Tandai domain sebagai aktif kembali dan hapus timestamp inactive.
     */
    public function markAsActive(): void
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'inactive_at' => null,
        ]);
    }

    /**
     * Deteksi kategori secara otomatis berdasarkan nama domain dan konten.
     */
    public function autoDetectCategory(): ?string
    {
        $name = strtolower($this->name);
        $content = strtolower($this->content ?? '');

        // Domain pemerintah (.go.id)
        if (str_contains($name, '.go.id')) {
            return self::CATEGORY_PEMERINTAH;
        }

        // Domain staging
        if (str_contains($name, 'staging') || str_contains($name, 'stg.')) {
            return self::CATEGORY_STAGING;
        }

        // IP Docker (172.x.x.x range — private network)
        if (preg_match('/^172\.(1[6-9]|2[0-9]|3[0-1])\./', $content)) {
            return self::CATEGORY_DOCKER;
        }

        // IP Docker (10.x.x.x range — private network)
        if (preg_match('/^10\./', $content)) {
            return self::CATEGORY_DOCKER;
        }

        return null;
    }
}
