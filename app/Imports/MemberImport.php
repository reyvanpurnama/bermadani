<?php

namespace App\Imports;

use App\Models\Member;
use App\Models\User;
use App\Services\MemberService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class MemberImport implements ToCollection, WithHeadingRow
{
    protected $memberService;
    protected $sukarelaData = [];
    public $errors = [];
    public $successCount = 0;
    public $skipCount = 0;

    public function __construct(MemberService $memberService)
    {
        $this->memberService = $memberService;
        $this->loadSukarelaData();
    }

    /**
     * Load simpanan sukarela data from pivot CSV
     */
    protected function loadSukarelaData()
    {
        $pivotFile = base_path('docs/data/pivot-sukarela.csv');
        
        if (!file_exists($pivotFile)) {
            return;
        }

        $handle = fopen($pivotFile, 'r');
        $headers = fgetcsv($handle); // Skip first 4 header rows
        fgetcsv($handle);
        fgetcsv($handle);
        fgetcsv($handle);
        
        while (($data = fgetcsv($handle)) !== false) {
            if (!empty($data[0]) && $data[0] !== 'Row Labels' && $data[0] !== 'Grand Total') {
                $name = trim($data[0]);
                $grandTotal = $this->parseRupiah($data[4] ?? '0'); // Column Grand Total
                $this->sukarelaData[$name] = $grandTotal;
            }
        }
        
        fclose($handle);
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $index => $row) {
            $rowNumber = $index + 2; // +2 karena index mulai dari 0 dan ada header row

            try {
                DB::beginTransaction();

                // Skip if nama anggota kosong
                if (empty($row['nama_anggota'])) {
                    $this->skipCount++;
                    $this->errors[] = "Row {$rowNumber}: Nama anggota kosong, dilewati";
                    DB::rollBack();
                    continue;
                }

                $name = trim($row['nama_anggota']);
                
                // Parse tanggal pendaftaran dari Excel serial date
                $joinDate = null;
                if (!empty($row['pendaftaran_anggota'])) {
                    try {
                        if (is_numeric($row['pendaftaran_anggota'])) {
                            // Excel serial date
                            $joinDate = Date::excelToDateTimeObject($row['pendaftaran_anggota']);
                        } else {
                            // String date
                            $joinDate = new \DateTime($row['pendaftaran_anggota']);
                        }
                    } catch (\Exception $e) {
                        $joinDate = now();
                    }
                } else {
                    $joinDate = now();
                }

                // Parse simpanan values dengan format rupiah Indonesia
                $simpananPokok = $this->parseRupiah($row['simpanan_pokok'] ?? '0');
                $simpananWajib = $this->parseRupiah($row['total_simpanan_wajib'] ?? '0');
                $simpananSukarela = $this->sukarelaData[$name] ?? 0;

                // Cek apakah member sudah ada berdasarkan nama
                $existingMember = Member::whereHas('user', function ($query) use ($name) {
                    $query->where('name', $name);
                })->first();

                if ($existingMember) {
                    $this->skipCount++;
                    $this->errors[] = "Row {$rowNumber}: Member '{$name}' sudah ada, dilewati";
                    DB::rollBack();
                    continue;
                }

                // Generate email dari nama (lowercase, replace space dengan .)
                $email = Str::slug($name, '.') . '@koperasi.umb.ac.id';
                
                // Cek apakah email sudah ada
                $emailExists = User::where('email', $email)->exists();
                if ($emailExists) {
                    // Tambahkan random number di email
                    $email = Str::slug($name, '.') . '.' . rand(100, 999) . '@koperasi.umb.ac.id';
                }

                // Create user account
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'username' => Str::slug($name, '.'),
                    'password' => Hash::make('password123'), // Default password
                    'role' => 'USER', // Changed from 'member' to 'USER' to match ENUM
                ]);

                // Generate nomor anggota
                $nomorAnggota = Member::generateNomorAnggota();

                // Create member
                $member = Member::create([
                    'userId' => $user->id,
                    'nomorAnggota' => $nomorAnggota,
                    'name' => $name,
                    'email' => $email,
                    'nim' => null, // Tidak ada di Excel
                    'phone' => null, // Tidak ada di Excel
                    'gender' => 'MALE', // Default to 'MALE' since Excel doesn't have gender data
                    'address' => null, // Tidak ada di Excel
                    'unitKerja' => 'Unknown', // Default value since migration might require it
                    'joinDate' => $joinDate,
                    'status' => 'ACTIVE',
                    'tier' => 'BRONZE',
                    'points' => 0, // Changed from loyaltyPoints to points
                    'totalSpent' => 0,
                    'simpananPokok' => $simpananPokok,
                    'simpananWajib' => $simpananWajib,
                    'simpananSukarela' => $simpananSukarela,
                ]);

                // Catat transaksi simpanan pokok jika ada
                if ($simpananPokok > 0) {
                    $member->simpananTransactions()->create([
                        'type' => 'POKOK',
                        'transactionType' => 'SETOR',
                        'amount' => $simpananPokok,
                        'balanceAfter' => $simpananPokok,
                        'notes' => 'Import data awal - Simpanan Pokok',
                        'processedBy' => auth()->id() ?? 1, // Default to user ID 1 if not authenticated
                        'processedAt' => $joinDate,
                        'status' => 'APPROVED',
                    ]);
                }

                // Catat transaksi simpanan wajib jika ada
                if ($simpananWajib > 0) {
                    $member->simpananTransactions()->create([
                        'type' => 'WAJIB',
                        'transactionType' => 'SETOR',
                        'amount' => $simpananWajib,
                        'balanceAfter' => $simpananWajib,
                        'notes' => 'Import data awal - Simpanan Wajib',
                        'processedBy' => auth()->id() ?? 1,
                        'processedAt' => $joinDate,
                        'status' => 'APPROVED',
                    ]);
                }

                // Catat transaksi simpanan sukarela jika ada
                if ($simpananSukarela > 0) {
                    $member->simpananTransactions()->create([
                        'type' => 'SUKARELA',
                        'transactionType' => 'SETOR',
                        'amount' => $simpananSukarela,
                        'balanceAfter' => $simpananSukarela,
                        'notes' => 'Saldo awal simpanan sukarela (dari pivot data)',
                        'processedBy' => auth()->id() ?? 1,
                        'processedAt' => $joinDate,
                        'status' => 'APPROVED',
                    ]);
                }

                $this->successCount++;
                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                $this->errors[] = "Row {$rowNumber}: Error - " . $e->getMessage();
            }
        }
    }

    /**
     * Parse Indonesian rupiah format to number
     * Examples: "Rp2.950.000" -> 2950000, " Rp 10.088 " -> 10088
     */
    private function parseRupiah($value)
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        // Jika ada formula (dimulai dengan =), return 0
        if (is_string($value) && str_starts_with(trim($value), '=')) {
            return 0;
        }

        if (is_string($value)) {
            // Remove "Rp", spaces, and dots (thousand separators)
            $cleaned = str_replace(['Rp', 'rp', ' ', '.'], '', $value);
            // Replace comma with dot for decimal separator (if any)
            $cleaned = str_replace(',', '.', $cleaned);
            
            $number = (float) $cleaned;
            
            // Sanity check: jika angka terlalu besar (> 1 miliar) atau negatif, return 0
            if ($number > 1000000000 || $number < 0) {
                return 0;
            }
            
            return $number;
        }

        return 0;
    }

    /**
     * Get import summary
     */
    public function getSummary()
    {
        return [
            'success' => $this->successCount,
            'skipped' => $this->skipCount,
            'errors' => count($this->errors),
            'error_details' => $this->errors,
        ];
    }
}
