<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\SubgroupBO;
use App\Models\Subgroup;

class SubgroupController extends Controller {

    public function index() {
        $response = new Response([], 200);
        $data = SubgroupBO::listAll();
        if ($data) {
            $size = count($data['subgroups']);
            if ($size > 0) {
                for ($i = 0; $i < $size; $i++) {
                    $data['subgroups'][$i] = $data['subgroups'][$i]->getData();
                }
            }
            $response = new Response($data);
        }
        return $response;
    }

    public function store(Request $request) {
        $array = $request->input();
        $response = new Response([], 400);
        $isIsset = isset($array['name']) && isset($array['group_id']);
        $isNotEmpty = !empty($array['name']) && !empty($array['group_id']);
        $subgroup = new Subgroup($array);
        if ($subgroup->getId()) {
            $subgroup_old = SubgroupBO::get($subgroup->getId());
            $diff = array_diff_assoc($subgroup->getDataDiff(), $subgroup_old->getDataDiff());
            foreach ($diff as $key => $value) {
                if (!$value) {
                    unset($diff[$key]);
                }
            }
            $subgroup = SubgroupBO::change($subgroup->getId(), $diff);
            if ($subgroup) {
                $response = new Response($subgroup->getData());
            }
        } else if ($isIsset && $isNotEmpty) {
            $subgroup = SubgroupBO::register($subgroup);
            if ($subgroup) {
                $response = new Response($subgroup->getData());
            }
        }
        return $response;
    }

    public function show(Request $request, $id) {
        $response = new Response([], 200);
        $params = $request->input();
        if (@$params['subgroup'] == true) {
            $data = SubgroupBO::get($id);
            $response = new Response($data->getData(), 200);
        } else {
            $data = SubgroupBO::listAllofGroup($id);
            $size = count($data['subgroups']);
            if ($size > 0) {
                for ($i = 0; $i < $size; $i++) {
                    $data['subgroups'][$i] = $data['subgroups'][$i]->getData();
                }
            }
            $response = new Response($data, 200);
        }

        return $response;
    }

    public function destroy(Request $request, $id) {
        $response = new Response([], 412);
        $data = SubgroupBO::delete($id);
        if ($data) {
            $response = new Response([], 200);
        }
        return $response;
    }

}
