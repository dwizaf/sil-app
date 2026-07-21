<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\OpdDrive;

$jsonFile = 'opd_gdrive_folders_64.json';
if (!file_exists($jsonFile)) {
    die("File $jsonFile tidak ditemukan.\n");
}

$data = json_decode(file_get_contents($jsonFile), true);

$inserted = 0;
$updated = 0;

foreach ($data as $item) {
    $opdDrive = OpdDrive::updateOrCreate(
        ['opd_name' => $item['opd_name']],
        ['gdrive_link' => $item['gdrive_link']]
    );

    if ($opdDrive->wasRecentlyCreated) {
        $inserted++;
    } else {
        $updated++;
    }
}

echo "BERHASIL IMPORT 64 FOLDER GDRIVE OPD!\n";
echo "Total ditambah: $inserted\n";
echo "Total diperbarui: $updated\n";
echo "Total di database saat ini: " . OpdDrive::count() . "\n";
