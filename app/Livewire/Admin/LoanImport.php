<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Loan;
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class LoanImport extends Component
{
    use WithFileUploads;

    public $file;
    public $previewData = [];
    public $headers = [];
    public $isConfirmingImport = false;
    public $importStats = [
        'total' => 0,
        'success' => 0,
        'failed' => 0,
        'matched_members' => 0,
    ];

    public function updatedFile()
    {
        $this->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $this->parseCsv();
    }

    public function parseCsv()
    {
        $path = $this->file->getRealPath();
        $rows = array_map('str_getcsv', file($path));

        if (count($rows) > 0) {
            $this->headers = $rows[0];
            unset($rows[0]); // Remove header

            $this->previewData = [];

            foreach ($rows as $row) {
                if (count($row) !== count($this->headers))
                    continue;

                $rowData = array_combine($this->headers, $row);

                // Find Member
                $name = $rowData['NAMA_DEBITUR'] ?? '';
                $member = Member::where('name', 'like', '%' . $name . '%')->first();

                $this->previewData[] = [
                    'raw' => $rowData,
                    'member' => $member,
                    'status' => $member ? 'MATCH' : 'NOT_FOUND'
                ];
            }
        }
    }

    public function import()
    {
        if (empty($this->previewData))
            return;

        $this->importStats = ['total' => 0, 'success' => 0, 'failed' => 0, 'matched_members' => 0];

        DB::beginTransaction();
        try {
            foreach ($this->previewData as $item) {
                $this->importStats['total']++;

                if (!$item['member']) {
                    $this->importStats['failed']++;
                    continue;
                }

                $this->importStats['matched_members']++;
                $member = $item['member'];
                $data = $item['raw'];

                // Parse Data
                $amount = (float) ($data['PLAFOND_RP'] ?? 0);
                $monthlyPayment = (float) ($data['TOTAL'] ?? 0); // Include Simwa BMT 30k
                $simwaAmount = (float) ($data['SIMPANAN_WAJIB'] ?? 0); // New Column
                $tenor = (int) ($data['TENOR'] ?? 0);
                $angsuranKe = (int) ($data['ANGSURAN_KE'] ?? 1);
                $paidInstallments = max(0, $angsuranKe - 1);
                $accountNumber = $data['NO_REKENING'] ?? null;

                // Parse Date 05/11/2024 -> Y-m-d
                $startDate = null;
                if (!empty($data['TANGGAL_CAIR'])) {
                    try {
                        $startDate = Carbon::createFromFormat('d/m/Y', $data['TANGGAL_CAIR'])->format('Y-m-d');
                    } catch (\Exception $e) {
                        // Fallback or leave start date as today/null
                        $startDate = now();
                    }
                }

                // Check Existing Loan to Avoid Duplicates
                // Logic: Member + Source BMT + Amount Matches
                $loan = Loan::where('member_id', $member->id)
                    ->where('loanSource', 'BMT_ITQAN')
                    ->where('amount', $amount)
                    ->first();

                if ($loan) {
                    // Update Existing
                    $loan->update([
                        'paid_installments' => $paidInstallments,
                        'monthlyPayment' => $monthlyPayment,
                        'tenor' => $tenor,
                        'startDate' => $startDate ?? $loan->startDate,
                        'account_number' => $accountNumber,
                        'simwa_amount' => $simwaAmount,
                        'status' => 'ACTIVE', // Ensure active
                    ]);
                } else {
                    // Create New
                    Loan::create([
                        'member_id' => $member->id,
                        'amount' => $amount,
                        'interestRate' => 0, // BMT Logic Unknown, set 0
                        'tenor' => $tenor,
                        'monthlyPayment' => $monthlyPayment,
                        'remainingAmount' => $amount - ($paidInstallments * ($amount / $tenor)), // Estimate
                        'status' => 'ACTIVE',
                        'loanSource' => 'BMT_ITQAN',
                        'purpose' => 'Pinjaman BMT Itqan (Import)',
                        'startDate' => $startDate ?? now(),
                        'endDate' => $startDate ? Carbon::parse($startDate)->addMonths($tenor) : now()->addMonths($tenor),
                        'paid_installments' => $paidInstallments,
                        'account_number' => $accountNumber,
                        'simwa_amount' => $simwaAmount,
                        'approvedAt' => now(), // Auto approve imported
                        'approvedBy' => 'System Import',
                    ]);
                }

                $this->importStats['success']++;
            }

            DB::commit();

            session()->flash('message', 'Import berhasil! Sukses: ' . $this->importStats['success']);
            $this->previewData = [];
            $this->file = null;

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.loan-import')->layout('layouts.admin');
    }
}
