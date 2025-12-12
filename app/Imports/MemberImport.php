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
    public $errors = [];
    public $successCount = 0;
    public $skipCount = 0;

    public function __construct(MemberService $memberService)
    {
        $this->memberService = $memberService;
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

                // Parse simpanan values
                $simpananPokok = $this->parseNumber($row['simpanan_pokok'] ?? 0);
                $simpananWajib = $this->parseNumber($row['total_simpanan_wajib'] ?? 0);

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
                    'simpananSukarela' => 0,
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
                        'processedBy' => auth()->id() ?? 1, // Default to user ID 1 if not authenticated
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
     * Parse number dari string atau formula Excel
     */
    private function parseNumber($value)
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        // Jika ada formula (dimulai dengan =), return 0 karena kita tidak bisa evaluate formula
        if (is_string($value) && str_starts_with(trim($value), '=')) {
            return 0;
        }

        // Jika ada string lain, coba extract angka
        if (is_string($value)) {
            // Remove non-numeric characters except dot and comma
            $cleaned = preg_replace('/[^0-9.,]/', '', $value);
            $cleaned = str_replace(',', '', $cleaned);
            $number = (float) $cleaned;
            
            // Sanity check: jika angka terlalu besar (> 1 miliar), kembalikan 0
            if ($number > 1000000000) {
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
