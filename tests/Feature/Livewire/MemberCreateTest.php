<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Admin\MemberCreate;
use App\Models\Member;
use App\Models\SimpananTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MemberCreateTest extends TestCase
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

    public function test_member_registration_with_lunas_option_requires_min_200k(): void
    {
        $admin = $this->makeAdmin();

        Livewire::actingAs($admin)
            ->test(MemberCreate::class)
            // Step 1
            ->set('name', 'Budi Santoso')
            ->set('phone', '081234567890')
            ->set('gender', 'MALE')
            ->set('unitKerja', 'Dosen')
            ->set('address', 'Bandung')
            ->call('nextStep') // currentStep becomes 2
            // Step 2 with LUNAS
            ->set('simpananPokokOption', 'LUNAS')
            ->set('simpananPokok', 150000)
            ->call('nextStep')
            ->assertHasErrors(['simpananPokok' => 'min']);
    }

    public function test_member_registration_with_cicilan_option_requires_min_50k(): void
    {
        $admin = $this->makeAdmin();

        Livewire::actingAs($admin)
            ->test(MemberCreate::class)
            // Step 1
            ->set('name', 'Budi Santoso')
            ->set('phone', '081234567890')
            ->set('gender', 'MALE')
            ->set('unitKerja', 'Dosen')
            ->set('address', 'Bandung')
            ->call('nextStep') // currentStep becomes 2
            // Step 2 with CICIL_4X
            ->set('simpananPokokOption', 'CICIL_4X')
            ->set('simpananPokok', 40000)
            ->call('nextStep')
            ->assertHasErrors(['simpananPokok' => 'min']);
    }

    public function test_successful_registration_with_lunas_option(): void
    {
        $admin = $this->makeAdmin();

        Livewire::actingAs($admin)
            ->test(MemberCreate::class)
            // Step 1
            ->set('name', 'Budi Santoso')
            ->set('phone', '081234567890')
            ->set('gender', 'MALE')
            ->set('unitKerja', 'Dosen')
            ->set('address', 'Bandung')
            ->call('nextStep')
            // Step 2
            ->set('simpananPokokOption', 'LUNAS')
            ->set('simpananPokok', 200000)
            ->set('simpananWajib', 50000)
            ->set('simpananSukarela', 10000)
            ->call('nextStep')
            // Step 3
            ->call('submit');

        $this->assertDatabaseHas('members', [
            'name' => 'Budi Santoso',
            'simpananPokok' => 200000,
            'simpananWajib' => 50000,
            'simpananSukarela' => 10000,
        ]);

        $member = Member::where('name', 'Budi Santoso')->firstOrFail();

        // Should have 3 completed transactions (POKOK, WAJIB, SUKARELA)
        $this->assertSame(3, SimpananTransaction::where('memberId', $member->id)->count());
        // No future unpaid bills
        $this->assertSame(0, SimpananTransaction::where('memberId', $member->id)
            ->where('billStatus', 'APPROVED')
            ->whereColumn('paidAmount', '<', 'amount')
            ->count());
    }

    public function test_successful_registration_with_cicilan_option_creates_future_bills(): void
    {
        $admin = $this->makeAdmin();

        Livewire::actingAs($admin)
            ->test(MemberCreate::class)
            // Step 1
            ->set('name', 'Budi Santoso')
            ->set('phone', '081234567890')
            ->set('gender', 'MALE')
            ->set('unitKerja', 'Dosen')
            ->set('address', 'Bandung')
            ->call('nextStep')
            // Step 2
            ->set('simpananPokokOption', 'CICIL_4X')
            ->set('simpananPokok', 50000)
            ->set('simpananWajib', 50000)
            ->set('simpananSukarela', 10000)
            ->call('nextStep')
            // Step 3
            ->call('submit');

        $this->assertDatabaseHas('members', [
            'name' => 'Budi Santoso',
            'simpananPokok' => 50000,
            'simpananWajib' => 50000,
            'simpananSukarela' => 10000,
        ]);

        $member = Member::where('name', 'Budi Santoso')->firstOrFail();

        // Should have 3 paid initial transactions (POKOK = 50k, WAJIB = 50k, SUKARELA = 10k)
        // AND 3 future unpaid bills of POKOK = 50k each
        $this->assertSame(6, SimpananTransaction::where('memberId', $member->id)->count());

        $bills = SimpananTransaction::where('memberId', $member->id)
            ->where('type', 'POKOK')
            ->where('paidAmount', 0)
            ->where('billStatus', 'APPROVED')
            ->get();

        $this->assertCount(3, $bills);
        foreach ($bills as $bill) {
            $this->assertEquals(50000, $bill->amount);
            $this->assertNotNull($bill->billingMonth);
        }
    }
}
