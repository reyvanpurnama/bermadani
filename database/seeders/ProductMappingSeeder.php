<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\AuditRetailProductMapping;

class ProductMappingSeeder extends Seeder
{
    public function run(): void
    {
        $csvPath = base_path('docs/data/databulanan/PENJUALAN 25 - DAFTAR HARGA JUAL 25.csv');
        if (!file_exists($csvPath)) {
            $this->command->error("File $csvPath not found!");
            return;
        }

        $dbProducts = Product::all();
        $dbProductsMap = [];
        foreach ($dbProducts as $p) {
            $norm = $this->normalizeName($p->name);
            $dbProductsMap[$norm] = $p;
        }

        $handle = fopen($csvPath, 'r');
        fgetcsv($handle); // skip header

        $total = 0;
        $exactCount = 0;
        $fuzzyCount = 0;
        $unmappedCount = 0;

        while (($data = fgetcsv($handle)) !== false) {
            if (empty($data[0])) continue;
            $finalName = trim($data[0]);
            $hb = isset($data[1]) ? (float) str_replace(['.', ','], ['', '.'], $data[1]) : 0;
            $hj = isset($data[2]) ? (float) str_replace(['.', ','], ['', '.'], $data[2]) : 0;

            $total++;
            // Remove numeric suffix for matching base product name in DB
            $baseName = preg_replace('/\s+\d+$/', '', $finalName);
            $normRaw = $this->normalizeName($baseName);

            $matchedProduct = null;

            if (isset($dbProductsMap[$normRaw])) {
                $matchedProduct = $dbProductsMap[$normRaw];
                $exactCount++;
            } else {
                $bestScore = 0;
                $bestMatch = null;
                foreach ($dbProducts as $p) {
                    $normP = $this->normalizeName($p->name);
                    
                    if (str_contains($normRaw, $normP) || str_contains($normP, $normRaw)) {
                        $score = 85;
                    } else {
                        similar_text($normRaw, $normP, $percent);
                        $score = $percent;
                    }

                    if ($score > $bestScore && $score >= 75) {
                        $bestScore = $score;
                        $bestMatch = $p;
                    }
                }

                if ($bestMatch) {
                    $matchedProduct = $bestMatch;
                    $fuzzyCount++;
                } else {
                    $unmappedCount++;
                }
            }

            AuditRetailProductMapping::updateOrCreate(
                ['raw_product_name' => $finalName],
                ['product_id' => $matchedProduct ? $matchedProduct->id : null]
            );

            if ($matchedProduct) {
                $updates = [];
                if (($matchedProduct->buyPrice == 0 || $matchedProduct->buyPrice === null) && $hb > 0) {
                    $updates['buyPrice'] = $hb;
                }
                if (($matchedProduct->sellPrice == 0 || $matchedProduct->sellPrice === null) && $hj > 0) {
                    $updates['sellPrice'] = $hj;
                }
                if (!empty($updates)) {
                    $matchedProduct->update($updates);
                }
            }
        }
        fclose($handle);

        echo "Total Raw CSV Items Processed: $total" . PHP_EOL;
        echo "Exact Matched: $exactCount" . PHP_EOL;
        echo "Fuzzy Matched: $fuzzyCount" . PHP_EOL;
        echo "Unmapped: $unmappedCount" . PHP_EOL;
        echo "AuditRetailProductMapping table now has " . AuditRetailProductMapping::count() . " records." . PHP_EOL;
    }

    private function normalizeName(string $name): string
    {
        $name = strtolower(trim($name));
        $name = preg_replace('/[^a-z0-9]/', '', $name);
        return $name;
    }
}
