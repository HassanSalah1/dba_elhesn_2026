<?php

namespace App\Console\Commands;

use App\Repositories\Api\SqlServerApiRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestSqlServerConnection extends Command
{
    protected $signature = 'sqlserver:test';

    protected $description = 'Test connection to SQL Server (sqlsrv API + optional Laravel DB connection)';

    public function handle(): int
    {
        $this->info('Testing SQL Server connection...');
        $this->newLine();

        $result = SqlServerApiRepository::testConnection();
        if ($result['ok']) {
            $this->info('[sqlsrv_connect] ' . $result['message']);
        } else {
            $this->error('[sqlsrv_connect] ' . $result['message']);
        }

        if (extension_loaded('pdo_sqlsrv')) {
            $this->newLine();
            try {
                DB::connection('sqlsrv')->getPdo();
                $this->info('[Laravel sqlsrv] PDO connection OK (check .env DB_SQL_* matches your server).');
            } catch (\Throwable $e) {
                $this->warn('[Laravel sqlsrv] ' . $e->getMessage());
                $this->line('  (Set DB_SQL_HOST, DB_SQL_DATABASE, DB_SQL_USERNAME, DB_SQL_PASSWORD in .env if you use Laravel\'s sqlsrv connection.)');
            }
        } else {
            $this->newLine();
            $this->comment('pdo_sqlsrv not loaded — Laravel DB::connection(\'sqlsrv\') not tested.');
        }

        return $result['ok'] ? Command::SUCCESS : Command::FAILURE;
    }
}
