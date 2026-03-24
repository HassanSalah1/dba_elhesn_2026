<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DeployCheck extends Command
{
    protected $signature = 'deploy:check';

    protected $description = 'Verify deployment: PHP, extensions, env, database, storage';

    public function handle(): int
    {
        $this->info('=== Deployment Verification ===');
        $this->newLine();

        $checks = [];
        $checks[] = $this->checkPhpVersion();
        $checks[] = $this->checkExtensions();
        $checks[] = $this->checkEnv();
        $checks[] = $this->checkAppKey();
        $checks[] = $this->checkMysqlConnection();
        $checks[] = $this->checkSqlServerConnection();
        $checks[] = $this->checkStorageLink();
        $checks[] = $this->checkPassportKeys();

        $this->newLine();
        $this->table(
            ['Check', 'Status', 'Details'],
            $checks
        );

        $failed = array_filter($checks, fn ($c) => $c[1] === 'FAIL');
        if (count($failed) > 0) {
            $this->error('Some checks failed. Review the table above.');
            return Command::FAILURE;
        }

        $this->info('All critical checks passed.');
        return Command::SUCCESS;
    }

    private function checkPhpVersion(): array
    {
        $version = PHP_VERSION;
        $ok = version_compare($version, '8.2.0', '>=');
        return ['PHP Version', $ok ? 'OK' : 'FAIL', $version . ($ok ? '' : ' (need 8.2+)')];
    }

    private function checkExtensions(): array
    {
        $required = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'json', 'curl', 'fileinfo', 'sodium'];
        $optional = ['sqlsrv', 'pdo_sqlsrv'];

        $missing = [];
        foreach ($required as $ext) {
            if (! extension_loaded($ext)) {
                $missing[] = $ext;
            }
        }

        $optionalMissing = [];
        foreach ($optional as $ext) {
            if (! extension_loaded($ext)) {
                $optionalMissing[] = $ext;
            }
        }

        $status = count($missing) === 0 ? 'OK' : 'FAIL';
        $details = count($missing) > 0
            ? 'Missing: ' . implode(', ', $missing)
            : 'All required OK' . (count($optionalMissing) > 0 ? ' | Optional missing: ' . implode(', ', $optionalMissing) : '');

        return ['Extensions', $status, $details];
    }

    private function checkEnv(): array
    {
        $path = base_path('.env');
        $exists = File::exists($path);
        return ['.env file', $exists ? 'OK' : 'FAIL', $exists ? 'Found' : 'Missing'];
    }

    private function checkAppKey(): array
    {
        $key = config('app.key');
        $ok = ! empty($key) && strlen($key) > 20;
        return ['APP_KEY', $ok ? 'OK' : 'FAIL', $ok ? 'Set' : 'Not set or invalid'];
    }

    private function checkMysqlConnection(): array
    {
        try {
            DB::connection('mysql')->getPdo();
            return ['MySQL', 'OK', 'Connected'];
        } catch (\Throwable $e) {
            return ['MySQL', 'FAIL', $e->getMessage()];
        }
    }

    private function checkSqlServerConnection(): array
    {
        if (! function_exists('sqlsrv_connect')) {
            return ['SQL Server', 'SKIP', 'ext-sqlsrv not installed'];
        }

        $result = \App\Repositories\Api\SqlServerApiRepository::testConnection();

        return ['SQL Server', $result['ok'] ? 'OK' : 'FAIL', $result['message']];
    }

    private function checkStorageLink(): array
    {
        $link = public_path('storage');
        $target = storage_path('app/public');
        $exists = File::exists($link);

        if (! $exists) {
            return ['Storage Link', 'FAIL', 'Run: php artisan storage:link'];
        }

        $isLink = is_link($link);
        if (! $isLink) {
            return ['Storage Link', 'WARN', 'public/storage exists but is not a symlink'];
        }

        return ['Storage Link', 'OK', 'Linked'];
    }

    private function checkPassportKeys(): array
    {
        $publicKey = storage_path('oauth-public.key');
        $privateKey = storage_path('oauth-private.key');

        if (! File::exists($publicKey) || ! File::exists($privateKey)) {
            return ['Passport Keys', 'FAIL', 'Run: php artisan passport:install'];
        }

        return ['Passport Keys', 'OK', 'Found'];
    }
}
