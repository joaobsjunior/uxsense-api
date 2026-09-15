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
        if ($isIssetAdmin && $isNotEmptyAdmin && is_string($array['login']) && is_string($array['password'])) {
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
                $response = new Response([], 203);
            }
        }
        return $response;
    }

    public function postLostPassword(Request $request) {
        $data = [];
        if ($request->filled('login') && is_string($request->input('login'))) {
            $data = UserBO::lostPassword($request->input('login'));
        }
        return new Response($data);
    }

    public function postFirstAccess(Request $request) {
        $token = $request->input('token');
        $password = $request->input('password');
        $return = UserBO::firstAccess($token, $password);
        if ($return) {
            return new Response("", 200);
        } else {
            return new Response("", 400);
        }
    }

    public function logout(Request $request) {
        $administrator = $request->attributes->get('administrator');
        if ($administrator) {
            // Invalidate the API token server side.
            \Illuminate\Support\Facades\DB::table('administrator')
                ->where('idadministrator', $administrator->idadministrator)
                ->update(['token' => null]);
        }
        return new Response([]);
    }

    public function inviteUser(Request $request) {
        $params = $request->input();
        if (!isset($params['group_id']) || !isset($params['login']) || !is_string($params['login']) || trim($params['login']) === '') {
            return new Response(['message' => 'login and group_id are required'], 400);
        }
        $user = new User($params);
        $user = UserBO::invite($user, $params['group_id']);
        if ($user["created"]) {
            return new Response($user, 200);
        } else {
            return new Response($user, 400);
        }
    }

}
