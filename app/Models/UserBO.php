<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserBO
{
    /**
     * Number of days an invitation / password recovery token stays valid.
     */
    public const TOKEN_TTL_DAYS = 3;

    public static function authenticate(User $user)
    {
        $_user = DB::table('administrator')->where('login', $user->getLogin())->first();
        if ($_user && Helpers::verifyPassword($user->getPlainPassword(), $_user->password)) {
            $user->setId($_user->idadministrator);
            $user->setName($_user->name);
            $user->setToken(Helpers::token(32));
            $update = ['token' => $user->getToken()];
            if (Helpers::passwordNeedsRehash($_user->password)) {
                // Transparently upgrade legacy sha1(md5()) hashes to bcrypt.
                $update['password'] = Helpers::hashPassword($user->getPlainPassword());
            }
            $data = DB::table('administrator')
                ->where('idadministrator', $user->getId())
                ->update($update);
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
            $token = self::issuePasswordToken($user->getId());
            DB::table('administrator_group')->insert([
                'idadministrator' => $user->getId(),
                'idgroup' => $group_id,
            ]);
            $email = new Mail();
            return $email->sendCodeChangePassword($user->getLogin(), $token, $user->getName(), "Convite para UXSense");
        }
        return Mail::returnData(false, false, "userNotCreated", true);
    }

    public static function lostPassword($login)
    {
        $user = DB::table('administrator')->where('login', $login)->first();
        if ($user) {
            $token = self::issuePasswordToken($user->idadministrator);
            $email = new Mail();
            return $email->sendCodeChangePassword($user->login, $token, $user->name, "Recuperação de Senha");
        }
        return Mail::returnData(false, false, "userNotExist", true);
    }

    public static function firstAccess($token, $password)
    {
        if (!is_string($token) || $token === '' || !is_string($password) || $password === '') {
            return false;
        }
        $data = DB::table('manager_password')->where('token', $token)->first();
        if ($data) {
            if (self::tokenExpired($data)) {
                DB::table('manager_password')->where('token', $token)->delete();
                return false;
            }
            $user = new User();
            $user->setPassword($password);
            $return = DB::table('administrator')
                ->where('idadministrator', $data->idadministrator)
                ->update(['password' => $user->getPassword()]);
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

    /**
     * Create a fresh single-use token for the administrator, replacing any
     * previous one (the old code built this DELETE by string concatenation
     * and never executed it).
     */
    private static function issuePasswordToken($idadministrator)
    {
        $token = Helpers::token(40);
        DB::table('manager_password')->where('idadministrator', $idadministrator)->delete();
        DB::table('manager_password')->insert([
            'token' => $token,
            'idadministrator' => $idadministrator,
        ]);
        return $token;
    }

    private static function tokenExpired($row)
    {
        if (!isset($row->date) || $row->date === null || $row->date === '') {
            return false;
        }
        try {
            return Carbon::parse($row->date)->lt(Carbon::now()->subDays(self::TOKEN_TTL_DAYS));
        } catch (\Throwable $e) {
            return false;
        }
    }

}
