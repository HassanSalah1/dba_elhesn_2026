<?php
$file = '/Users/hassansalah/projects/milestone_apps/dba_elhesn/app/Repositories/Api/V2/Setting/SettingApiRepository.php';
$content = file_get_contents($file);

// Replace getCompetitionMatches logic
$oldGetComp = <<<'EOF'
    public static function getCompetitionMatches(array $data)
    {
        $lang = \Illuminate\Support\Facades\App::getLocale();
        $type = isset($data['type']) ? $data['type'] : 'today';
        $todayStr = date('Y-m-d');
        
        if (!isset($data['competition_id'])) {
            return [
                'message' => trans('api.general_error_message'),
                'code' => \App\Entities\HttpCode::ERROR
            ];
        }

        $query = \App\Models\SportMatch::with(['team1Club', 'team2Club'])
            ->where('competition_row_id', $data['competition_id']);

        if ($type === 'previous') {
            $query->where('match_date', '<', $todayStr)->orderBy('match_date', 'desc');
        } elseif ($type === 'week') {
            $today = \Carbon\Carbon::today();
            $startOfWeek = $today->copy()->startOfWeek(\Carbon\Carbon::SUNDAY)->format('Y-m-d');
            $endOfWeek = $today->copy()->endOfWeek(\Carbon\Carbon::SATURDAY)->format('Y-m-d');
            $query->whereBetween('match_date', [$startOfWeek, $endOfWeek])->orderBy('match_date', 'asc');
        } elseif ($type === 'upcoming' || $type === 'next') {
            $query->where('match_date', '>', $todayStr)->orderBy('match_date', 'asc');
        } else {
            $query->where('match_date', '=', $todayStr)->orderBy('match_date', 'asc');
        }
EOF;

$newGetComp = <<<'EOF'
    public static function getCompetitionMatches(array $data)
    {
        $lang = \Illuminate\Support\Facades\App::getLocale();
        $type = isset($data['type']) ? $data['type'] : null;
        $todayStr = date('Y-m-d');
        
        if (!isset($data['competition_id'])) {
            return [
                'message' => trans('api.general_error_message'),
                'code' => \App\Entities\HttpCode::ERROR
            ];
        }

        $query = \App\Models\SportMatch::with(['team1Club', 'team2Club'])
            ->where('competition_row_id', $data['competition_id'])
            ->where(function($q) {
                $q->where('team1_row_id', 21)->orWhere('team2_row_id', 21);
            });

        if ($type === 'previous') {
            $query->where('match_date', '<', $todayStr)->orderBy('match_date', 'desc')->orderBy('match_time', 'desc');
        } elseif ($type === 'week') {
            $today = \Carbon\Carbon::today();
            $startOfWeek = $today->copy()->startOfWeek(\Carbon\Carbon::SUNDAY)->format('Y-m-d');
            $endOfWeek = $today->copy()->endOfWeek(\Carbon\Carbon::SATURDAY)->format('Y-m-d');
            $query->whereBetween('match_date', [$startOfWeek, $endOfWeek])->orderBy('match_date', 'asc');
        } elseif ($type === 'upcoming' || $type === 'next') {
            $query->where('match_date', '>', $todayStr)->orderBy('match_date', 'asc');
        } elseif ($type === 'today') {
            $query->where('match_date', '=', $todayStr)->orderBy('match_date', 'asc');
        } else {
            $query->orderBy('match_date', 'desc')->orderBy('match_time', 'desc');
        }
EOF;
$content = str_replace($oldGetComp, $newGetComp, $content);

// Apply translations helper
$helper = <<<'EOF'
    private static function mapMatchData($match, $lang) {
        $team1Name = $match->team1;
        $team2Name = $match->team2;
        $team1Logo = $match->team1Club && $match->team1Club->logo ? url($match->team1Club->logo) : null;
        $team2Logo = $match->team2Club && $match->team2Club->logo ? url($match->team2Club->logo) : null;
        
        if ($lang == 'en') {
            if ($match->team1Club && $match->team1Club->name_en) $team1Name = $match->team1Club->name_en;
            if ($match->team2Club && $match->team2Club->name_en) $team2Name = $match->team2Club->name_en;
        }

        if (!$team1Logo || ($lang == 'en' && $team1Name == $match->team1)) {
            $club1 = \App\Models\Club::where('name_ar', $match->team1)->orWhere('name_en', $match->team1)->first();
            if ($club1) {
                if (!$team1Logo && $club1->logo) $team1Logo = url($club1->logo);
                if ($lang == 'en' && $club1->name_en) $team1Name = $club1->name_en;
            } else {
                $t1 = \App\Models\SportTeam::where('name_ar', $match->team1)->orWhere('name_en', $match->team1)->first();
                if ($t1) {
                    if (!$team1Logo && $t1->image) $team1Logo = url($t1->image);
                    if ($lang == 'en' && $t1->name_en) $team1Name = $t1->name_en;
                }
            }
        }
        if (!$team1Logo) $team1Logo = url('images/default-logo.png');
        
        if (!$team2Logo || ($lang == 'en' && $team2Name == $match->team2)) {
            $club2 = \App\Models\Club::where('name_ar', $match->team2)->orWhere('name_en', $match->team2)->first();
            if ($club2) {
                if (!$team2Logo && $club2->logo) $team2Logo = url($club2->logo);
                if ($lang == 'en' && $club2->name_en) $team2Name = $club2->name_en;
            } else {
                $t2 = \App\Models\SportTeam::where('name_ar', $match->team2)->orWhere('name_en', $match->team2)->first();
                if ($t2) {
                    if (!$team2Logo && $t2->image) $team2Logo = url($t2->image);
                    if ($lang == 'en' && $t2->name_en) $team2Name = $t2->name_en;
                }
            }
        }
        if (!$team2Logo) $team2Logo = url('images/default-logo.png');

        return [
            'id' => $match->row_id,
            'team1' => $team1Name,
            'team1_logo' => $team1Logo,
            'team2' => $team2Name,
            'team2_logo' => $team2Logo,
            'team1_result' => $match->team1_result,
            'team2_result' => $match->team2_result,
            'match_date' => $match->match_date,
            'match_time' => $match->match_time,
            'stage_round' => $match->stage_round,
            'pitch' => $match->pitch,
            'week' => $match->week,
            'live_link' => $match->live_link,
            'fanet_match_id' => $match->fanet_match_id,
            'competition_name' => $match->competition ? ($lang == 'ar' ? $match->competition->name_ar : $match->competition->name_en) : null,
            'season_name' => ($match->competition && $match->competition->season) ? $match->competition->season->name : null,
        ];
    }
EOF;
$content = preg_replace('/class SettingApiRepository\s*\{/', "class SettingApiRepository\n{\n$helper\n", $content);

// Replace getMatchesGrouped inner loop
$oldLoop = <<<'EOF'
                // Determine logo URLs
                $team1Logo = $match->team1Club && $match->team1Club->logo ? url($match->team1Club->logo) : null;
                $team2Logo = $match->team2Club && $match->team2Club->logo ? url($match->team2Club->logo) : null;
                
                // Fallbacks if null
                if (!$team1Logo) {
                    $club1 = \App\Models\Club::where('name_ar', $match->team1)->orWhere('name_en', $match->team1)->first();
                    if ($club1 && $club1->logo) $team1Logo = url($club1->logo);
                    else {
                        $team1 = \App\Models\SportTeam::where('name_ar', $match->team1)->orWhere('name_en', $match->team1)->first();
                        $team1Logo = $team1 && $team1->image ? url($team1->image) : url('images/default-logo.png');
                    }
                }
                
                if (!$team2Logo) {
                    $club2 = \App\Models\Club::where('name_ar', $match->team2)->orWhere('name_en', $match->team2)->first();
                    if ($club2 && $club2->logo) $team2Logo = url($club2->logo);
                    else {
                        $team2 = \App\Models\SportTeam::where('name_ar', $match->team2)->orWhere('name_en', $match->team2)->first();
                        $team2Logo = $team2 && $team2->image ? url($team2->image) : url('images/default-logo.png');
                    }
                }

                $matchesData[] = [
                    'id' => $match->row_id,
                    'team1' => $match->team1,
                    'team1_logo' => $team1Logo,
                    'team2' => $match->team2,
                    'team2_logo' => $team2Logo,
                    'team1_result' => $match->team1_result,
                    'team2_result' => $match->team2_result,
                    'match_date' => $match->match_date,
                    'match_time' => $match->match_time,
                    'stage_round' => $match->stage_round,
                    'pitch' => $match->pitch,
                    'week' => $match->week,
                    'live_link' => $match->live_link,
                    'fanet_match_id' => $match->fanet_match_id,
                ];
EOF;
$newLoop = <<<'EOF'
                $matchesData[] = self::mapMatchData($match, $lang);
EOF;
$content = str_replace($oldLoop, $newLoop, $content);

// Fix getMatchDetails
$oldDetails = <<<'EOF'
        $team1Logo = $match->team1Club && $match->team1Club->logo ? url($match->team1Club->logo) : null;
        $team2Logo = $match->team2Club && $match->team2Club->logo ? url($match->team2Club->logo) : null;
        
        if (!$team1Logo) {
            $club1 = \App\Models\Club::where('name_ar', $match->team1)->orWhere('name_en', $match->team1)->first();
            if ($club1 && $club1->logo) $team1Logo = url($club1->logo);
            else {
                $team1 = \App\Models\SportTeam::where('name_ar', $match->team1)->orWhere('name_en', $match->team1)->first();
                $team1Logo = $team1 && $team1->image ? url($team1->image) : url('images/default-logo.png');
            }
        }
        
        if (!$team2Logo) {
            $club2 = \App\Models\Club::where('name_ar', $match->team2)->orWhere('name_en', $match->team2)->first();
            if ($club2 && $club2->logo) $team2Logo = url($club2->logo);
            else {
                $team2 = \App\Models\SportTeam::where('name_ar', $match->team2)->orWhere('name_en', $match->team2)->first();
                $team2Logo = $team2 && $team2->image ? url($team2->image) : url('images/default-logo.png');
            }
        }
        
        $matchData = [
            'id' => $match->row_id,
            'team1' => $match->team1,
            'team1_logo' => $team1Logo,
            'team2' => $match->team2,
            'team2_logo' => $team2Logo,
            'team1_result' => $match->team1_result,
            'team2_result' => $match->team2_result,
            'match_date' => $match->match_date,
            'match_time' => $match->match_time,
            'stage_round' => $match->stage_round,
            'pitch' => $match->pitch,
            'week' => $match->week,
            'live_link' => $match->live_link,
            'fanet_match_id' => $match->fanet_match_id,
            'competition_name' => $match->competition ? ($lang == 'ar' ? $match->competition->name_ar : $match->competition->name_en) : null,
            'season_name' => ($match->competition && $match->competition->season) ? $match->competition->season->name : null,
        ];
EOF;
$newDetails = <<<'EOF'
        $matchData = self::mapMatchData($match, $lang);
EOF;
$content = str_replace($oldDetails, $newDetails, $content);

// Rewrite getNextHeroMatch completely (to restore the list return + flag)
$oldHero = <<<'EOF'
    public static function getNextHeroMatch(array $data)
    {
        $todayStr = date('Y-m-d');
        
        $match = \App\Models\SportMatch::with(['team1Club', 'team2Club', 'competition.season'])
            ->where('match_date', '>=', $todayStr)
            ->orderBy('match_date', 'asc')
            ->orderBy('match_time', 'asc')
            ->first();

        if (!$match) {
            return [
                'data' => null,
                'message' => 'success',
                'code' => \App\Entities\HttpCode::SUCCESS
            ];
        }

        $team1Logo = $match->team1Club && $match->team1Club->logo ? url($match->team1Club->logo) : null;
        $team2Logo = $match->team2Club && $match->team2Club->logo ? url($match->team2Club->logo) : null;
        
        if (!$team1Logo) {
            $club1 = \App\Models\Club::where('name_ar', $match->team1)->orWhere('name_en', $match->team1)->first();
            if ($club1 && $club1->logo) $team1Logo = url($club1->logo);
            else {
                $team1 = \App\Models\SportTeam::where('name_ar', $match->team1)->orWhere('name_en', $match->team1)->first();
                $team1Logo = $team1 && $team1->image ? url($team1->image) : url('images/default-logo.png');
            }
        }
        
        if (!$team2Logo) {
            $club2 = \App\Models\Club::where('name_ar', $match->team2)->orWhere('name_en', $match->team2)->first();
            if ($club2 && $club2->logo) $team2Logo = url($club2->logo);
            else {
                $team2 = \App\Models\SportTeam::where('name_ar', $match->team2)->orWhere('name_en', $match->team2)->first();
                $team2Logo = $team2 && $team2->image ? url($team2->image) : url('images/default-logo.png');
            }
        }

        $lang = \Illuminate\Support\Facades\App::getLocale();
        
        $matchData = [
            'id' => $match->row_id,
            'team1' => $match->team1,
            'team1_logo' => $team1Logo,
            'team2' => $match->team2,
            'team2_logo' => $team2Logo,
            'match_date' => $match->match_date,
            'match_time' => $match->match_time,
            'stage_round' => $match->stage_round,
            'pitch' => $match->pitch,
            'week' => $match->week,
            'live_link' => $match->live_link,
            'fanet_match_id' => $match->fanet_match_id,
        ];

        return [
            'data' => $matchData,
            'message' => 'success',
            'code' => \App\Entities\HttpCode::SUCCESS
        ];
    }
EOF;
$newHero = <<<'EOF'
    public static function getNextHeroMatch(array $data)
    {
        $todayStr = date('Y-m-d');
        
        $matches = \App\Models\SportMatch::with(['team1Club', 'team2Club', 'competition.season'])
            ->whereHas('competition', function($q) {
                $q->where('mobile_app_header_comp', 1);
            })
            ->where('match_date', '>=', $todayStr)
            ->orderBy('match_date', 'asc')
            ->orderBy('match_time', 'asc')
            ->get();

        if ($matches->isEmpty()) {
            return [
                'data' => [],
                'message' => 'success',
                'code' => \App\Entities\HttpCode::SUCCESS
            ];
        }

        $lang = \Illuminate\Support\Facades\App::getLocale();
        $matchesData = [];

        foreach ($matches as $match) {
            $matchesData[] = self::mapMatchData($match, $lang);
        }

        return [
            'data' => $matchesData,
            'message' => 'success',
            'code' => \App\Entities\HttpCode::SUCCESS
        ];
    }
EOF;
$content = str_replace($oldHero, $newHero, $content);

// Also restore getMatchesGrouped optional type logic
$content = str_replace("\$type = isset(\$data['type']) ? \$data['type'] : 'today';", "\$type = isset(\$data['type']) ? \$data['type'] : null;", $content);
$content = str_replace("} else {\n                \$q->where('match_date', '=', \$todayStr)->orderBy('match_date', 'asc');\n            }", "} elseif (\$type === 'today') {\n                \$q->where('match_date', '=', \$todayStr)->orderBy('match_date', 'asc');\n            } else {\n                \$q->orderBy('match_date', 'desc')->orderBy('match_time', 'desc');\n            }", $content);

file_put_contents($file, $content);
echo "SettingApiRepository updated.\n";
EOF
