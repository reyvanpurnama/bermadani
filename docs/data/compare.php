<?php
// Load anggota CSV
$anggota = [];
$lines = file('/home/alexa/Documents/project/web-koperasi-umb/docs/data/anggota.csv');
foreach ($lines as $i => $line) {
    if ($i == 0 || empty(trim($line)))
        continue;
    $cols = str_getcsv($line);
    if (!empty($cols[1])) {
        $name = strtoupper(trim($cols[1]));
        $anggota[$name] = $cols[0];
    }
}

// Load payroll content
$content = file_get_contents('/home/alexa/Documents/project/web-koperasi-umb/docs/data/payroll-des2025.md');

// Extract names more carefully - handle variations
$payrollNames = [];

// Get all lines and extract names
$payrollLines = explode("\n", $content);
foreach ($payrollLines as $line) {
    // Match patterns like "1 Meti Mediyastuti Angsuran" or "1 IA KURNIA SIMWA"
    if (preg_match('/^\d+\s+(.+?)\s+(Angsuran|SIMWA|TABUNGAN)/i', $line, $m)) {
        $name = strtoupper(trim($m[1]));
        // Clean up common abbreviations
        $name = str_replace('.', '', $name); // Remove dots (M. -> M)
        $name = preg_replace('/\s+/', ' ', $name); // Normalize spaces
        $payrollNames[$name] = true;
    }
}

// Function to normalize name for comparison
function normalizeName($name)
{
    $name = strtoupper($name);
    $name = str_replace('.', '', $name);
    $name = str_replace("'", '', $name);
    $name = preg_replace('/\s+/', ' ', $name);
    return trim($name);
}

// Function to check if names match (fuzzy)
function namesMatch($name1, $name2)
{
    $n1 = normalizeName($name1);
    $n2 = normalizeName($name2);

    // Exact match
    if ($n1 == $n2)
        return true;

    // One contains the other
    if (str_contains($n1, $n2) || str_contains($n2, $n1))
        return true;

    // Split into parts
    $parts1 = explode(' ', $n1);
    $parts2 = explode(' ', $n2);

    // First word match (for abbreviated names like "HILAL" matching "MOHAMAD HILAL NUMAN")
    // Check if any significant word matches
    foreach ($parts1 as $p1) {
        if (strlen($p1) < 4)
            continue; // Skip short words like "M", "T"
        foreach ($parts2 as $p2) {
            if (strlen($p2) < 4)
                continue;
            if ($p1 == $p2)
                return true;
            // Similar match (allow 1 char difference for typos like MUTTAQIEN vs MUTAQIEN)
            if (strlen($p1) > 5 && strlen($p2) > 5) {
                if (levenshtein($p1, $p2) <= 2)
                    return true;
            }
        }
    }

    // Match first 2 parts if both have them
    if (count($parts1) >= 2 && count($parts2) >= 2) {
        if ($parts1[0] == $parts2[0] && $parts1[1] == $parts2[1])
            return true;
        // Or first and last
        if ($parts1[0] == $parts2[0] && end($parts1) == end($parts2))
            return true;
    }

    return false;
}

echo "=== ANGGOTA TIDAK ADA DI PAYROLL DES 2025 ===\n";
echo "Total Anggota di CSV: " . count($anggota) . "\n";
echo "Total Nama di Payroll: " . count($payrollNames) . "\n\n";

// Debug: show payroll names
// echo "Payroll names:\n";
// foreach ($payrollNames as $n => $v) echo "- $n\n";
// echo "\n";

// Find missing members
$missing = [];
$matched = [];
foreach ($anggota as $name => $no) {
    $found = false;
    $matchedWith = '';

    foreach ($payrollNames as $pname => $v) {
        if (namesMatch($name, $pname)) {
            $found = true;
            $matchedWith = $pname;
            break;
        }
    }

    if (!$found) {
        $missing[$no] = $name;
    } else {
        $matched[$no] = "$name => $matchedWith";
    }
}

ksort($missing, SORT_NUMERIC);

echo "Tidak ditemukan di payroll: " . count($missing) . " orang\n";
echo "-------------------------------------------\n";
foreach ($missing as $no => $name) {
    echo "$no. $name\n";
}
