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

        // Ensure User doesn't exist
        $user = User::where('email', $email)->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Tira',
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => 'MEMBER',
            ]);
        }

        // Create Member Profile
        Member::updateOrCreate(
            ['userId' => $user->id],
            [
                'nomorAnggota' => 'MM-2026-001',
                'name' => 'Tira',
                'email' => $email, // Duplicate email in members table (legacy schema)
                'phone' => '081234567890',
                'address' => 'Bandung, Jawa Barat',
                'gender' => 'FEMALE',
                'unitKerja' => 'Other', // Or 'Minimarket'
                'status' => 'ACTIVE',
                'isMemberKoperasi' => false,

                // SAVING PREFERENCES
                'simwa_payment_method' => 'MANUAL', // No Payroll Cut for 50k
                'monthly_simpanan_wajib' => 0,      // Explicitly 0

                'sukarela_payment_method' => 'SALARY_DEDUCTION', // YES Payroll Cut
                'monthly_sukarela_amount' => 100000,             // 100k
            ]
        );

        $this->command->info('Tira inserted successfully as Minimarket Member!');
    }
}
