<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Member\Loans;
use App\Livewire\Member\LoanDetail;
use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MemberLoansTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_view_loans_page_and_outstanding()
    {
        $user = User::factory()->create([
            'role' => 'MEMBER',
            'isActive' => true,
        ]);

        $member = Member::create([
            'userId' => $user->id,
            'nomorAnggota' => '26000099',
            'name' => 'Tira Setyani',
            'gender' => 'P',
            'email' => 'tira@example.com',
            'phone' => '08123456789',
            'status' => 'ACTIVE',
            'joinDate' => '2024-01-01',
            'simpananPokok' => 200000,
            'simpananWajib' => 1200000,
        ]);

        $loan = Loan::create([
            'member_id' => $member->id,
            'amount' => 10000000,
            'remainingAmount' => 7500000,
            'monthlyPayment' => 500000,
            'interestRate' => 0.0,
            'tenor' => 20,
            'paid_installments' => 5,
            'status' => 'ACTIVE',
            'loanSource' => 'BERMADANI',
            'purpose' => 'Pembiayaan Renovasi',
            'startDate' => now()->subMonths(5),
            'endDate' => now()->addMonths(15),
        ]);

        $this->actingAs($user);

        Livewire::test(Loans::class)
            ->assertSet('totalOutstanding', 7500000.0)
            ->assertSet('totalMonthlyPayment', 500000.0)
            ->assertSee('Pembiayaan Syariah Saya')
            ->assertSee('7.500.000')
            ->assertSee('Pembiayaan Renovasi');

        Livewire::test(LoanDetail::class, ['loan' => $loan])
            ->assertSee('Detail')
            ->assertSee('Riwayat Angsuran')
            ->assertSee('Pembiayaan Renovasi')
            ->assertSee('7.500.000');
    }
}
