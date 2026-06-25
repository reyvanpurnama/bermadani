<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Admin\MonthlyFinancialReport;
use App\Models\Member;
use App\Models\SimpananTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MonthlyFinancialReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Register REGEXP function for SQLite testing
        $connection = \Illuminate\Support\Facades\DB::connection();
        if ($connection instanceof \Illuminate\Database\SQLiteConnection) {
            $pdo = $connection->getPdo();
            $pdo->sqliteCreateFunction('regexp', function ($pattern, $value) {
                return preg_match('/' . str_replace('/', '\/', $pattern) . '/i', $value);
            }, 2);
        }
    }

    private function makeAdmin(): User
    {
        return User::factory()->create([
            'role' => 'ADMIN',
            'isActive' => true,
        ]);
    }

    public function test_monthly_financial_report_includes_unpaid_pokok_installments(): void
    {
        $admin = $this->makeAdmin();
        $user = User::factory()->create();

        // 1. Create a member with no loans, but having an unpaid Simpanan Pokok bill for next month
        $member = Member::create([
            'userId' => $user->id,
            'nomorAnggota' => '123456',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '081234567890',
            'gender' => 'MALE',
            'unitKerja' => 'Dosen',
            'status' => 'ACTIVE',
            'isMemberKoperasi' => true,
            'simpananPokok' => 50000,
            'simpananWajib' => 0,
            'simpananSukarela' => 0,
            'simwa_payment_method' => 'MANUAL', // exclude regular simwa salary deduction
            'sukarela_payment_method' => 'MANUAL',
        ]);

        $nextMonth = Carbon::now()->addMonth();
        $billingMonthStr = $nextMonth->format('Y-m');

        // Create the unpaid Simpanan Pokok bill for next month
        $bill = SimpananTransaction::create([
            'memberId' => $member->id,
            'type' => 'POKOK',
            'transactionType' => 'SETOR',
            'amount' => 50000,
            'balanceAfter' => 0,
            'notes' => 'Cicilan Simpanan Pokok Ke-2 dari 4',
            'processedBy' => $admin->id,
            'status' => 'APPROVED',
            'billStatus' => 'APPROVED',
            'billingMonth' => $billingMonthStr,
            'paidAmount' => 0,
        ]);

        // Run the report component for next month
        Livewire::actingAs($admin)
            ->test(MonthlyFinancialReport::class)
            ->set('selectedMonth', $nextMonth->format('m'))
            ->set('selectedYear', $nextMonth->format('Y'))
            ->call('generateReport')
            ->assertSet('isExecuted', false)
            ->assertSee('John Doe')
            // Assert that reportData includes the pokok amount and grand total
            ->assertSee('50.000');
    }

    public function test_executing_payroll_pays_pokok_installments_and_updates_member_balance(): void
    {
        $admin = $this->makeAdmin();
        $user = User::factory()->create();

        // 1. Create a member with an unpaid Simpanan Pokok bill for next month
        $member = Member::create([
            'userId' => $user->id,
            'nomorAnggota' => '123457',
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '081234567891',
            'gender' => 'FEMALE',
            'unitKerja' => 'Staff',
            'status' => 'ACTIVE',
            'isMemberKoperasi' => true,
            'simpananPokok' => 50000,
            'simpananWajib' => 0,
            'simpananSukarela' => 0,
            'simwa_payment_method' => 'MANUAL',
            'sukarela_payment_method' => 'MANUAL',
        ]);

        $nextMonth = Carbon::now()->addMonth();
        $billingMonthStr = $nextMonth->format('Y-m');

        $bill = SimpananTransaction::create([
            'memberId' => $member->id,
            'type' => 'POKOK',
            'transactionType' => 'SETOR',
            'amount' => 50000,
            'balanceAfter' => 0,
            'notes' => 'Cicilan Simpanan Pokok Ke-2 dari 4',
            'processedBy' => $admin->id,
            'status' => 'APPROVED',
            'billStatus' => 'APPROVED',
            'billingMonth' => $billingMonthStr,
            'paidAmount' => 0,
        ]);

        // Execute the payroll for next month
        Livewire::actingAs($admin)
            ->test(MonthlyFinancialReport::class)
            ->set('selectedMonth', $nextMonth->format('m'))
            ->set('selectedYear', $nextMonth->format('Y'))
            ->call('generateReport')
            ->call('executePayroll')
            ->assertHasNoErrors();

        // Assert member's simpananPokok is now 100000 (50k original + 50k from paid installment)
        $member->refresh();
        $this->assertEquals(100000, $member->simpananPokok);

        // Assert the bill is now marked as paid (paidAmount = 50000) and balanceAfter updated
        $bill->refresh();
        $this->assertEquals(50000, $bill->paidAmount);
        $this->assertEquals(100000, $bill->balanceAfter);
        $this->assertEquals($admin->id, $bill->approvedBy);
        $this->assertStringContainsString('Setoran Payroll (Cicilan Pokok)', $bill->notes);
    }

    public function test_loading_old_snapshots_without_pokok_key_does_not_crash(): void
    {
        $admin = $this->makeAdmin();

        // Create an old snapshot with data missing 'pokok' and 'total_pokok'
        \App\Models\FinancialReportSnapshot::create([
            'month' => 5,
            'year' => 2025,
            'status' => 'EXECUTED',
            'executed_by' => $admin->id,
            'data' => [
                'summary' => [
                    'total_members' => 1,
                    'total_simwa' => 50000,
                    'total_sukarela' => 10000,
                    'total_angsuran_bermadani' => 0,
                    'total_angsuran_bmt_itqan_1' => 0,
                    'total_angsuran_bmt_itqan_2' => 0,
                    'grand_total' => 60000,
                ],
                'items' => [
                    [
                        'member_id' => 999,
                        'nama' => 'Old Member',
                        'unit_kerja' => 'Staff',
                        'simwa' => 50000,
                        'sukarela' => 10000,
                        'angsuran_bermadani' => 0,
                        'angsuran_ke_bermadani' => 0,
                        'tenor_bermadani' => 0,
                        'angsuran_bmt_itqan_1' => 0,
                        'simwa_bmt_itqan_1' => 0,
                        'angsuran_ke_bmt_itqan_1' => 0,
                        'tenor_bmt_itqan_1' => 0,
                        'angsuran_bmt_itqan_2' => 0,
                        'simwa_bmt_itqan_2' => 0,
                        'angsuran_ke_bmt_itqan_2' => 0,
                        'tenor_bmt_itqan_2' => 0,
                        'total' => 60000,
                        'has_loan' => false,
                        'loan_details' => [],
                    ]
                ]
            ]
        ]);

        // Run the report component for that month (May 2025)
        Livewire::actingAs($admin)
            ->test(MonthlyFinancialReport::class)
            ->set('selectedMonth', '05')
            ->set('selectedYear', '2025')
            ->call('generateReport')
            ->assertSet('isSnapshot', true)
            ->assertSee('Old Member')
            ->assertSee('50.000') // simwa
            ->assertSee('10.000'); // sukarela
    }
}
