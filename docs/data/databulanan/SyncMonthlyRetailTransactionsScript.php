<?php

$masterPricesFile = 'docs/data/databulanan/PENJUALAN 25 - DAFTAR HARGA JUAL 25.csv';
$monthlyFiles = glob('docs/data/databulanan/retail_report_*.csv');

// 1. Load master prices from PENJUALAN 25 - DAFTAR HARGA JUAL 25.csv
$priceMap = [];
if (($h = fopen($masterPricesFile, 'r')) !== false) {
    fgetcsv($h); // skip header
    while (($d = fgetcsv($h)) !== false) {
        if (count($d) < 3) continue;
        $nama = trim($d[0]);
        $hj = (float) str_replace(['.', ','], ['', '.'], trim($d[2]));
        // Remove numeric suffix if matching base name
        $baseName = preg_replace('/\s+\d+$/', '', $nama);
        if ($nama !== '' && $hj > 0) {
            $priceMap[$nama] = $hj;
            if (!isset($priceMap[$baseName])) {
                $priceMap[$baseName] = $hj;
            }
        }
    }
    fclose($h);
}

echo "Master prices map loaded: " . count($priceMap) . " items." . PHP_EOL;

$totalLaba2025 = 0;
$totalLaba2026 = 0;

function parseNumberClean($val) {
    $val = trim((string)$val);
    if ($val === '' || $val === '#N/A' || $val === '-') return 0.0;
    if (str_contains($val, '.') && str_contains($val, ',')) {
        $val = str_replace('.', '', $val);
        $val = str_replace(',', '.', $val);
        return (float) preg_replace('/[^\d\.\-]/', '', $val);
    }
    if (str_contains($val, ',')) {
        $val = str_replace(',', '.', $val);
        return (float) preg_replace('/[^\d\.\-]/', '', $val);
    }
    if (str_contains($val, '.')) {
        $parts = explode('.', $val);
        if (count($parts) > 2) {
            $val = str_replace('.', '', $val);
        } elseif (strlen($parts[1]) === 3 && is_numeric($parts[0]) && (int)$parts[0] > 0 && (int)$parts[0] < 10000) {
            $val = str_replace('.', '', $val);
        }
    }
    return (float) preg_replace('/[^\d\.\-]/', '', $val);
}

foreach ($monthlyFiles as $file) {
    $rows = [];
    $header = null;
    $refreshedCount = 0;

    if (($h = fopen($file, 'r')) !== false) {
        $header = fgetcsv($h);
        while (($d = fgetcsv($h)) !== false) {
            if (count($d) < 7) continue;
            $nama = trim($d[1]);
            $qty = (int) trim($d[2]);
            $hb = parseNumberClean($d[5] ?? $d[4]);

            if (isset($priceMap[$nama])) {
                $d[6] = $priceMap[$nama];
                $refreshedCount++;
            }

            $hj = parseNumberClean($d[6]);
            $labaRow = ($qty * $hj) - $hb;

            if (str_contains($file, '2025')) {
                $totalLaba2025 += $labaRow;
            } else {
                $totalLaba2026 += $labaRow;
            }

            $rows[] = $d;
        }
        fclose($h);
    }

    if (!empty($rows) && $header) {
        $w = fopen($file, 'w');
        fputcsv($w, $header);
        foreach ($rows as $r) {
            fputcsv($w, $r);
        }
        fclose($w);
    }

    echo "Refreshed " . basename($file) . ": $refreshedCount transaction rows updated." . PHP_EOL;
}

echo PHP_EOL . "=== HASIL SINKRONISASI TRANSAKSI BULANAN ===" . PHP_EOL;
echo "Total Laba Retail 2025: Rp " . number_format($totalLaba2025, 0, ',', '.') . PHP_EOL;
echo "Total Laba Retail 2026: Rp " . number_format($totalLaba2026, 0, ',', '.') . PHP_EOL;

// Save summary json for seeder when DB is up
file_put_contents('docs/data/databulanan/rat_laba_summary.json', json_encode([
    '2025' => $totalLaba2025,
    '2026' => $totalLaba2026,
], JSON_PRETTY_PRINT));

echo "Summary saved to rat_laba_summary.json" . PHP_EOL;
