<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SchedulerBO {

    public static function register($array = []) {
        switch ($array['type_scheduler']) {
            case 'admin':
                return SchedulerBO::registerPerAdmin($array);
            case 'group':
                return SchedulerBO::registerPerGroup($array);
            case 'subgroup':
                return SchedulerBO::registerPerSubgroup($array);
            case 'custom':
                return SchedulerBO::registerPerCustom($array);
        }
        return false;
    }

    private static function registerPerAdmin($array) {
        $scheduler = new Scheduler($array);
        if (!Helpers::isNullOrEmpty($array['admin_id'])) {
            DB::statement("CALL SCHEDULER_PER_ADMIN(?,?,?,?,?,?,@result)", array(
                $scheduler->getQuestionId(),
                $array['admin_id'],
                $scheduler->getDate(),
                $scheduler->getTime(),
                SchedulerBO::getQtdRepeater($array),
                $scheduler->getTechniqueId()
            ));
            $result = DB::select('SELECT @result AS result');
            if ($result && count($result)) {
                return (array) $result[0];
            }
        }
        return false;
    }

    private static function registerPerGroup($array) {
        $scheduler = new Scheduler($array);
        if (!Helpers::isNullOrEmpty($array['group_id'])) {
            DB::statement("CALL SCHEDULER_PER_GROUP(?,?,?,?,?,?,@result)", array(
                $scheduler->getQuestionId(),
                $array['group_id'],
                $scheduler->getDate(),
                $scheduler->getTime(),
                SchedulerBO::getQtdRepeater($array),
                $scheduler->getTechniqueId()
            ));
            $result = DB::select('SELECT @result AS result');
            if ($result && count($result)) {
                return (array) $result[0];
            }
        }
        return false;
    }

    private static function registerPerSubgroup($array) {
        $scheduler = new Scheduler($array);
        if (!Helpers::isNullOrEmpty($array['subgroup_id'])) {
            DB::statement("CALL SCHEDULER_PER_SUBGROUP(?,?,?,?,?,?,@result)", array(
                $scheduler->getQuestionId(),
                $array['subgroup_id'],
                $scheduler->getDate(),
                $scheduler->getTime(),
                SchedulerBO::getQtdRepeater($array),
                $scheduler->getTechniqueId()
            ));
            $result = DB::select('SELECT @result AS result');
            if ($result && count($result)) {
                return (array) $result[0];
            }
        }
        return false;
    }

    private static function registerPerCustom($array) {
        $scheduler = new Scheduler($array);
        DB::statement("CALL SCHEDULER_PER_TEAM(?,?,?,?,?,?,@result)", array(
            $scheduler->getQuestionId(),
            $scheduler->getTeamId(),
            $scheduler->getDate(),
            $scheduler->getTime(),
            SchedulerBO::getQtdRepeater($array),
            $scheduler->getTechniqueId()
        ));
        $result = DB::select('SELECT @result AS result');
        if ($result && count($result)) {
            return (array) $result[0];
        }
        return false;
    }

    private static function getQtdRepeater($array) {
        $repeater = 0;
        if ($array['repeater'] == true) {
            $repeater = $array['qtd_repeater'];
        }
        return $repeater;
    }

    public static function change($id, $diff) {
        $callback = DB::table('scheduler')->where('idscheduler', $id)->update($diff);
        if ($callback) {
            return SchedulerBO::get($id);
        }
        return false;
    }

    public static function delete($id) {
        $callback = DB::table('scheduler')->where('idscheduler', $id)->delete();
        $response = false;
        if ($callback) {
            $response = true;
        }
        return $response;
    }

    public static function listAll($data) {
        $query = DB::table('scheduler')->where('sent', 0);
        if (isset($data['question_id']) && $data['question_id'] !== '') {
            $query->where('idquestion', $data['question_id']);
        }
        // Only the two SQL sort directions are accepted (this value used to be
        // concatenated straight into the ORDER BY clause).
        $direction = strtolower((string) ($data['date'] ?? 'asc')) === 'desc' ? 'desc' : 'asc';
        $request = $query->orderBy('date', $direction)->orderBy('time', $direction)->get();
        $return = [];
        $return['schedulers'] = [];
        foreach ($request as $value) {
            $return['schedulers'][] = new Scheduler([
                'id' => $value->idscheduler,
                'date' => $value->date,
                'time' => $value->time,
                'question_id' => $value->idquestion,
                'team_id' => $value->idteam,
                'technique_id' => $value->capture_technique_id
            ]);
        }
        return $return;
    }

    public static function get($id) {
        $data = DB::table('scheduler')->where('idscheduler', $id)->first();
        if ($data) {
            $scheduler = new Scheduler([
                'id' => $data->idscheduler,
                'date' => $data->date,
                'time' => $data->time,
                'question_id' => $data->idquestion,
                'team_id' => $data->idteam,
                'technique_id' => $data->capture_technique_id
            ]);
            return $scheduler;
        }
        return false;
    }

    public static function getSchedulerPendentByClient($idclient = null) {
        if ($idclient === null) {
            $idclient = $GLOBALS["device"]->idclient ?? null;
        }
        if ($idclient === null) {
            return false;
        }
        $now = Carbon::now();
        $data = DB::table('scheduler')
                ->select('scheduler.*')
                ->join('team_client', 'team_client.idteam', '=', 'scheduler.idteam')
                ->where('team_client.idclient', $idclient)
                ->whereNotIn('scheduler.idscheduler', function ($query) use ($idclient) {
                    $query->select('answer.idscheduler')
                        ->from('answer')
                        ->where('answer.idclient', $idclient);
                })
                ->where('scheduler.date', $now->toDateString())
                ->where('scheduler.time', '<=', $now->toTimeString())
                ->orderBy('scheduler.time', 'asc')
                ->first();
        if ($data) {
            $scheduler = new Scheduler([
                'id' => $data->idscheduler,
                'date' => $data->date,
                'time' => $data->time,
                'question_id' => $data->idquestion,
                'team_id' => $data->idteam,
                'technique_id' => $data->capture_technique_id
            ]);
            return $scheduler;
        }
        return false;
    }

}
