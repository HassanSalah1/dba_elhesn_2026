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
        SqlServerApiRepository::getPlayerImages();
        $this->info('Successfully');
        return Command::SUCCESS;
    }
}
