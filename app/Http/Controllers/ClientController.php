<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Client;
use App\Models\ClientBO;
use App\Models\Device;
use App\Models\DeviceBO;
use App\Models\Mail;

class ClientController extends Controller {

    public function postIndex(Request $request) {
        $array = $request->input();
        $response = new Response([], 400);
        $client = new Client($array);
        if (isset($GLOBALS["device"])) {
            $client->setId($GLOBALS["device"]->idclient);
            $client_old = ClientBO::get($client->getId());
            if ($client_old->getPassword() === $client->getPassword() && isset($array['newpassword']) && $array['newpassword'] != "") {
                $client->setPassword($array['newpassword']);
                $diff = array_diff_assoc($client->getDataDiff(true), $client_old->getDataDiff(true));
            } else {
                $diff = array_diff_assoc($client->getDataDiff(), $client_old->getDataDiff());
            }
            foreach ($diff as $key => $value) {
                if ($value == "") {
                    unset($diff[$key]);
                }
            }
            $new_client = ClientBO::change($client->getId(), $diff);
            if ($new_client) {
                $response = new Response($client->getData());
            }
        }
        return $response;
    }

    public function signup(Request $request) {
        $array = $request->input();
        $response = new Response([], 400);
        $isIsset = isset($array['name']) && isset($array['email']) && isset($array['register']) && isset($array['password']);
        $isNotEmpty = "" !== ($array['name']) && "" !== ($array['email']) && "" !== ($array['register']) && "" !== ($array['password']);
        $client = new Client($array);
        $device = new Device($array);
        if ($isIsset && $isNotEmpty) {
            $client = ClientBO::register($client, $device);
            if ($client) {
                $response = new Response($client->getData());
            }
        }
        return $response;
    }

    public function lostPassword(Request $request) {
        $array = $request->input();
        $response = new Response([], 400);
        $client = new Client($array);
        if ($client->getEmail()) {
            $data = ClientBO::lostPassword($client->getEmail());
            if ($data) {
                $email = new Mail();
                $return = $email->sendLostPassword($data->getEmail(), $data->getPassword(), $data->getName(), "Solicitação de recuperação de senha");
                $response = new Response($return);
            }
        }
        return $response;
    }

    public function getIndex(Request $request) {
        $array = $request->input();
        $response = new Response([], 400);
        $client = ClientBO::get($request->header('user_id'));
        if ($client) {
            $response = new Response($client->getData());
        }
        return $response;
    }

    public function login(Request $request) {
        $response = new Response([], 400);
        $array = $request->input();
        $client = new Client($array);
        $device = new Device($array);
        if ($client->getPassword() && $client->getRegister() && $device->getUuid()) {
            $data = ClientBO::authenticate($client, $device);
            if ($data) {
                $response = new Response($data);
            } else {
                $response = new Response([], 203);
            }
        }
        return $response;
    }

    public function updateRegistration(Request $request) {
        $response = new Response([], 400);
        $device_id = $request->header('GSX-DEVICE');
        $array = $request->input();
        $device_old = DeviceBO::get($device_id);
        $device = clone $device_old;
        if ($device) {
            $device->setRegistratorId($array['registration_id']);
            $diff = array_diff_assoc($device->getDataDB(), $device_old->getDataDB());
            if (count($diff) != 0) {
                $device = DeviceBO::change($device->getId(), $diff);
            }
            if ($device) {
                $response = new Response($device->getData(), 200);
            }
        }
        return $response;
    }

    public function logout(Request $request) {
        $response = new Response([], 400);
        $device = DeviceBO::get($request->header('GSX-DEVICE'));
        $device_old = clone $device;
        if ($device) {
            $device->setToken(null);
            $device->setRegistratorId(null);
            $diff = array_diff_assoc($device->getDataDB(), $device_old->getDataDB());
            if (count($diff) > 0) {
                $device = DeviceBO::change($device->getId(), $diff);
            }
            if ($device) {
                $response = new Response($device->getData(), 200);
            }
        }
        return $response;
    }

}
