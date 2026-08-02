<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Admin\RatSetup;
use App\Livewire\Admin\RatEligibility;
use App\Livewire\Admin\RatAllocation;
use App\Livewire\Admin\RatDisbursement;
use App\Models\Member;
use App\Models\MemberShuDistribution;
use App\Models\RatSession;
use App\Models\User;
use App\Services\ShuCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RatSessionManagementTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        return User::factory()->create([
            'role' => 'ADMIN',
            'isActive' => true,
        ]);
    }

    private function createMember(string $nomorAnggota, string $name, string $joinDate, float $simpananPokok, float $simpananWajib): Member
    {
        $user = User::factory()->create([
            'role' => 'MEMBER',
            'isActive' => true,
            'email' => $nomorAnggota . '@example.com',
        ]);

        return Member::create([
            'userId' => $user->id,
            'nomorAnggota' => $nomorAnggota,
            'name' => $name,
            'email' => $nomorAnggota . '@example.com',
            'gender' => 'MALE',
            'unitKerja' => 'Testing',
            'joinDate' => $joinDate,
            'status' => 'ACTIVE',
            'simpananPokok' => $simpananPokok,
            'simpananWajib' => $simpananWajib,
        ]);
    }

    public function test_rat_setup_and_5_pos_allocation(): void
    {
        $admin = $this->makeAdmin();

        Livewire::actingAs($admin)
            ->test(RatSetup::class)
            ->set('year', 2025)
            ->set('title', 'RAT 2025 Test')
            ->set('totalNetProfit', 20000000)
            ->set('totalMemberShu', 10000000)
            ->set('cadanganPercentage', 25)
            ->set('jasaSimpananPercentage', 30)
            ->set('jasaUsahaPercentage', 25)
            ->set('pengurusPercentage', 10)
            ->set('danaSosialPercentage', 10)
            ->call('saveSession');

        $session = RatSession::where('year', 2025)->firstOrFail();
        $this->assertEquals(20000000, (float) $session->total_net_profit);
        $this->assertEquals(10000000, (float) $session->total_member_shu);
        $this->assertEquals(25, (float) $session->cadangan_percentage);
        $this->assertEquals(30, (float) $session->jasa_simpanan_percentage);
        $this->assertEquals(RatSession::STATUS_DRAFT, $session->status);
    }

    public function test_rat_eligibility_filtering_and_exclusion(): void
    {
        $admin = $this->makeAdmin();

        $memberA = $this->createMember('24000001', 'Member A', '2024-01-01 00:00:00', 500000, 500000);
        $memberB = $this->createMember('25000001', 'Member B', '2025-01-01 00:00:00', 500000, 500000);
        $memberC = $this->createMember('24000002', 'Member C', '2024-01-01 00:00:00', 500000, 500000);

        $session = RatSession::create([
            'year' => 2025,
            'event_date' => '2025-12-31',
            'title' => 'RAT 2025',
            'total_net_profit' => 20000000,
            'total_member_shu' => 10000000,
            'status' => RatSession::STATUS_CONFIGURED,
        ]);

        Livewire::actingAs($admin)
            ->test(RatEligibility::class, ['session' => $session->id])
            ->set('joinDateCutoff', '2024-12-31')
            ->call('toggleMemberExclusion', $memberC->id)
            ->call('saveEligibility');

        $session->refresh();
        $this->assertEquals('2024-12-31', $session->join_date_cutoff->format('Y-m-d'));
        $this->assertContains($memberC->id, $session->excluded_member_ids);

        // Verify eligibility logic via ShuCalculationService
        $service = new ShuCalculationService();
        $this->assertTrue($service->isMemberEligible($memberA->id, $memberA->joinDate->format('Y-m-d H:i:s'), 'ACTIVE', '2024-12-31', [$memberC->id]));
        $this->assertFalse($service->isMemberEligible($memberB->id, $memberB->joinDate->format('Y-m-d H:i:s'), 'ACTIVE', '2024-12-31', [$memberC->id]));
        $this->assertFalse($service->isMemberEligible($memberC->id, $memberC->joinDate->format('Y-m-d H:i:s'), 'ACTIVE', '2024-12-31', [$memberC->id]));
    }

    public function test_rat_allocation_and_finalization(): void
    {
        $admin = $this->makeAdmin();

        $memberA = $this->createMember('24000001', 'Member A', '2024-01-01', 500000, 500000);

        $session = RatSession::create([
            'year' => 2025,
            'event_date' => '2025-12-31',
            'title' => 'RAT 2025',
            'total_net_profit' => 20000000,
            'total_member_shu' => 10000000,
            'jasa_simpanan_percentage' => 30,
            'jasa_usaha_percentage' => 25,
            'status' => RatSession::STATUS_MEMBERS_LOCKED,
        ]);

        Livewire::actingAs($admin)
            ->test(RatAllocation::class, ['session' => $session->id])
            ->call('recalculate')
            ->call('finalizeSession');

        $session->refresh();
        $this->assertEquals(RatSession::STATUS_FINALIZED, $session->status);
        $this->assertNotNull($session->finalized_at);

        $distA = MemberShuDistribution::where('rat_session_id', $session->id)->where('member_id', $memberA->id)->firstOrFail();
        $this->assertGreaterThan(0, (float) $distA->shu_amount);
    }

    public function test_shu_disbursement_triggers_financial_transaction(): void
    {
        $admin = $this->makeAdmin();
        $member = $this->createMember('24000001', 'Member A', '2024-01-01', 500000, 500000);

        $session = RatSession::create([
            'year' => 2025,
            'event_date' => '2025-12-31',
            'title' => 'RAT 2025',
            'total_net_profit' => 10000000,
            'total_member_shu' => 5000000,
            'status' => RatSession::STATUS_FINALIZED,
        ]);

        $service = new ShuCalculationService();
        $service->calculateAndSaveDistributions($session);

        $dist = MemberShuDistribution::where('rat_session_id', $session->id)->where('member_id', $member->id)->firstOrFail();

        Livewire::actingAs($admin)
            ->test(RatDisbursement::class, ['session' => $session->id])
            ->call('toggleDisbursed', $dist->id);

        $dist->refresh();
        $this->assertTrue($dist->is_disbursed);
        $this->assertNotNull($dist->financial_transaction_id);

        $this->assertDatabaseHas('financial_transactions', [
            'id' => $dist->financial_transaction_id,
            'type' => 'EXPENSE',
            'category' => 'Pembagian SHU',
        ]);
    }
}
