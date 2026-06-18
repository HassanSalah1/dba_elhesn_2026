<?php
 
namespace App\Http\Controllers\Api\V2;
 
use App\Http\Controllers\Controller;
use App\Repositories\Api\V2\SqlServerApiRepository;
use Illuminate\Http\Request;
 
class SqlServerController extends Controller
{
 
    public function getSports(Request $request)
    {
        echo phpinfo();
        die();
    }
 
    public function getTeams(Request $request)
    {
        return SqlServerApiRepository::getPlayerImages();
    }
}
