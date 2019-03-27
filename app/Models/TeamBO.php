<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class TeamBO {

    public static function register(Team $team) {
        $data = $team->getDataDiff();
        $callback = DB::table('team')->insertGetId($data);
        if ($callback) {
            $team->setId($callback);
            return $team;
        }
        return false;
    }
    
    public static function registerTeamClient($team_id, $client_id) {
        $data = [
            "idteam" => $team_id,
            "idclient" => $client_id
        ];
        return DB::table('team_client')->insert($data);
    }
    

    public static function change($id, $diff) {
        $callback = DB::table('team')->where('idteam', $id)->update($diff);
        if ($callback) {
            return TeamBO::get($id);
        }
        return false;
    }

    public static function delete($id) {
        return DB::table('team')->where('idteam', $id)->delete();
    }
    
    public static function deleteTeamClient($team_id, $client_id) {
        return DB::table('team_client')
                ->where("idteam", $team_id)
                ->where("idclient", $client_id)
                ->delete();
    }

    public static function listAll() {
        $data = DB::table('team')
                ->orderBy('name', 'desc')
                ->get();
        $return = [];
        $return['teams'] = [];
        foreach ($data as $value) {
            $return['teams'][] = (new Team([
                'id' => $value->idteam,
                'name' => $value->name,
                'unit_id' => $value->idunit,
                'subgroup_id' => $value->idsubgroup,
            ]));
        }
        return $return;
    }

    public static function listAllofSubgroup($id) {
        $data = DB::table('team')
                ->where('idsubgroup', $id)
                ->orderBy('name', 'desc')
                ->get();
        $return = [];
        $return['teams'] = [];
        foreach ($data as $value) {
            $return['teams'][] = (new Team([
                'id' => $value->idteam,
                'name' => $value->name,
                'unit_id' => $value->idunit,
                'subgroup_id' => $value->idsubgroup,
            ]));
        }
        return $return;
    }
    public static function listAllofClient($id) {
        $data = DB::table('team')
                ->select('team.*')
                ->join('team_client', 'team.idteam', '=', 'team_client.idteam')
                ->where('team_client.idclient',$id)
                ->orderBy('team.name', 'desc')
                ->get();
        $return = [];
        $return['teams'] = [];
        foreach ($data as $value) {
            $return['teams'][] = (new Team([
                'id' => $value->idteam,
                'name' => $value->name,
                'unit_id' => $value->idunit,
                'subgroup_id' => $value->idsubgroup,
            ]));
        }
        return $return;
    }

    public static function get($id) {
        $data = DB::table('team')->where('idteam', $id)->first();
        if ($data) {
            return (new Team([
                'id' => $data->idteam,
                'name' => $data->name,
                'unit_id' => $data->idunit,
                'subgroup_id' => $data->idsubgroup,
            ]));
        }
        return false;
    }

}
