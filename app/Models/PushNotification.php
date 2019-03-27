<?php

namespace App\Models;

use App\Models\Scheduler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PushNotification
{

    public static function getDevicesByScheduler(Scheduler $scheduler)
    {
        $data = DB::table('scheduler')
            ->select('device.*')
            ->join('team_client', 'team_client.idteam', '=', 'scheduler.idteam')
            ->join('device', 'team_client.idclient', '=', 'device.idclient')
            ->where('scheduler.idscheduler', $scheduler->getId())
            ->whereNotNull('device.registratorid')
            ->whereNotNull('device.token')
            ->get();
        $return = [];
        $return['devices'] = [];
        foreach ($data as $value) {
            $return['devices'][] = new Device([
                'id' => $value->iddevice,
                'client_id' => $value->idclient,
                'registrator_id' => $value->registratorid,
                'platform' => $value->platform,
                'uuid' => $value->uuid,
                'token' => $value->token,
            ]);
        }
        return $return;
    }

    public static function setSchedulerSent(Scheduler $schuduler)
    {
        return DB::table('scheduler')->where('idscheduler', $schuduler->getId())->update(['sent' => 1]);
    }

    public static function getScheduler()
    {
        date_default_timezone_set('America/Bahia');
        Log::info('PushNotification::getScheduler()');
        $data = DB::select('SELECT * FROM scheduler WHERE scheduler.sent = 0 AND scheduler.date = DATE(NOW()) AND scheduler.time <= time(NOW())');
        Log::info($data);
        $return = [];
        $return['schedulers'] = [];
        foreach ($data as $value) {
            $return['schedulers'][] = (new Scheduler([
                'id' => $value->idscheduler,
                'date' => $value->date,
                'time' => $value->time,
                'question_id' => $value->idquestion,
                'team_id' => $value->idteam,
                'technique_id' => $value->capture_technique_id,
            ]));
        }
        Log::info($return);
        return $return;
    }

}
