<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class UserBO
{

    public static function authenticate(User $user)
    {
        $_user = DB::table('administrator')->where([
            ['login', $user->getLogin()],
            ['password', $user->getPassword()],
        ])->first();
        if ($_user) {
            $user->setId($_user->idadministrator);
            $user->setName($_user->name);
            $token = md5(uniqid(rand(), true));
            $user->setToken($token);
            $data = DB::table('administrator')
                ->where('idadministrator', $user->getId())
                ->update(['token' => $user->getToken()]);
            if ($data) {
                return $user;
            }
        }
        return false;
    }

    public static function invite(User $user, $group_id)
    {
        $check_info = DB::table('administrator_group')
            ->where('idgroup', $group_id)
            ->first();
        if ($check_info) {
            return Mail::returnData(false, false, "usedGroup", true);
        }

        $check_info = DB::table('administrator')
            ->where('login', $user->getLogin())
            ->first();
        if ($check_info) {
            return Mail::returnData(false, false, "existingAdministrator", true);
        }
        $data = $user->getDataDB();
        $callback = DB::table('administrator')->insertGetId($data);
        if ($callback) {
            $user->setId($callback);
            $token = sha1(md5(uniqid($user->getLogin())));
            DB::raw('delete from `manager_password` where `date` <= TIMESTAMP(DATE_SUB(NOW(), INTERVAL 3 DAY)) or `idadministrator` = ' . $user->getId());
            DB::table('administrator_group')->insert([
                'idadministrator' => $user->getId(),
                'idgroup' => $group_id,
            ]);
            DB::table('manager_password')->insert([
                'token' => $token,
                'idadministrator' => $user->getId(),
            ]);
            $email = new Mail();
            return $email->sendCodeChangePassword($user->getLogin(), $token, $user->getName(), "Convite para UXSense");
        }
    }

    public static function lostPassword($login)
    {
        $user = DB::table('administrator')->where('login', $login)->first();
        if ($user) {
            $token = sha1(md5(uniqid($user->login)));
            DB::raw('delete from `manager_password` where `date` <= TIMESTAMP(DATE_SUB(NOW(), INTERVAL 3 DAY)) or `idadministrator` = ' . $user->idadministrator);
            DB::table('manager_password')->insert([
                'token' => $token,
                'idadministrator' => $user->idadministrator,
            ]);
            $email = new Mail();
            return $email->sendCodeChangePassword($user->login, $token, $user->name, "Recuperação de Senha");
        }
        return Mail::returnData(false, false, "userNotExist", true);
    }

    public static function firstAccess($token, $password)
    {
        $data = DB::table('manager_password')->where('token', $token)->first();
        if ($data) {
            $user = new User();
            $user->setPassword($password);
            $return = DB::table('administrator')->where('idadministrator', $data->idadministrator)->update(['password' => $user->getPassword()]);
            if ($return) {
                DB::table('manager_password')
                    ->where('idadministrator', $data->idadministrator)
                    ->delete();
                return true;
            }
        }
        return false;
    }

    public static function change($id, $diff)
    {
        $callback = DB::table('administrator')->where('idadministrator', $id)->update($diff);
        if ($callback) {
            return UserBO::get($id);
        }
        return false;
    }

    public static function get($id)
    {
        $data = DB::table('administrator')
            ->where('idadministrator', $id)
            ->orderBy('name', 'desc')
            ->first();
        if ($data) {
            return (new User([
                'id' => $data->idadministrator,
                'name' => $data->name,
                'login' => $data->login,
                'password' => $data->password,
            ], true));
        }
        return false;
    }

}
