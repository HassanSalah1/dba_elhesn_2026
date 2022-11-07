<?php

namespace App\Console\Commands;

use App\Models\TeamPlayer;
use App\Repositories\Api\Setting\SettingApiRepository;
use App\Repositories\Api\SqlServerApiRepository;
use App\Repositories\General\UtilsRepository;
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
                $playerId = $player->player_id;
                $image = null;
                $sql = "SELECT TOP 1 PlayerPhoto FROM dbo.MobileApp_PlayersPhotos WHERE PlayerRowID=$playerId";
                $file_id = 'IMG_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
                $image_path = 'uploads/players/';
                if (($result = \sqlsrv_query($conn, $sql)) !== false) {
                    $object = sqlsrv_fetch_object($result);
                    if ($object) {
                        $image = UtilsRepository::createImageBase64(base64_encode($object->PlayerPhoto), $image_path, $file_id, 282, 561);
                    }
                }
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
