<?php

namespace App\Models;

use App\Models\Scheduler;
use Carbon\Carbon;
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
        Log::info('PushNotification::getScheduler()');
        $now = Carbon::now();
        $data = DB::table('scheduler')
            ->where('sent', 0)
            ->where('date', $now->toDateString())
            ->where('time', '<=', $now->toTimeString())
            ->get();
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
        return $return;
    }

}
