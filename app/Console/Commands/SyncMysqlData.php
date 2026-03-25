<?php

namespace App\Console\Commands;

use App\Repositories\Api\SqlServerApiRepository;
use Illuminate\Console\Command;

class SyncMysqlData extends Command
{
    protected $signature = 'mysql:sync {--table=all : Table to sync (teams, players, users, user_teams, all)}';

    protected $description = 'Sync MySQL data with SQL Server: upsert existing records, delete orphaned ones';

    private array $validTables = ['teams', 'players', 'users', 'user_teams', 'all'];

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $table = $this->option('table');

        if (!in_array($table, $this->validTables)) {
            $this->error('Invalid --table option. Allowed values: ' . implode(', ', $this->validTables));
            return Command::FAILURE;
        }

        $this->warn('⚠  This will DELETE any MySQL records not found in SQL Server.');
        $this->warn('   Order of sync: teams → players → users → user_teams');
        $this->newLine();

        if (!$this->confirm('Are you sure you want to continue?')) {
            $this->info('Cancelled.');
            return Command::SUCCESS;
        }

        $rows = [];

        // Order matters: teams and users must exist before user_teams
        if (in_array($table, ['teams', 'all'])) {
            $this->line('Syncing <info>sport_teams</info>...');
            $stats  = SqlServerApiRepository::syncTeamsWithSqlServer();
            $rows[] = ['sport_teams', $stats['upserted'], $stats['deleted']];
        }

        if (in_array($table, ['players', 'all'])) {
            $this->line('Syncing <info>team_players</info>...');
            $stats  = SqlServerApiRepository::syncPlayersWithSqlServer();
            $rows[] = ['team_players', $stats['upserted'], $stats['deleted']];
        }

        if (in_array($table, ['users', 'all'])) {
            $this->line('Syncing <info>users</info> (SQL Server origin only)...');
            $stats  = SqlServerApiRepository::syncUsersWithSqlServer();
            $rows[] = ['users', $stats['upserted'], $stats['deleted']];
        }

        if (in_array($table, ['user_teams', 'all'])) {
            $this->line('Syncing <info>user_teams</info>...');
            $stats  = SqlServerApiRepository::syncUserTeamsWithSqlServer();
            $rows[] = ['user_teams', $stats['upserted'], $stats['deleted']];
        }

        $this->newLine();
        $this->table(
            ['Table', 'Upserted (add/update)', 'Deleted (orphaned)'],
            $rows
        );

        return Command::SUCCESS;
    }
}
