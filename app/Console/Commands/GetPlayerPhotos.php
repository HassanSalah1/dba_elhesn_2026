<?php

namespace App\Console\Commands;

use App\Models\TeamPlayer;
use App\Repositories\Api\Setting\SettingApiRepository;
use App\Repositories\Api\SqlServerApiRepository;
use Illuminate\Console\Command;

class GetPlayerPhotos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'player_photos:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $conn = SqlServerApiRepository::startConnection();
        if ($conn) {
            $players = TeamPlayer::where('image', '=', null)->get();
            foreach ($players as $player) {
                $image = SqlServerApiRepository::getPlayerImage($conn, $player->player_id);
                $this->info('image : ' . $image);
                if ($image) {
                    $player->update([
                        'image' => $image
                    ]);
                }
            }
        }
        $this->info('Successfully');
        return Command::SUCCESS;
    }
}
