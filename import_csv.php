<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$csvFile = storage_path('app/temp_pse.csv');
$file = fopen($csvFile, 'r');
if (!$file) {
    die("Failed to open CSV.\n");
}

$header = fgetcsv($file); // skip header
$count = 0;

while (($row = fgetcsv($file)) !== false) {
    if (count($row) < 9) continue;
    
    // 0: Timestamp
    // 1: OPD
    // 2: App Name
    // 3: Version
    // 4: Contact
    // 5: Phone
    // 6: URL
    // 7: DNS
    // 8: Desc
    
    $timestamp = \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $row[0])->format('Y-m-d');
    
    // Check if exists
    $exists = \App\Models\Pse::where('app_name', $row[2])->first();
    if (!$exists) {
        \App\Models\Pse::create([
            'registration_date' => $timestamp,
            'opd' => $row[1],
            'app_name' => $row[2],
            'version' => $row[3] === '-' ? null : $row[3],
            'contact_person' => $row[4],
            'contact_phone' => $row[5],
            'url' => $row[6],
            'dns_or_ip' => $row[7],
            'description' => $row[8],
            'status' => 'belum_terdaftar',
            'sector' => 'publik',
            'req_security' => true,
            'req_protection' => true,
            'req_personal_data' => true,
            'req_feasibility' => true,
            'req_spbe' => true,
        ]);
        $count++;
    }
}
fclose($file);
echo "Imported $count records successfully!\n";
