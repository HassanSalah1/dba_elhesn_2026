<?php
 
namespace App\Services\Api\V2\Setting;
 
use App\Repositories\Api\V2\Setting\SettingApiRepository;
use App\Repositories\General\UtilsRepository;
use App\Repositories\General\ValidationRepository;
 
class SettingApiService
{
 
    public static function getIntros(array $data)
    {
        $response = SettingApiRepository::getIntros($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function getAbout(array $data)
    {
        $response = SettingApiRepository::getAbout($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function getMagles(array $data)
    {
        $response = SettingApiRepository::getMagles($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function getClubStructure(array $data)
    {
        $response = SettingApiRepository::getClubStructure($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function getElders(array $data)
    {
        $response = SettingApiRepository::getElders($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function getTerms(array $data)
    {
        $response = SettingApiRepository::getTerms($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function getHistory(array $data)
    {
        $response = SettingApiRepository::getHistory($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function getContactDetails(array $data)
    {
        $response = SettingApiRepository::getContactDetails($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function addContact(array $data)
    {
        $data['user_id'] = auth('api')->id();
        $keys = [
            'contact_type' => 'required',
            'message' => 'required',
            'name' => 'required_without:user_id',
            'phone' => 'required_without:user_id',
        ];
        $validated = ValidationRepository::validateAPIGeneral($data, $keys);
        if ($validated !== true) {
            return $validated;
        }
        $response = SettingApiRepository::addContact($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function getCategories(array $data)
    {
        $response = SettingApiRepository::getCategories($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function getTeams(array $data)
    {
        $response = SettingApiRepository::getTeams($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
 
    public static function getGallery(array $data)
    {
        $response = SettingApiRepository::getGallery($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function getSportGames(array $data)
    {
        $response = SettingApiRepository::getSportGames($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function getSportGamesGallery(array $data)
    {
        $response = SettingApiRepository::getSportGamesGallery($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function getCommittees(array $data)
    {
        $response = SettingApiRepository::getCommittees($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function getHome(array $data)
    {
        $response = SettingApiRepository::getHome($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function getNews(array $data)
    {
        $response = SettingApiRepository::getNews($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function getNewDetails(array $data)
    {
        $response = SettingApiRepository::getNewDetails($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function getActions(array $data)
    {
        $response = SettingApiRepository::getActions($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function getActionDetails(array $data)
    {
        $response = SettingApiRepository::getActionDetails($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function getSiteNews(array $data)
    {
        $response = SettingApiRepository::getSiteNews($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function getRegulations(array $data)
    {
        $response = SettingApiRepository::getRegulations($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function getSportTeams(array $data)
    {
        $response = SettingApiRepository::getSportTeams($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function getTeamPlayers(array $data)
    {
        $response = SettingApiRepository::getTeamPlayers($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function getEmergencies(array $data)
    {
        $response = SettingApiRepository::getEmergencies($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function getTeamPlayerDetails(array $data)
    {
        $response = SettingApiRepository::getTeamPlayerDetails($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function getClinicStatus(array $data)
    {
        $response = SettingApiRepository::getClinicStatus($data);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function getSeasons(array $data)
    {
        $response = SettingApiRepository::getSeasons($data);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function getClubs(array $data)
    {
        $response = SettingApiRepository::getClubs($data);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function getMatchesGrouped(array $data)
    {
        $response = SettingApiRepository::getMatchesGrouped($data);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function getNextHeroMatch(array $data)
    {
        $response = SettingApiRepository::getNextHeroMatch($data);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function getCompetitionMatches(array $data)
    {
        $response = SettingApiRepository::getCompetitionMatches($data);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function getMatchDetails(array $data)
    {
        $response = SettingApiRepository::getMatchDetails($data);
        return UtilsRepository::handleResponseApi($response);
    }
}
