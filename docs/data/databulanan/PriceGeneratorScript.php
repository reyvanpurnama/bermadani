<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sheet15File = 'docs/data/databulanan/PENJUALAN 25 - Sheet15.csv';
$masterRefFile = 'docs/data/databulanan/PENJUALAN 25 - DAFTAR HARGA JUAL 25(1).csv';
$masterPricesFile = 'docs/data/databulanan/PENJUALAN 25 - DAFTAR HARGA JUAL 25.csv';

// 1. Read benchmark selling prices from DAFTAR HARGA JUAL 25(1).csv
$benchmarkHj = [];
if (($h = fopen($masterRefFile, 'r')) !== false) {
    fgetcsv($h); // skip header
    while (($d = fgetcsv($h)) !== false) {
        if (count($d) < 3) continue;
        $nama = trim($d[0]);
        $hjRaw = trim($d[2]);
        if ($hjRaw !== '' && $hjRaw !== '#N/A' && $hjRaw !== '-') {
            $hj = (float) str_replace(['.', ','], ['', '.'], $hjRaw);
            if ($hj > 0) {
                $benchmarkHj[$nama] = round($hj);
            }
        }
    }
    fclose($h);
}

echo "Benchmark prices loaded from $masterRefFile: " . count($benchmarkHj) . " items." . PHP_EOL;

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

function roundTo500($val) {
    return ceil($val / 500) * 500;
}

// 2. Read Sheet15.csv (488 rows) and map benchmark prices
$handle = fopen($sheet15File, 'r');
fgetcsv($handle); // skip header

$rows = [];
$nameCounts = [];

while (($data = fgetcsv($handle, 1000, ',')) !== false) {
    if (count($data) < 2 || trim($data[0]) === '') continue;

    $rawName = trim($data[0]);
    $hbClean = str_replace(['.', ','], ['', '.'], trim($data[1]));
    $hb = round((float) $hbClean);

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
        'hb' => (int)$hb,
    ];
}
fclose($handle);

echo "Loaded and disambiguated " . count($rows) . " rows from $sheet15File." . PHP_EOL;

$filledRows = [];
$totalHb = 0;
$totalHj = 0;
$totalLaba = 0;

foreach ($rows as $r) {
    $hb = $r['hb'];
    $rawName = $r['raw_name'];
    $finalName = $r['final_name'];

    if (isset($benchmarkHj[$rawName])) {
        $patokanHj = $benchmarkHj[$rawName];

        if (isGorengan($rawName, $gorenganKeywords)) {
            $hj = max($patokanHj, $hb + 500);
        } elseif ($patokanHj > $hb) {
            $hj = $patokanHj;
        } else {
            $hj = max(roundTo500($hb * 1.15), $hb + 500);
        }
    } else {
        if (isGorengan($rawName, $gorenganKeywords)) {
            $hj = $hb + 500;
        } else {
            $hj = max(roundTo500($hb * 1.15), $hb + 500);
        }
    }

    if ($hj <= $hb) {
        $hj = roundTo500($hb + 500);
    }

    $hj = (int) round($hj);
    $laba = (int) ($hj - $hb);

    $totalHb += $hb;
    $totalHj += $hj;
    $totalLaba += $laba;

    $filledRows[] = [
        'NAMA BARANG' => $finalName,
        'HARGA BELI' => $hb,
        'HARGA JUAL' => $hj,
        'LABA' => $laba,
    ];
}

// 3. Write output to PENJUALAN 25 - DAFTAR HARGA JUAL 25.csv with clean standard CSV formatting
$writeHandle = fopen($masterPricesFile, 'w');
fputcsv($writeHandle, ['NAMA BARANG', 'HARGA BELI', 'HARGA JUAL', 'LABA']);
foreach ($filledRows as $fr) {
    fputcsv($writeHandle, [$fr['NAMA BARANG'], $fr['HARGA BELI'], $fr['HARGA JUAL'], $fr['LABA']]);
}
fclose($writeHandle);

echo "Successfully written " . count($filledRows) . " clean rows to $masterPricesFile" . PHP_EOL;
echo "Cumulative Buy Price (HPP): Rp " . number_format($totalHb, 0, ',', '.') . PHP_EOL;
echo "Cumulative Sell Price (Omzet): Rp " . number_format($totalHj, 0, ',', '.') . PHP_EOL;
echo "Cumulative Profit (Laba): Rp " . number_format($totalLaba, 0, ',', '.') . PHP_EOL;
echo "Average Gross Margin: " . number_format(($totalLaba / $totalHj) * 100, 2, ',', '.') . "%" . PHP_EOL;
