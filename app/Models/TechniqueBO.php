<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class TechniqueBO {

    public static function listAll() {
        $data = DB::table('capture_technique')
                ->orderBy('name', 'asc')
                ->get();
        $return = [];
        $return['techniques'] = [];
        foreach ($data as $value) {
            $return['techniques'][] = (new Technique([
                'id' => $value->idcapture_technique,
                'name' => $value->name,
                'description' => $value->description,
            ]));
        }
        return $return;
    }

    public static function get($id) {
        $data = DB::table('capture_technique')
                ->where('idcapture_technique', $id)
                ->first();
        if ($data) {
            return (new Technique([
                'id' => $data->idcapture_technique,
                'name' => $data->name,
                'description' => $data->description,
            ]));
        }
        return false;
    }

}
