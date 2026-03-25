<?php

namespace App\Console\Commands;

use App\Models\SportTeam;
use App\Models\TeamPlayer;
use App\Models\User;
use App\Models\UserTeam;
use App\Repositories\Api\SqlServerApiRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FreshMysqlData extends Command
{
    protected $signature = 'mysql:fresh';

    protected $description = 'Wipe MySQL SQL-Server-sourced tables and re-import everything fresh from SQL Server';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->warn('⚠  WARNING: This will WIPE and re-import the following tables:');
        $this->line('   • user_teams');
        $this->line('   • team_players');
        $this->line('   • sport_teams');
        $this->line('   • users (SQL Server origin only — local admins/fans are safe)');
        $this->newLine();

        if (!$this->confirm('Are you absolutely sure?', false)) {
            $this->info('Cancelled.');
            return Command::SUCCESS;
        }

        // Step 1: Wipe in reverse dependency order
        $this->line('Wiping tables...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        UserTeam::truncate();
        TeamPlayer::truncate();
        SportTeam::truncate();
        User::whereNotNull('user_id')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->info('Tables wiped.');
        $this->newLine();

        // Step 2: Re-import in dependency order
        $rows = [];

        $this->line('Importing <info>sport_teams</info>...');
        $stats  = SqlServerApiRepository::syncTeamsWithSqlServer();
        $rows[] = ['sport_teams', $stats['upserted'], $stats['deleted']];

        $this->line('Importing <info>team_players</info>...');
        $stats  = SqlServerApiRepository::syncPlayersWithSqlServer();
        $rows[] = ['team_players', $stats['upserted'], $stats['deleted']];

        $this->line('Importing <info>users</info>...');
        $stats  = SqlServerApiRepository::syncUsersWithSqlServer();
        $rows[] = ['users', $stats['upserted'], $stats['deleted']];

        $this->line('Importing <info>user_teams</info>...');
        $stats  = SqlServerApiRepository::syncUserTeamsWithSqlServer();
        $rows[] = ['user_teams', $stats['upserted'], $stats['deleted']];

        $this->newLine();
        $this->info('Done! Fresh import summary:');
        $this->table(['Table', 'Imported', 'Deleted (orphaned)'], $rows);

        return Command::SUCCESS;
    }
}
