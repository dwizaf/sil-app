<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\OpdDrive;

// 50 Base Folders from Master GDrive
$jsonFile = 'opd_gdrive_folders.json';
if (!file_exists($jsonFile)) {
    die("File $jsonFile tidak ditemukan.\n");
}

$baseData = json_decode(file_get_contents($jsonFile), true);

$dinkesLink = "https://drive.google.com/drive/folders/1xtgi3V13pALdnKoILI1gEqPFPZ6qmvi0";

// List of 14 Hospitals (RSUD & RS Khusus)
$rsList = [
    "51. RSUD Dr. Soetomo",
    "52. RSUD Dr. Saiful Anwar",
    "53. RSUD Soedono Madiun",
    "54. RSUD Haji Surabaya",
    "55. RS Jiwa Menur",
    "56. RS Paru Surabaya",
    "57. RS Karsa Husada Batu",
    "58. RSUD Mohammad Noer Pamekasan",
    "59. RSUD Sumberglagah Mojokerto",
    "60. RSUD Daha Husada Kediri",
    "61. RS Paru Dungus Madiun",
    "62. RS Paru Jember",
    "63. RS Mata Masyarakat Jawa Timur",
    "64. RSUD Husada Prima",
];

$allOpds = [];

foreach ($baseData as $item) {
    $allOpds[$item['opd_name']] = $item['gdrive_link'];
}

foreach ($rsList as $rsName) {
    // If not set, use Dinkes GDrive link as fallback so it can be updated anytime
    if (!isset($allOpds[$rsName])) {
        $allOpds[$rsName] = $dinkesLink;
    }
}

$inserted = 0;
$updated = 0;

foreach ($allOpds as $name => $link) {
    $opdDrive = OpdDrive::updateOrCreate(
        ['opd_name' => $name],
        ['gdrive_link' => $link]
    );

    if ($opdDrive->wasRecentlyCreated) {
        $inserted++;
    } else {
        $updated++;
    }
}

echo "BERHASIL SINKRONISASI 64 OPD & RSUD SEBAGAI ENTITAS OPD!\n";
echo "Total OPD/RSUD di database saat ini: " . OpdDrive::count() . "\n";
