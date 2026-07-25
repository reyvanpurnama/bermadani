<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;

class TiraSeeder extends Seeder
{
    public function run()
    {
        $email = 'tira@bermadani.id';

        // Ensure User exists/updated
        $user = User::where('email', $email)->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Tira Setyani',
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => 'MEMBER',
            ]);
        } else {
            $user->update([
                'name' => 'Tira Setyani',
            ]);
        }

        // Create or Update Member Profile for Tira Setyani
        Member::updateOrCreate(
            ['userId' => $user->id],
            [
                'nomorAnggota' => 'MM-2026-001',
                'name' => 'Tira Setyani',
                'email' => $email,
                'phone' => '081234567890',
                'address' => 'Bandung, Jawa Barat',
                'gender' => 'FEMALE',
                'unitKerja' => 'Retail',
                'status' => 'ACTIVE',
                'isMemberKoperasi' => false,

                // SAVING BALANCES & PREFERENCES
                'simpananSukarela' => 1150000.00, // 2.300.000 / 2
                'simpananWajib' => 1150000.00,    // 23 bulan x 50.000

                'simwa_payment_method' => 'SALARY_DEDUCTION',
                'monthly_simpanan_wajib' => 50000.00, // 50.000 simpanan wajib per bulan

                'sukarela_payment_method' => 'SALARY_DEDUCTION',
                'monthly_sukarela_amount' => 50000.00, // 50.000 simpanan sukarela per bulan
            ]
        );

        $this->command->info('Tira Setyani updated successfully with divided savings (1.150.000 Wajib, 1.150.000 Sukarela) and monthly 50k Wajib + 50k Sukarela billing!');
    }
}
