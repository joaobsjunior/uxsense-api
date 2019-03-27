<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class GroupBO {

    public static function register(Group $group) {
        $data = $group->getDataDiff();
        unset($data['id']);
        $callback = DB::table('group')->insertGetId($data);
        if ($callback) {
            $group->setId($callback);
            return $group;
        }
        return false;
    }

    public static function change($id, $diff) {
        $callback = DB::table('group')->where('idgroup', $id)->update($diff);
        if ($callback) {
            return GroupBO::get($id);
        }
        return false;
    }

    public static function delete($id) {
        $callback = DB::table('group')->where('idgroup', $id)->delete();
        if ($callback) {
            return true;
        }
        return false;
    }

    public static function listAll() {
        $request = DB::table('group')
                ->orderBy('name', 'asc');
        if (isset($GLOBALS["administrator"])) {
            $group_id = GroupBO::getObjectByUser($GLOBALS["administrator"]->idadministrator);
            if ($group_id) {
                $request->where('idgroup', $group_id->idgroup);
            }
        }
        $data = $request->get();
        $return = [];
        $return['groups'] = [];
        foreach ($data as $value) {
            $return['groups'][] = (new Group([
                'id' => $value->idgroup,
                'name' => $value->name
            ]));
        }
        return $return;
    }

    public static function get($id) {
        $data = DB::table('group')->where('idgroup', $id)->first();
        if ($data) {
            return (new Group([
                'id' => $data->idgroup,
                'name' => $data->name
            ]));
        }
        return false;
    }

    public static function getObjectByUser($id_user) {
        $administrator_group = DB::table('administrator_group')
                ->where('idadministrator', $id_user)
                ->first();
        if ($administrator_group) {
            return $administrator_group;
        }
        return false;
    }

    public static function getByClientID($client_id) {
        $data = DB::table('team_client')
                ->select('group.*')
                ->join('team', 'team_client.idteam', '=', 'team.idteam')
                ->join('subgroup', 'team.idsubgroup', '=', 'subgroup.idsubgroup')
                ->join('group', 'subgroup.idgroup', '=', 'group.idgroup')
                ->where('team_client.idclient', $client_id)
                ->distinct()
                ->first();
        if ($data) {
            return (new Group([
                'id' => $data->idgroup,
                'name' => $data->name
            ]));
        }
        return false;
    }

}
