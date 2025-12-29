<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UpdateMemberEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'members:update-emails {--with-users : Also create/update user accounts with default password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update member emails to nomorAnggota@bermadani.id format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating member emails...');

        $members = Member::all();
        $count = 0;
        $userCount = 0;

        DB::beginTransaction();
        try {
            foreach ($members as $member) {
                $newEmail = strtolower($member->nomorAnggota) . '@bermadani.id';
                
                // Update member email
                $member->update(['email' => $newEmail]);
                $count++;

                // Create/update user account if flag is set
                if ($this->option('with-users')) {
                    $user = User::where('email', $newEmail)->first();
                    
                    if (!$user) {
                        $user = User::create([
                            'name' => $member->name,
                            'email' => $newEmail,
                            'password' => Hash::make('password'),
                            'role' => 'MEMBER',
                        ]);
                        
                        // Link member to user
                        $member->update(['userId' => $user->id]);
                        $userCount++;
                    }
                }
            }

            DB::commit();
            $this->info("Successfully updated $count member emails to @bermadani.id format.");
            
            if ($this->option('with-users')) {
                $this->info("Created $userCount new user accounts with password: 'password'");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
