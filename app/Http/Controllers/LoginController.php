<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use App\Models\UserBO;
use App\Models\GroupBO;

class LoginController extends Controller {

    public function postIndex(Request $request) {
        $array = $request->input();
        $response = new Response([], 400);
        $isIssetAdmin = isset($array['login']) && isset($array['password']);
        $isNotEmptyAdmin = !empty($array['login']) && !empty($array['password']);
        if ($isIssetAdmin && $isNotEmptyAdmin) {
            $user = new User($array);
            $data = UserBO::authenticate($user);
            if ($data) {
                $return = $data->getData();
                $group_id = GroupBO::getObjectByUser($data->getId());
                if ($group_id) {
                    $return['group'] = $group_id->idgroup;
                }
                $response = new Response($return);
            } else {
                $response = new Response($response, 203);
            }
        }
        return $response;
    }

    public function postLostPassword(Request $request) {
        if($request->filled('login')){
            $data = UserBO::lostPassword($request['login']);
        }
        return new Response($data);
    }

    public function postFirstAccess(Request $request) {
        $data = $request->input();
        $token = $data['token'];
        $password = $data['password'];
        $return = UserBO::firstAccess($token, $password);
        if ($return) {
            return new Response("", 200);
        } else {
            return new Response("", 400);
        }
    }

    public function logout(Request $request) {
        return new Response([]);
    }

    public function inviteUser(Request $request) {
        $params = $request->input();
        $user = new User($params);
        $user = UserBO::invite($user, $params['group_id']);
        if ($user["created"]) {
            return new Response($user, 200);
        } else {
            return new Response($user, 400);
        }
    }

}
