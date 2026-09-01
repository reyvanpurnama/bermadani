<?php

namespace App\Http\Controllers;

use App\Models\CooperativeSetting;
use App\Shared\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InstallerController extends Controller
{
    /**
     * Helper: Check system requirements.
     */
    private function checkRequirements(): array
    {
        $requirements = [
            'php' => [
                'name' => 'PHP Version >= 8.2',
                'pass' => version_compare(PHP_VERSION, '8.2.0', '>='),
                'current' => PHP_VERSION,
            ],
            'pdo' => [
                'name' => 'PDO Extension',
                'pass' => extension_loaded('pdo') && extension_loaded('pdo_mysql'),
            ],
            'mbstring' => [
                'name' => 'Mbstring Extension',
                'pass' => extension_loaded('mbstring'),
            ],
            'openssl' => [
                'name' => 'OpenSSL Extension',
                'pass' => extension_loaded('openssl'),
            ],
            'curl' => [
                'name' => 'cURL Extension',
                'pass' => extension_loaded('curl'),
            ],
            'fileinfo' => [
                'name' => 'FileInfo Extension',
                'pass' => extension_loaded('fileinfo'),
            ],
            'storage_writable' => [
                'name' => 'Storage Directory Writable',
                'pass' => is_writable(storage_path()),
            ],
            'bootstrap_writable' => [
                'name' => 'Bootstrap Cache Writable',
                'pass' => is_writable(base_path('bootstrap/cache')),
            ],
        ];

        $allPassed = collect($requirements)->every(fn($item) => $item['pass']);

        return [$requirements, $allPassed];
    }

    /**
     * Step 1: Requirements Check
     */
    public function step1()
    {
        if (file_exists(storage_path('installed'))) {
            return redirect()->route('home');
        }

        [$requirements, $allPassed] = $this->checkRequirements();

        return view('installer.step1', compact('requirements', 'allPassed'));
    }

    /**
     * Step 2: Database Configuration Form
     */
    public function step2()
    {
        if (file_exists(storage_path('installed'))) {
            return redirect()->route('home');
        }

        [$requirements, $allPassed] = $this->checkRequirements();
        if (!$allPassed) {
            return redirect()->route('installer.step1');
        }

        return view('installer.step2');
    }

    /**
     * Test Database Connection (AJAX/POST)
     */
    public function testDatabase(Request $request)
    {
        $request->validate([
            'db_host' => 'required|string',
            'db_port' => 'required|numeric',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        try {
            $connection = new \PDO(
                "mysql:host={$request->db_host};port={$request->db_port};dbname={$request->db_database}",
                $request->db_username,
                $request->db_password,
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );

            return response()->json([
                'success' => true,
                'message' => 'Koneksi database berhasil!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Koneksi gagal: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Step 3: Koperasi Identity & Admin Form
     */
    public function step3(Request $request)
    {
        if (file_exists(storage_path('installed'))) {
            return redirect()->route('home');
        }

        // Store DB config in session for final processing
        if ($request->isMethod('post')) {
            $request->validate([
                'db_host' => 'required|string',
                'db_port' => 'required|numeric',
                'db_database' => 'required|string',
                'db_username' => 'required|string',
                'db_password' => 'nullable|string',
            ]);

            session([
                'installer_db' => [
                    'host' => $request->db_host,
                    'port' => $request->db_port,
                    'database' => $request->db_database,
                    'username' => $request->db_username,
                    'password' => $request->db_password,
                ]
            ]);
        }

        if (!session('installer_db')) {
            return redirect()->route('installer.step2');
        }

        return view('installer.step3');
    }

    /**
     * Process Full Installation
     */
    public function processInstall(Request $request)
    {
        if (file_exists(storage_path('installed'))) {
            return redirect()->route('home');
        }

        $dbConfig = session('installer_db');
        if (!$dbConfig) {
            return redirect()->route('installer.step2');
        }

        $request->validate([
            'coop_name' => 'required|string|max:255',
            'coop_short_name' => 'required|string|max:50',
            'coop_email_domain' => 'required|string|max:100',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255',
            'admin_password' => 'required|string|min:8|confirmed',
        ]);

        try {
            // 1. Update .env file
            $this->updateEnv([
                'APP_URL' => url('/'),
                'DB_HOST' => $dbConfig['host'],
                'DB_PORT' => $dbConfig['port'],
                'DB_DATABASE' => $dbConfig['database'],
                'DB_USERNAME' => $dbConfig['username'],
                'DB_PASSWORD' => $dbConfig['password'] ?? '',
                'COOP_NAME' => $request->coop_name,
                'COOP_SHORT_NAME' => $request->coop_short_name,
                'COOP_EMAIL_DOMAIN' => $request->coop_email_domain,
            ]);

            // 2. Set dynamic DB connection in runtime
            config([
                'database.connections.mysql.host' => $dbConfig['host'],
                'database.connections.mysql.port' => $dbConfig['port'],
                'database.connections.mysql.database' => $dbConfig['database'],
                'database.connections.mysql.username' => $dbConfig['username'],
                'database.connections.mysql.password' => $dbConfig['password'] ?? '',
            ]);
            DB::purge('mysql');
            DB::reconnect('mysql');

            // 3. Run Migrations
            Artisan::call('migrate:fresh', ['--force' => true]);

            // 4. Seed Default Cooperative Settings
            Artisan::call('db:seed', ['--class' => 'CooperativeSettingsSeeder', '--force' => true]);

            // Update DB Settings with custom inputs
            CooperativeSetting::setValue('coop_name', $request->coop_name, 'general', 'Nama Koperasi');
            CooperativeSetting::setValue('coop_short_name', $request->coop_short_name, 'general', 'Nama Singkat');
            CooperativeSetting::setValue('coop_email_domain', $request->coop_email_domain, 'general', 'Email Domain');

            // 5. Create Super Admin User
            $admin = User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'role' => 'SUPER_ADMIN',
            ]);

            // 6. Generate APP_KEY if not set
            if (empty(env('APP_KEY'))) {
                Artisan::call('key:generate', ['--force' => true]);
            }

            // 7. Write Installation Lock File
            file_put_contents(storage_path('installed'), date('Y-m-d H:i:s'));

            // Clear session & cache
            session()->forget(['installer_db']);
            Artisan::call('config:clear');
            Artisan::call('cache:clear');

            return redirect()->route('installer.success')->with('admin_email', $request->admin_email);

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Gagal menginstal: ' . $e->getMessage()]);
        }
    }

    /**
     * Installation Success Screen
     */
    public function success()
    {
        return view('installer.success');
    }

    /**
     * Helper: Update .env file key-value pairs
     */
    private function updateEnv(array $data): void
    {
        $envPath = base_path('.env');
        if (!file_exists($envPath)) {
            copy(base_path('.env.example'), $envPath);
        }

        $envContent = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            // Escape values with spaces or quotes
            $formattedValue = (str_contains($value, ' ') || str_contains($value, '#')) ? '"' . trim($value, '"') . '"' : $value;

            if (preg_match("/^{$key}=/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$formattedValue}", $envContent);
            } else {
                $envContent .= "\n{$key}={$formattedValue}";
            }
        }

        file_put_contents($envPath, $envContent);
    }
}
