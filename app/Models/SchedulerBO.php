<?php

namespace App\Models;

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

	$string = '
	SELECT * FROM scheduler
	WHERE sent = 0
	';
        if (isset($data['question_id'])) {
            $string .= 'AND idquestion = '.$data['question_id'];
        }
        $string .= '
        ORDER BY date '.(isset($data['date']) ? $data['date'] : 'asc').',
	time '.(isset($data['date']) ? $data['date'] : 'asc').'
        ';
        $request = DB::select($string);
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
	$data = collect(DB::select('
	SELECT * FROM scheduler
	WHERE idscheduler = '.$id.'
	'))->first();
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

    public static function getSchedulerPendentByClient() {
        date_default_timezone_set('America/Bahia');
         $data = collect(DB::select('
		SELECT * FROM scheduler
		JOIN team_client ON team_client.idteam = scheduler.idteam
		WHERE team_client.idclient = '.$GLOBALS["device"]->idclient.'
		AND scheduler.idscheduler
		NOT IN (SELECT answer.idscheduler FROM answer WHERE answer.idclient = '.$GLOBALS["device"]->idclient.')
		AND scheduler.date = DATE(NOW())
		AND scheduler.time <= TIME(NOW())
		ORDER BY scheduler.time ASC
		LIMIT 1;
	  '))->first();
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
