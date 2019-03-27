<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class DeviceBO {

    public static function register(Device $device) {
        $data = $device->getDataDB();
        $id = DB::table('device')->insertGetId($data);
        if ($id) {
            $device = DeviceBO::get($id);
            return $device;
        }
        return false;
    }

    public static function change($id, $diff) {
        $callback = DB::table('device')->where('iddevice', $id)->update($diff);
        if ($callback) {
            return DeviceBO::get($id);
        }
        return false;
    }

    public static function delete($id) {
        $callback = DB::table('device')->where('iddevice', $id)->delete();
        $response = false;
        if ($callback) {
            $response = true;
        }
        return $response;
    }
    
    public static function deleteAllByClientIDAndPlatform($id, $platform) {
        $callback = DB::table('device')
                ->where('idclient', $id)
                ->where('platform', $platform)
                ->delete();
        $response = false;
        if ($callback) {
            $response = true;
        }
        return $response;
    }

    public static function listAll() {
        $data = DB::table('device')
                ->get();
        $return = [];
        $return['devices'] = [];

        foreach ($data as $value) {
            $return['devices'][] = new Device([
                'id' => $value->iddevice,
                'client_id' => $value->idclient,
                'registrator_id' => $value->registratorid,
                'platform' => $value->platform,
                'uuid' => $value->uuid,
                'token' => $value->token
            ]);
        }
        return $return;
    }
    
    public static function get($id) {
        $data = DB::table('device')
                ->where('iddevice', $id)
                ->first();
        if ($data) {
            return (new Device([
                'id' => $data->iddevice,
                'client_id' => $data->idclient,
                'registrator_id' => $data->registratorid,
                'platform' => $data->platform,
                'uuid' => $data->uuid,
                'token' => $data->token
            ]));
        }
        return false;
    }
    
    public static function getByClientId($idclient) {
        $data = DB::table('device')
                ->where('idclient', $idclient)
                ->get();
        $return = [];
        $return['devices'] = [];


        foreach ($data as $value) {
            $return['devices'][] = new Device([
                'id' => $value->iddevice,
                'client_id' => $value->idclient,
                'registrator_id' => $value->registratorid,
                'platform' => $value->platform,
                'uuid' => $value->uuid,
                'token' => $value->token
            ]);
        }
        return $return;
    }
    
    public static function getByUuid($uuid) {
        $data = DB::table('device')
                ->where('uuid', $uuid)
                ->first();
        if ($data) {
            return (new Device([
                'id' => $data->iddevice,
                'client_id' => $data->idclient,
                'registrator_id' => $data->registratorid,
                'platform' => $data->platform,
                'uuid' => $data->uuid,
                'token' => $data->token
            ]));
        }
        return false;
    }

    public static function sendNotification($registration_ids = [], $message = []) {
        $authToken = 'AIzaSyBwmC1IwyGgAyQgxJGsSKrdKUDWilkFh4w';
        $postData = array(
            'notification' => array(
                'title' => $message['title'],
                'text' => $message['text']
            ),
            'data' => array(
                'id' => $message['id']
            ),
            'registration_ids' => $registration_ids
        );
        $ch = curl_init('https://fcm.googleapis.com/fcm/send');
        curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                'Authorization: key=' . $authToken,
                'Content-Type: application/json'
            ),
            CURLOPT_POSTFIELDS => json_encode($postData)
        ));
        $response = curl_exec($ch);
        if ($response === FALSE) {
            die(curl_error($ch));
        }
        $responseData = json_decode($response, TRUE);
        echo $responseData;
    }

}
