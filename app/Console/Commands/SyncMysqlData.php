<?php

namespace App\Console\Commands;

use App\Repositories\Api\SqlServerApiRepository;
use App\Repositories\Api\V2\SqlServerApiRepository as V2SqlServerApiRepository;
use Illuminate\Console\Command;

class SyncMysqlData extends Command
{
    protected $signature = 'mysql:sync {--table=all : Table to sync (teams, players, player_details, users, user_teams, matches, seasons, attend_reasons, clubs, competitions, standings, clinic_time_slots, clinic_bookings, hr_categories, hr_employees, hr_attendance, hr_leave_types, hr_leave_requests, hr_documents, all)} {--force : Skip confirmation prompt (for cron/scheduler)}';

    protected $description = 'Sync MySQL data with SQL Server: upsert existing records, delete orphaned ones';

    private array $validTables = ['teams', 'players', 'player_details', 'users', 'user_teams', 'matches', 'seasons', 'attend_reasons', 'clubs', 'competitions', 'standings', 'clinic_time_slots', 'clinic_bookings', 'hr_categories', 'hr_employees', 'hr_attendance', 'hr_leave_types', 'hr_leave_requests', 'hr_documents', 'all'];

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
        $this->warn('   Order of sync: teams → players → player_details → users → user_teams → matches → seasons → attend_reasons → clubs → competitions → standings → clinic_time_slots → clinic_bookings');
        $this->newLine();

        if (!$this->option('force') && !$this->confirm('Are you sure you want to continue?')) {
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

        if (in_array($table, ['player_details', 'all'])) {
            $this->line('Syncing <info>player_details</info> from MobileApp DB...');
            $stats  = V2SqlServerApiRepository::syncPlayerDetailsFromMobileApp();
            $rows[] = ['player_details', $stats['updated'], $stats['skipped']];
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

        if (in_array($table, ['matches', 'all'])) {
            $this->line('Syncing <info>sport_matches</info>...');
            $stats  = V2SqlServerApiRepository::syncMatchesWithSqlServer();
            $rows[] = ['sport_matches', $stats['upserted'], $stats['deleted']];
        }

        if (in_array($table, ['seasons', 'all'])) {
            $this->line('Syncing <info>seasons</info>...');
            $stats  = V2SqlServerApiRepository::syncSeasonsWithSqlServer();
            $rows[] = ['seasons', $stats['upserted'], $stats['deleted']];
        }

        if (in_array($table, ['attend_reasons', 'all'])) {
            $this->line('Syncing <info>attend_reasons</info>...');
            $stats  = V2SqlServerApiRepository::syncAttendReasonsWithSqlServer();
            $rows[] = ['attend_reasons', $stats['upserted'], $stats['deleted']];
        }

        if (in_array($table, ['clubs', 'all'])) {
            $this->line('Syncing <info>clubs</info>...');
            $stats  = V2SqlServerApiRepository::syncClubsWithSqlServer();
            $rows[] = ['clubs', $stats['upserted'], $stats['deleted']];
        }

        if (in_array($table, ['competitions', 'all'])) {
            $this->line('Syncing <info>competitions</info>...');
            $stats  = V2SqlServerApiRepository::syncCompetitionsWithSqlServer();
            $rows[] = ['competitions', $stats['upserted'], $stats['deleted']];
        }

        if (in_array($table, ['standings', 'all'])) {
            $this->line('Syncing <info>league_standings</info>...');
            $stats  = V2SqlServerApiRepository::syncLeagueStandingsWithSqlServer();
            $rows[] = ['league_standings', $stats['upserted'], $stats['deleted']];
        }

        if (in_array($table, ['clinic_time_slots', 'all'])) {
            $this->line('Syncing <info>clinic_time_slots</info> from SQL Server...');
            $stats  = V2SqlServerApiRepository::syncClinicTimeSlotsWithSqlServer();
            $rows[] = ['clinic_time_slots', $stats['upserted'], $stats['deleted']];
        }

        if (in_array($table, ['clinic_bookings', 'all'])) {
            $this->line('Pushing <info>clinic_bookings</info> to SQL Server...');
            $stats  = V2SqlServerApiRepository::pushClinicBookingsToSqlServer();
            $rows[] = ['clinic_bookings (push)', $stats['pushed'], $stats['failed']];
        }

        // HR Sync Steps
        if (in_array($table, ['hr_categories', 'all'])) {
            $this->line('Syncing <info>hr_employee_categories</info>...');
            $stats  = V2SqlServerApiRepository::syncHrEmployeeCategoriesWithSqlServer();
            $rows[] = ['hr_employee_categories', $stats['upserted'], 0];
        }

        if (in_array($table, ['hr_employees', 'all'])) {
            $this->line('Syncing <info>hr_employees</info>...');
            $stats  = V2SqlServerApiRepository::syncHrEmployeesWithSqlServer();
            $rows[] = ['hr_employees', $stats['upserted'], 0];
        }

        if (in_array($table, ['hr_attendance', 'all'])) {
            $this->line('Syncing <info>hr_attendance_records</info>...');
            $stats  = V2SqlServerApiRepository::syncHrAttendanceRecordsWithSqlServer();
            $rows[] = ['hr_attendance_records', $stats['upserted'], 0];
        }

        if (in_array($table, ['hr_leave_types', 'all'])) {
            $this->line('Syncing <info>hr_leave_types</info>...');
            $stats  = V2SqlServerApiRepository::syncHrLeaveTypesWithSqlServer();
            $rows[] = ['hr_leave_types', $stats['upserted'], 0];
        }

        if (in_array($table, ['hr_leave_requests', 'all'])) {
            $this->line('Pushing <info>hr_leave_requests</info> to SQL Server...');
            $stats  = V2SqlServerApiRepository::pushHrLeaveRequestsToSqlServer();
            $rows[] = ['hr_leave_requests (push)', $stats['pushed'], $stats['failed']];
        }

        if (in_array($table, ['hr_documents', 'all'])) {
            $this->line('Pushing <info>hr_documents</info> to SQL Server...');
            $stats  = V2SqlServerApiRepository::pushHrDocumentsToSqlServer();
            $rows[] = ['hr_documents (push)', $stats['pushed'], $stats['failed']];
        }

        $this->newLine();
        $this->table(
            ['Table', 'Upserted (add/update)', 'Deleted (orphaned)'],
            $rows
        );

        return Command::SUCCESS;
    }
}
