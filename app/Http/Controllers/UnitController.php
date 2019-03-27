<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
use App\Models\Unit;
use App\Models\UnitBO;

class UnitController extends Controller {

    public function index(Request $request) {
        $response = new Response([], 200);
        $data = UnitBO::listAll();
        if ($data) {
            $size = count($data['units']);
            if ($size > 0) {
                for ($i = 0; $i < $size; $i++) {
                    $data['units'][$i] = $data['units'][$i]->getData();
                }
            }
            $response = new Response($data);
        }
        return $response;
    }

    public function show(Request $request, $id) {
        $response = new Response([], 400);
        if ($id) {
            $data = UnitBO::get($id);
            if ($data) {
                $response = new Response($data->getData());
            }
        }
        return $response;
    }

    public function store(Request $request) {
        $array = $request->input();
        $response = new Response([], 400);
        $isIsset = isset($array['name']) && isset($array['latitude']) && isset($array['longitude']);
        $isNotEmpty = !empty($array['name']) && !empty($array['latitude']) && !empty($array['longitude']);
        $unit = new Unit($array);
        if ($unit->getId()) {
            $unit_old = UnitBO::get($unit->getId());
            $diff = array_diff_assoc($unit->getData(), $unit_old->getData());
            foreach ($diff as $key => $value) {
                if (!$value) {
                    unset($diff[$key]);
                }
            }
            $unit = UnitBO::change($unit->getId(), $diff);
            if ($unit) {
                $response = new Response($unit->getData());
            }
        } else if ($isIsset && $isNotEmpty) {
            $unit = UnitBO::register($unit);
            if ($unit) {
                $response = new Response($unit->getData());
            }
        }
        return $response;
    }

    public function destroy(Request $request, $id) {
        $response = new Response([], 400);
        if ($id) {
            $data = UnitBO::delete($id);
            $data = ['deleted' => $data];
            $response = new Response($data);
        }
        return $response;
    }

}
