<?php

namespace App\Console\Commands;

use App\Repositories\Api\Setting\SettingApiRepository;
use App\Repositories\Api\SqlServerApiRepository;
use Illuminate\Console\Command;

class GetUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:daily';

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
        SqlServerApiRepository::getUsers();
        $this->info('Successfully');
        return Command::SUCCESS;
    }
}
