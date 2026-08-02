<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Dashboard;
use App\Models\FinancialTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        return User::factory()->create([
            'role' => 'ADMIN',
            'isActive' => true,
        ]);
    }

    public function test_operating_expenses_excludes_shu_and_net_profit_is_unaffected(): void
    {
        $admin = $this->makeAdmin();

        // 1. Create manual income of 1,000,000 to have some profit
        FinancialTransaction::create([
            'type' => 'INCOME',
            'category' => 'Omset Penjualan (Historis)',
            'amount' => 1000000.00,
            'transactionDate' => now()->toDateString(),
            'description' => 'Sales',
            'userId' => $admin->id,
        ]);

        // 2. Create normal operating expense of 200,000
        FinancialTransaction::create([
            'type' => 'EXPENSE',
            'category' => 'Biaya Operasional',
            'amount' => 200000.00,
            'transactionDate' => now()->toDateString(),
            'description' => 'Internet bill',
            'userId' => $admin->id,
        ]);

        // 3. Create SHU disbursement expense of 300,000
        FinancialTransaction::create([
            'type' => 'EXPENSE',
            'category' => 'Pembagian SHU',
            'amount' => 300000.00,
            'transactionDate' => now()->toDateString(),
            'description' => 'Disbursed SHU',
            'userId' => $admin->id,
        ]);

        // Test the calculations on the Dashboard
        Livewire::actingAs($admin)
            ->test(Dashboard::class)
            ->assertSet('operatingExpenses', 200000.00) // Excludes SHU (300,000)
            ->assertSet('equityOutflows', 300000.00) // Correctly maps SHU
            ->assertSet('netProfit', 800000.00) // 1,000,000 (Income) - 200,000 (Operating Expense) = 800,000 (SHU is excluded!)
            ->assertSet('cashOnHand', 500000.00); // 1,000,000 (Income) - 200,000 (Operating Expense) - 300,000 (SHU) = 500,000 (SHU is included!)
    }
}
