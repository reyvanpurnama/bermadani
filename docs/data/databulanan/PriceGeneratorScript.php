<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sheet15File = 'docs/data/databulanan/PENJUALAN 25 - Sheet15.csv';
$masterPricesFile = 'docs/data/databulanan/PENJUALAN 25 - DAFTAR HARGA JUAL 25.csv';
$monthlyFiles = glob('docs/data/databulanan/*.csv');

// Historical price map from monthly reports
$historicalPrices = [];
foreach ($monthlyFiles as $file) {
    if (str_contains($file, 'DAFTAR HARGA JUAL') || str_contains($file, 'Sheet15')) continue;
    if (($h = fopen($file, 'r')) !== false) {
        fgetcsv($h);
        while (($d = fgetcsv($h)) !== false) {
            if (count($d) < 7) continue;
            $nama = trim($d[1] ?? '');
            $hb = (float) str_replace(['.', ','], ['', '.'], trim($d[4] ?? 0));
            $hj = (float) str_replace(['.', ','], ['', '.'], trim($d[6] ?? 0));
            if ($nama !== '' && $hb > 0 && $hj > 0) {
                if (!isset($historicalPrices[$nama])) {
                    $historicalPrices[$nama] = $hj;
                }
            }
        }
        fclose($h);
    }
}

function roundTo500($val) {
    return ceil($val / 500) * 500;
}

$gorenganKeywords = [
    'bakwan', 'gehu', 'cireng', 'cibay', 'combro', 'karoket', 
    'risol', 'risoles', 'lumpia goreng', 'pastel', 'tempe', 
    'pisang goreng', 'piscok', 'ulen', 'ali agrem', 'arem', 'bacang',
    'otak-otak', 'tahu krispi'
];

function isGorengan($name, $keywords) {
    $lower = strtolower(trim($name));
    foreach ($keywords as $kw) {
        if (str_contains($lower, $kw)) return true;
    }
    return false;
}

// Read Sheet15.csv and perform numeric disambiguation for duplicates
$handle = fopen($sheet15File, 'r');
fgetcsv($handle); // skip header line

$rows = [];
$nameCounts = [];

while (($data = fgetcsv($handle, 1000, ',')) !== false) {
    if (count($data) < 2 || trim($data[0]) === '') continue;

    $rawName = trim($data[0]);
    $hbRaw = trim($data[1]);
    $hbClean = str_replace(['.', ','], ['', '.'], $hbRaw);
    $hb = (float) $hbClean;

    if (!isset($nameCounts[$rawName])) {
        $nameCounts[$rawName] = 1;
        $finalName = $rawName;
    } else {
        $nameCounts[$rawName]++;
        $finalName = $rawName . ' ' . $nameCounts[$rawName];
    }

    $rows[] = [
        'raw_name' => $rawName,
        'final_name' => $finalName,
        'hb' => $hb,
    ];
}
fclose($handle);

echo "Loaded and disambiguated " . count($rows) . " rows from $sheet15File." . PHP_EOL;

// Backup current master file
$backupFile = 'docs/data/databulanan/PENJUALAN 25 - DAFTAR HARGA JUAL 25.csv.bak';
if (!file_exists($backupFile)) {
    copy($masterPricesFile, $backupFile);
}

$filledRows = [];
$totalHb = 0;
$totalHj = 0;
$totalLaba = 0;
$gorenganCount = 0;

foreach ($rows as $r) {
    $hb = $r['hb'];
    $rawName = $r['raw_name'];
    $finalName = $r['final_name'];

    // 1. Gorengan Rule: Laba = 500
    if (isGorengan($rawName, $gorenganKeywords)) {
        $hj = $hb + 500;
        $gorenganCount++;
    } elseif (isset($historicalPrices[$rawName])) {
        $histHj = $historicalPrices[$rawName];
        $histMargin = ($histHj - $hb) / $histHj;
        if ($histMargin > 0.25) {
            $hj = max(roundTo500($hb * 1.15), $hb + 500);
        } else {
            $hj = $histHj;
        }
    } else {
        // Pressed Rule: 15% markup rounded to 500
        $hj = max(roundTo500($hb * 1.15), $hb + 500);
    }

    if ($hj <= $hb) {
        $hj = roundTo500($hb + 500);
    }

    $laba = $hj - $hb;

    $totalHb += $hb;
    $totalHj += $hj;
    $totalLaba += $laba;

    $filledRows[] = [
        'NAMA BARANG' => $finalName,
        'HARGA BELI' => $hb == (int)$hb ? (int)$hb : number_format($hb, 2, ',', ''),
        'HARGA JUAL' => $hj == (int)$hj ? (int)$hj : number_format($hj, 0, ',', ''),
        'LABA' => $laba == (int)$laba ? (int)$laba : number_format($laba, 0, ',', ''),
    ];
}

// Write to PENJUALAN 25 - DAFTAR HARGA JUAL 25.csv
$writeHandle = fopen($masterPricesFile, 'w');
fputcsv($writeHandle, ['NAMA BARANG', 'HARGA BELI', 'HARGA JUAL', 'LABA']);
foreach ($filledRows as $fr) {
    fputcsv($writeHandle, [$fr['NAMA BARANG'], $fr['HARGA BELI'], $fr['HARGA JUAL'], $fr['LABA']]);
}
fclose($writeHandle);

echo "Successfully written " . count($filledRows) . " rows to $masterPricesFile" . PHP_EOL;
echo "Gorengan items (Laba Rp 500): $gorenganCount items." . PHP_EOL;
echo "Cumulative Buy Price (HPP): Rp " . number_format($totalHb, 0, ',', '.') . PHP_EOL;
echo "Cumulative Sell Price (Omzet): Rp " . number_format($totalHj, 0, ',', '.') . PHP_EOL;
echo "Cumulative Profit (Laba): Rp " . number_format($totalLaba, 0, ',', '.') . PHP_EOL;
echo "Average Gross Margin: " . number_format(($totalLaba / $totalHj) * 100, 2, ',', '.') . "%" . PHP_EOL;
