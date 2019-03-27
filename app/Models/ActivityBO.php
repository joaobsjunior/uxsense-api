<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class ActivityBO {

    public static function register(Activity $activity) {
        $data = $activity->getDataDiff();
        unset($data['id']);
        $callback = DB::table('activity')->insertGetId($data);
        if ($callback) {
            $activity->setId($callback);
            return $activity;
        }
        return false;
    }

    public static function change($id, $diff) {
        $callback = DB::table('activity')->where('idactivity', $id)->update($diff);
        if ($callback) {
            return ActivityBO::get($id);
        }
        return false;
    }

    public static function delete($id) {
        $callback = DB::table('activity')->where('idactivity', $id)->delete();
        $response = false;
        if ($callback) {
            $response = true;
        }
        return $response;
    }

    public static function listAll() {
        $data = DB::table('activity')
                ->orderBy('description', 'asc')
                ->get();
        $return = [];
        $return['activities'] = [];

        
        foreach ($data as $value) {
            $return['activities'][] = (new Activity([
                'id' => $value->idactivity,
                'description' => $value->description
            ]));
        }
        return $return;
    }

    public static function get($id) {
        $data = DB::table('activity')->where('idactivity', $id)->first();
        if ($data) {
            return (new Activity([
                'id' => $data->idactivity,
                'description' => $data->description
            ]));
        }
        return false;
    }

}
