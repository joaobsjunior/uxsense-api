<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\GroupBO;
use App\Models\Group;
use App\Models\SubgroupBO;

class GroupController extends Controller {

    public function index() {
        $response = new Response([], 200);
        $data = GroupBO::listAll();
        if ($data) {
            $size = count($data['groups']);
            for ($i = 0; $i < $size; $i++) {
                $data['groups'][$i] = $data['groups'][$i]->getData();
            }
            $response = new Response($data);
        }
        return $response;
    }

    public function store(Request $request) {
        $array = $request->input();
        $response = new Response([], 400);
        $isIsset = isset($array['name']);
        $isNotEmpty = !empty($array['name']);
        $group = new Group($array);
        if ($group->getId()) {
            $group_old = GroupBO::get($group->getId());
            $diff = array_diff_assoc($group->getData(), $group_old->getData());
            foreach ($diff as $key => $value) {
                if (!$value) {
                    unset($diff[$key]);
                }
            }
            $group = GroupBO::change($group->getId(), $diff);
            if ($group) {
                $response = new Response($group->getData());
            }
        } else if ($isIsset && $isNotEmpty) {
            $group = GroupBO::register($group);
            if ($group) {
                $response = new Response($group->getData());
            }
        }
        return $response;
    }

    public function show(Request $request, $id) {
        $response = new Response([], 400);
        $data = GroupBO::get($id);
        if ($data) {
            $response = new Response($data->getData());
        }
        return $response;
    }

    public function destroy(Request $request, $id) {
        $response = new Response([], 400);
        if ($id){
            $subgroups = SubgroupBO::listAllofGroup($id);
            if(count($subgroups['subgroups']) > 0){
                $response = new Response([],412);
            }else{
                if(GroupBO::delete($id)){
                    $response = new Response([],200);
                }
            }
        }
        return $response;
    }

}
