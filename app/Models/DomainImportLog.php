<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DomainImportLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'imported_by',
        'total_records',
        'new_domains',
        'updated_domains',
        'deactivated_domains',
        'reactivated_domains',
        'summary',
    ];

    protected $casts = [
        'summary' => 'array',
        'total_records' => 'integer',
        'new_domains' => 'integer',
        'updated_domains' => 'integer',
        'deactivated_domains' => 'integer',
        'reactivated_domains' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * User yang melakukan import.
     */
    public function importer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'imported_by');
    }
}
