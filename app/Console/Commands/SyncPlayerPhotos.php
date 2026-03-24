<?php

namespace App\Console\Commands;

use App\Repositories\Api\SqlServerApiRepository;
use Illuminate\Console\Command;

class SyncPlayerPhotos extends Command
{
    protected $signature = 'player_photos:sync';

    protected $description = 'Sync all missing player photos from SQL Server in one run';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $total = \App\Models\TeamPlayer::where('image', '=', null)->count();

        if ($total === 0) {
            $this->info('All player photos are already synced.');
            return Command::SUCCESS;
        }

        $this->info("Found {$total} players without photos. Starting sync...");

        $bar = $this->output->createProgressBar($total);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% | Updated: %updated% | Skipped: %skipped%');
        $bar->setMessage('0', 'updated');
        $bar->setMessage('0', 'skipped');
        $bar->start();

        $stats = SqlServerApiRepository::syncPlayerImages(function ($stats) use ($bar) {
            $bar->setMessage((string) $stats['updated'], 'updated');
            $bar->setMessage((string) $stats['skipped'], 'skipped');
            $bar->advance();
        });

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Processed', 'Updated (with photo)', 'Skipped (no photo in SQL Server)'],
            [[$stats['processed'], $stats['updated'], $stats['skipped']]]
        );

        return Command::SUCCESS;
    }
}
