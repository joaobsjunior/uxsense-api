<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class UnitBO {

    public static function register(Unit $unit) {
        $data = $unit->getDataDiff();
        unset($data['id']);
        $callback = DB::table('unit')->insertGetId($data);
        if ($callback) {
            $unit->setId($callback);
            return $unit;
        }
        return false;
    }

    public static function change($id, $diff) {
        $callback = DB::table('unit')->where('idunit', $id)->update($diff);
        if ($callback) {
            return UnitBO::get($id);
        }
        return false;
    }

    public static function delete($id) {
        $callback = DB::table('unit')->where('idunit', $id)->delete();
        $response = false;
        if ($callback) {
            $response = true;
        }
        return $response;
    }

    public static function listAll() {
        $data = DB::table('unit')
                ->orderBy('name', 'asc')
                ->get();
        $return = [];
        $return['units'] = [];
        
        
        foreach ($data as $value) {
            $return['units'][] = (new Unit([
                'id' => $value->idunit,
                'name' => $value->name,
                'latitude' => $value->latitude,
                'longitude' => $value->longitude
            ]));
        }
        return $return;
    }

    public static function get($id) {
        $data = DB::table('unit')->where('idunit', $id)->first();
        if ($data) {
            return (new Unit([
                'id' => $data->idunit,
                'name' => $data->name,
                'latitude' => $data->latitude,
                'longitude' => $data->longitude
            ]));
        }
        return false;
    }

}
