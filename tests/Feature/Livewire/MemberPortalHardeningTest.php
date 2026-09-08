<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Member\Profile;
use App\Livewire\Member\Transfer;
use App\Livewire\Member\TransferHistory;
use App\Models\Member;
use App\Models\SimpananTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MemberPortalHardeningTest extends TestCase
{
    use RefreshDatabase;

    private function makeMember(string $status = 'ACTIVE'): array
    {
        $user = User::factory()->create(['role' => 'MEMBER', 'isActive' => true]);
        $member = Member::create([
            'userId' => $user->id,
            'nomorAnggota' => '2600' . str_pad((string) $user->id, 4, '0', STR_PAD_LEFT),
            'name' => 'Anggota Uji',
            'email' => $user->email,
            'phone' => '08123456789',
            'gender' => 'P',
            'status' => $status,
            'isMemberKoperasi' => true,
            'joinDate' => now()->toDateString(),
            'simpananSukarela' => 100000,
        ]);

        return [$user, $member];
    }

    public function test_inactive_member_is_redirected_away_from_transfer(): void
    {
        [$user] = $this->makeMember('INACTIVE');

        $this->actingAs($user)
            ->get(route('member.transfer'))
            ->assertRedirect(route('member.dashboard'))
            ->assertSessionHas('info');
    }

    public function test_inactive_member_cannot_call_profile_or_transfer_mutations(): void
    {
        [$user] = $this->makeMember('RESIGNED');

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->call('updateProfile')
            ->assertHasErrors('member');

        Livewire::actingAs($user)
            ->test(Transfer::class)
            ->call('proceedToConfirm')
            ->assertHasErrors('member');
    }

    public function test_active_member_can_open_transfer_route(): void
    {
        [$user] = $this->makeMember();

        $this->actingAs($user)
            ->get(route('member.transfer'))
            ->assertOk();
    }

    public function test_member_cannot_open_another_members_transfer_receipt(): void
    {
        [$user] = $this->makeMember();
        [$otherUser, $otherMember] = $this->makeMember();

        $receipt = SimpananTransaction::create([
            'memberId' => $otherMember->id,
            'type' => 'SUKARELA',
            'transactionType' => 'TRANSFER_IN',
            'amount' => 50000,
            'balanceAfter' => 150000,
            'processedBy' => $otherUser->id,
            'approvedBy' => $otherUser->id,
            'approvedAt' => now(),
            'status' => 'APPROVED',
            'transferReference' => 'TRF-PRIVATE-001',
        ]);

        Livewire::actingAs($user)
            ->test(TransferHistory::class)
            ->call('viewReceipt', $receipt->id)
            ->assertStatus(404);
    }
}
