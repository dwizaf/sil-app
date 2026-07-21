<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\OpdDrive;

$jsonFile = 'opd_gdrive_folders.json';
if (!file_exists($jsonFile)) {
    die("File $jsonFile tidak ditemukan.\n");
}

$validData = json_decode(file_get_contents($jsonFile), true);
$validNames = array_column($validData, 'opd_name');

// Delete any records not in valid 50 OPDs
$deleted = OpdDrive::whereNotIn('opd_name', $validNames)->delete();

// Upsert valid 50 OPDs
foreach ($validData as $item) {
    OpdDrive::updateOrCreate(
        ['opd_name' => $item['opd_name']],
        ['gdrive_link' => $item['gdrive_link']]
    );
}

echo "Database cleaned & synchronized!\n";
echo "Total valid OPDs in DB: " . OpdDrive::count() . "\n";
