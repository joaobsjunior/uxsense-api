<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Team;
use App\Models\TeamBO;

class TeamController extends Controller {

    public function index() {
        $response = new Response([], 200);
        $data = TeamBO::listAll();
        if ($data) {
            $size = count($data['teams']);
            for ($i = 0; $i < $size; $i++) {
                $data['teams'][$i] = $data['teams'][$i]->getData();
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
        $team = new Team($array);
        if ($team->getId()) {
            $team_old = TeamBO::get($team->getId());
            $diff = array_diff_assoc($team->getDataDiff(), $team_old->getDataDiff());
            foreach ($diff as $key => $value) {
                if (!$value) {
                    unset($diff[$key]);
                }
            }
            $team = TeamBO::change($team->getId(), $diff);
            if ($team) {
                $response = new Response($team->getData());
            }
        } else if ($isIsset && $isNotEmpty) {
            $team = TeamBO::register($team);
            if ($team) {
                $response = new Response($team->getData());
            }
        }
        return $response;
    }

    public function show(Request $request, $id) {
        $response = new Response([], 400);
        $params = $request->input();
        if (isset($params['team'])) {
            $data = TeamBO::get($id);
            $data = $data->getData();
            $response = new Response($data, 200);
        } else if (isset($params['subgroup'])) {
            $data = TeamBO::listAllofSubgroup($id);
            $size = count($data['teams']);
            if ($size > 0) {
                for ($i = 0; $i < $size; $i++) {
                    $data['teams'][$i] = $data['teams'][$i]->getData();
                }
            }
            $response = new Response($data, 200);
        } else if (isset($params['client'])) {
            $data = TeamBO::listAllofClient($id);
            $size = count($data['teams']);
            if ($size > 0) {
                for ($i = 0; $i < $size; $i++) {
                    $data['teams'][$i] = $data['teams'][$i]->getData();
                }
            }
            $response = new Response($data, 200);
        }

        return $response;
    }

    public function destroy(Request $request, $id) {
        $response = new Response([], 400);
        if ($id) {
            if (TeamBO::delete($id)) {
                $response = new Response([], 200);
            }
        }
        return $response;
    }

    public function regiterForClient(Request $request) {
        $response = new Response([], 400);
        $data = $request->input();
        if (isset($data['client']) && isset($data['team'])) {
            $return = TeamBO::registerTeamClient($data['team'], $data['client']);
            if($return){
                $response = new Response([], 200);
            }
        }
        return $response;
    }
    
    public function destroyForClient(Request $request) {
        $response = new Response([], 400);
        $data = $request->input();
        if (isset($data['client']) && isset($data['team'])) {
            $return = TeamBO::deleteTeamClient($data['team'], $data['client']);
            if($return){
                $response = new Response([], 200);
            }
        }
        return $response;
    }

}
