<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpdDrive extends Model
{
    use HasFactory;

    protected $fillable = [
        'opd_name',
        'gdrive_link',
    ];
}
