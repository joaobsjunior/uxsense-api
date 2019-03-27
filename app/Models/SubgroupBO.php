<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class SubgroupBO {

    public static function register(Subgroup $subgroup) {
        $data = $subgroup->getDataDiff();
        $data['idgroup'] = $subgroup->getGroupId();
        unset($data['id']);
        unset($data['group_id']);
        $callback = DB::table('subgroup')->insertGetId($data);
        if ($callback) {
            $subgroup->setId($callback);
            return $subgroup;
        }
        return false;
    }

    public static function change($id, $diff) {
        $callback = DB::table('subgroup')->where('idsubgroup', $id)->update($diff);
        if ($callback) {
            return SubgroupBO::get($id);
        }
        return false;
    }

    public static function delete($id) {
        $callback = DB::table('subgroup')->where('idsubgroup', $id)->delete();
        $response = false;
        if ($callback) {
            $response = true;
        }
        return $response;
    }

    public static function listAll() {
        $data = DB::table('subgroup')
                ->orderBy('name', 'asc')
                ->get();
        $return = [];
        $return['subgroups'] = [];
        foreach ($data as $value) {
            $return['subgroups'][] = (new Subgroup([
                'id' => $value->idsubgroup,
                'name' => $value->name,
                'complement' => $value->complement,
                'group_id' => $value->idgroup
            ]));
        }
        return $return;
    }
    
     public static function listAllofGroup($id) {
        $data = DB::table('subgroup')->where('idgroup', $id)->get();
        $return = [];
        $return['subgroups'] = [];
        foreach ($data as $value) {
            $return['subgroups'][] = (new Subgroup([
                'id' => $value->idsubgroup,
                'name' => $value->name,
                'complement' => $value->complement,
                'group_id' => $value->idgroup
            ]));
        }
        return $return;
    }

    public static function get($id) {
        $data = DB::table('subgroup')
                ->where('idsubgroup', $id)
                ->first();
        if ($data) {
            return (new Subgroup([
                'id' => $data->idsubgroup,
                'name' => $data->name,
                'complement' => $data->complement,
                'group_id' => $data->idgroup
            ]));
        }
        return false;
    }

}
