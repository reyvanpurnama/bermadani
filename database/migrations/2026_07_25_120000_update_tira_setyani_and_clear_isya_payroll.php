<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Member;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update/Setup Tira Setyani member data
        $tiraMember = Member::where('name', 'like', '%Tira%')
            ->orWhere('email', 'tira@bermadani.id')
            ->first();

        if ($tiraMember) {
            $tiraMember->update([
                'name' => 'Tira Setyani',
                'isMemberKoperasi' => false,
                'simpananSukarela' => 1150000.00,
                'simpananWajib' => 1150000.00,
                'monthly_simpanan_wajib' => 50000.00,
                'simwa_payment_method' => 'SALARY_DEDUCTION',
                'monthly_sukarela_amount' => 50000.00,
                'sukarela_payment_method' => 'SALARY_DEDUCTION',
            ]);

            if ($tiraMember->userId) {
                User::where('id', $tiraMember->userId)->update([
                    'name' => 'Tira Setyani',
                ]);
            }
        }

        // 2. Clear Bu Isya from active payroll / pending bills if present
        $isyaMemberIds = Member::where('name', 'like', '%Isya%')->pluck('id');
        if ($isyaMemberIds->isNotEmpty()) {
            DB::table('simpanan_transactions')
                ->whereIn('memberId', $isyaMemberIds)
                ->where('status', 'PENDING')
                ->delete();

            // Set monthly deduction to 0 for Isya
            Member::whereIn('id', $isyaMemberIds)->update([
                'monthly_simpanan_wajib' => 0,
                'monthly_sukarela_amount' => 0,
                'simwa_payment_method' => 'MANUAL',
                'sukarela_payment_method' => 'MANUAL',
                'status' => 'INACTIVE',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert logic if necessary
    }
};
