<?php

namespace App\Console\Commands;

use App\Models\TeamPlayer;
use Illuminate\Console\Command;

class MigratePlayerPhotos extends Command
{
    protected $signature = 'players:migrate-photos {--dry-run : Show what would happen without making changes}';
    protected $description = 'One-time migration: replace old player images with DHPhoto files from Karim';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $dhPhotoDir = public_path('uploads/players/DHPhoto');

        if (!is_dir($dhPhotoDir)) {
            $this->error("DHPhoto directory not found at: {$dhPhotoDir}");
            return 1;
        }

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE — no changes will be made.');
        }

        $players = TeamPlayer::all();
        $stats = [
            'total'       => $players->count(),
            'updated'     => 0,
            'no_dhphoto'  => 0,
            'old_deleted' => 0,
            'already_set' => 0,
        ];

        $bar = $this->output->createProgressBar($players->count());
        $bar->start();

        foreach ($players as $player) {
            $dhPhotoFile = $dhPhotoDir . '/' . $player->player_id . '.jpg';

            if (!file_exists($dhPhotoFile)) {
                $stats['no_dhphoto']++;
                $bar->advance();
                continue;
            }

            $newImagePath = 'uploads/players/DHPhoto/' . $player->player_id . '.jpg';

            // Check if already pointing to the DHPhoto
            if ($player->image === $newImagePath) {
                $stats['already_set']++;
                $bar->advance();
                continue;
            }

            if (!$isDryRun) {
                // Delete old image file (only if it's not the same as new and not null)
                if ($player->image && $player->image !== $newImagePath) {
                    $oldPath = public_path($player->image);
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                        $stats['old_deleted']++;
                    }
                }

                // Update DB to point to the new DHPhoto path
                TeamPlayer::where('player_id', $player->player_id)->update([
                    'image'      => $newImagePath,
                    'image_hash' => null, // Reset hash — will be recalculated on next sync
                ]);
            }

            $stats['updated']++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Players', $stats['total']],
                ['✅ Updated to DHPhoto', $stats['updated']],
                ['⏭️  No DHPhoto found', $stats['no_dhphoto']],
                ['🗑️  Old files deleted', $stats['old_deleted']],
                ['✔️  Already set', $stats['already_set']],
            ]
        );

        if ($isDryRun) {
            $this->warn('This was a DRY RUN. Run without --dry-run to apply changes.');
        } else {
            $this->info('✅ Migration complete! Player images updated to DHPhoto files.');
        }

        return 0;
    }
}
