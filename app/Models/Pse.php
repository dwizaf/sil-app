<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pse extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_name',
        'url',
        'opd',
        'tdpse_number',
        'registration_date',
        'sector',
        'status',
        'notes',
        'instansi',
        'version',
        'contact_person',
        'contact_phone',
        'dns_or_ip',
        'description',
        'server_location',
        'risk_category',
        'risk_category_doc',
        'data_classification',
        'data_classification_doc',
        'personal_data_processed',
        'req_security',
        'req_protection',
        'req_personal_data',
        'req_feasibility',
        'req_spbe',
        'supporting_doc',
        'gdrive_folder_url',
        'badge_link',
        'badge_image',
        'certificate_pdf',
    ];

    protected $casts = [
        'personal_data_processed' => 'array',
        'req_security' => 'boolean',
        'req_protection' => 'boolean',
        'req_personal_data' => 'boolean',
        'req_feasibility' => 'boolean',
        'req_spbe' => 'boolean',
        'registration_date' => 'date',
    ];
}
