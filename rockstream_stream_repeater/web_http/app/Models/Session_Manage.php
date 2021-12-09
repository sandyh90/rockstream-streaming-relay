<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;

class Session_Manage extends Model
{
    protected $table = 'sessions';

    public function get_session_data($get_data)
    {
        return collect(
            DB::table('sessions')->where('user_id', $get_data)->orderBy('last_activity', 'desc')->get()
        )->map(function ($session) {
            $agent = tap(new Agent, function ($agent) use ($session) {
                $agent->setUserAgent($session->user_agent);
            });

            return (object) [
                'agent' => [
                    'check_device' => $agent->isDesktop() ? 'desktop' : ($agent->isPhone() ? 'phone' : ($agent->isRobot() ? 'robot' : 'unknown')),
                    'platform' => $agent->platform() . ' ' . $agent->version($agent->platform()),
                    'browser' => $agent->browser() . ' ' . $agent->version($agent->browser()),
                ],
                'user_agent' => $session->user_agent,
                'ip_address' => $session->ip_address,
                'last_activity' => $session->last_activity,
            ];
        });
    }
}
