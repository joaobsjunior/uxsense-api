<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class ClientBO {

    public static function authenticate(Client $client, Device $device) {
        $plain = $client->getPlainPassword();
        $_user = DB::table('client')->where('register', $client->getRegister())->first();
        if (!$_user || $plain === null || $plain === '') {
            return false;
        }

        $authenticated = false;
        $update = [];
        if (Helpers::verifyPassword($plain, $_user->password)) {
            $authenticated = true;
            if (Helpers::passwordNeedsRehash($_user->password)) {
                // Transparently upgrade legacy sha1(md5()) hashes to bcrypt.
                $update['password'] = Helpers::hashPassword($plain);
            }
        } elseif (isset($_user->passwordrecovery) && Helpers::verifyPassword($plain, $_user->passwordrecovery)) {
            // Temporary password sent by e-mail: it becomes the new password.
            $authenticated = true;
            $update['password'] = Helpers::hashPassword($plain);
            $update['passwordrecovery'] = null;
        }
        if (!$authenticated) {
            return false;
        }
        if (count($update) > 0) {
            DB::table('client')->where('idclient', $_user->idclient)->update($update);
            $_user = DB::table('client')->where('idclient', $_user->idclient)->first();
        }

        $client->setId($_user->idclient);
        $client->setName($_user->name);
        $client->setEmail($_user->email);
        $client->setRegister($_user->register);
        $client->setSex($_user->sex);
        $client->setDatebirth($_user->datebirth);
        $client->setPassword($_user->password, true);
        $device->setClientId($_user->idclient);
        $device->setToken(Helpers::token(32));
        $_device = DeviceBO::getByUuid($device->getUuid());
        if ($_device) {
            $diff = array_diff_assoc($device->getDataDB(), $_device->getDataDB());
            foreach ($diff as $key => $value) {
                if (!$value) {
                    unset($diff[$key]);
                }
            }
            if (count($diff) > 0) {
                $device = DeviceBO::change($_device->getId(), $diff);
            }
            $group = GroupBO::getByClientID($client->getId());
            if ($group) {
                $group = $group->getData();
            }
            return array(
                "client" => $client->getData(),
                "device" => $device->getData(),
                "group" => $group,
            );
        } else {
            $group = GroupBO::getByClientID($client->getId());
            if ($group) {
                $group = $group->getData();
            }
            if ($device->getPlatform() == 'iOS') {
                DeviceBO::deleteAllByClientIDAndPlatform($device->getClientId(), 'iOS');
            }
            $device = DeviceBO::register($device);
            if ($device) {
                return array(
                    "client" => $client->getData(),
                    "device" => $device->getData(),
                    "group" => $group,
                );
            }
        }
        return false;
    }

    public static function register(Client $client, Device $device) {
        $data = $client->getDataDiff();
        $data['password'] = $client->getPassword();
        $id = DB::table('client')->insertGetId($data);
        if ($id) {
            $client = ClientBO::get($id);
            return $client;
        }
        return false;
    }

    public static function change($id, $diff) {
        $callback = DB::table('client')->where('idclient', $id)->update($diff);
        if ($callback) {
            return ClientBO::get($id);
        }
        return false;
    }

    public static function delete($id) {
        $callback = DB::table('client')->where('idclient', $id)->delete();
        $response = false;
        if ($callback) {
            $response = true;
        }
        return $response;
    }

    public static function lostPassword($email) {
        $tempPassword = Helpers::rand_passwd();
        $data = DB::table('client')->where('email', $email)->first();
        $response = false;
        if ($data) {
            $callback = DB::table('client')
                    ->where('idclient', $data->idclient)
                    ->update(["passwordrecovery" => Helpers::hashPassword($tempPassword)]);
            if ($callback) {
                $response = new Client([
                    'id' => $data->idclient,
                    'name' => $data->name,
                    'email' => $data->email,
                    'register' => $data->register,
                    'sex' => $data->sex,
                    'datebirth' => $data->datebirth,
                        ], true);
                // The plain temporary password is only kept in memory so it can be e-mailed.
                $response->setPassword($tempPassword, true);
            }
        }
        return $response;
    }

    public static function listAll() {
        $data = DB::table('client')
                ->orderBy('name', 'asc')
                ->get();
        $return = [];
        $return['clients'] = [];

        foreach ($data as $value) {
            $return['clients'][] = new Client([
                'id' => $value->idclient,
                'name' => $value->name,
                'email' => $value->email,
                'register' => $value->register,
                'sex' => $value->sex,
                'password' => $value->password,
                'datebirth' => $value->datebirth,
                    ], true);
        }
        return $return;
    }

    public static function get($id) {
        $data = DB::table('client')
                ->where('idclient', $id)
                ->orderBy('name', 'desc')
                ->first();
        if ($data) {
            return (new Client([
                'id' => $data->idclient,
                'name' => $data->name,
                'email' => $data->email,
                'register' => $data->register,
                'sex' => $data->sex,
                'password' => $data->password,
                'datebirth' => $data->datebirth,
                    ], true));
        }
        return false;
    }

}
