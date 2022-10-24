<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Api\SqlServerApiRepository;
use App\Services\Api\Setting\SettingApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use PDO;
use PDOException;

class SqlServerController extends Controller
{

    public function getSports(Request $request)
    {
        echo phpinfo();
        die();
//        return SqlServerApiRepository::getSports();
    }

    public function getTeams(Request $request)
    {
        return SqlServerApiRepository::getPlayerImages();
    }
}

?>
