<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class AnswerBO {

    public static function register(Answer $answer) {
        $data = $answer->getDataDB();
        $callback = DB::table('answer')
        ->where('idclient', $answer->getIdclient())
        ->where('idscheduler', $answer->getIdscheduler())
        ->first();
        if ($callback) {
            $answer->setId($callback->idanswer);
            return $answer;
        } else {
            if (DB::getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE answer AUTO_INCREMENT=1');
            }
            $callback2 = DB::table('answer')->insertGetId($data);
            if ($callback2) {
                $answer->setId($callback2);
                return $answer;
            }
        }
        return false;
    }

    /**
     * Update an answer. When $idclient is given the answer must belong to that
     * client, so a device cannot modify other clients' answers.
     */
    public static function change($id, $diff, $idclient = null) {
        $query = DB::table('answer')->where('idanswer', $id);
        if ($idclient !== null) {
            $query->where('idclient', $idclient);
        }
        $callback = $query->update($diff);
        if ($callback) {
            return AnswerBO::get($id);
        }
        return false;
    }

    public static function listAll() {
        $data = DB::table('answer')
                ->where('idclient', $GLOBALS['device']->idclient)
                ->orderBy('date', 'desc')
                ->orderBy('time', 'desc')
                ->limit(10)
                ->get();
        $return = [];
        $return['answers'] = [];
        foreach ($data as $value) {
            $return['answers'][] = new Answer([
                'id' => $value->idanswer,
                'date' => $value->date,
                'time' => $value->time,
                'answer' => $value->answer,
                'latitude' => $value->latitude,
                'longitude' => $value->longitude,
                'client_id' => $value->idclient,
                'scheduler_id' => $value->idscheduler,
                'technique_id' => $value->capture_technique_id
            ]);
        }
        return $return;
    }

    public static function get($id, $idclient = null) {
        $query = DB::table('answer')->where('idanswer', $id);
        if ($idclient !== null) {
            $query->where('idclient', $idclient);
        }
        $data = $query->first();
        if ($data) {
            return new Answer([
                'id' => $data->idanswer,
                'date' => $data->date,
                'time' => $data->time,
                'answer' => $data->answer,
                'latitude' => $data->latitude,
                'longitude' => $data->longitude,
                'client_id' => $data->idclient,
                'scheduler_id' => $data->idscheduler,
                'technique_id' => $data->capture_technique_id
            ]);
        }
        return false;
    }

}
