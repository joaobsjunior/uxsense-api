<?php

namespace App\Models;

use Illuminate\Support\Facades\Hash;

class Helpers {

    public function renamePrefixKeyArray($inputArray, $prefix) {
        $keys = array_keys($inputArray);
        array_walk($keys, function (&$value, $omit, $prefix) {
            $value = str_replace($prefix, '', $value);
        }, $prefix);
        $newArray = array_combine($keys, $inputArray);
        return $newArray;
    }

    public function ajustArrayPaginate($array) {
        $prefix = "";
        $newArray = $this->renamePrefixKeyArray($array, $prefix);
        unset($newArray["items"]);
        unset($newArray["path"]);
        unset($newArray["pageName"]);
        unset($newArray["query"]);
        unset($newArray["fragment"]);
        return $newArray;
    }

    public static function isNullOrEmpty($value){
        if($value != null && isset($value) && !empty($value)){
            return false;
        }
        return true;
    }

    /**
     * Generate a temporary password using a cryptographically secure source.
     */
    public static function rand_passwd($length = 10) {
        $chars = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $max = strlen($chars) - 1;
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, $max)];
        }
        return $password;
    }

    /**
     * Generate an opaque, unpredictable API token.
     */
    public static function token($length = 32) {
        return bin2hex(random_bytes(intdiv($length, 2)));
    }

    /**
     * Hash a password for storage (bcrypt through the framework hasher).
     */
    public static function hashPassword($plain) {
        return Hash::make((string) $plain);
    }

    /**
     * Whether a stored hash was produced by the legacy sha1(md5()) scheme.
     */
    public static function isLegacyHash($stored) {
        return is_string($stored) && preg_match('/^[a-f0-9]{40}$/i', $stored) === 1;
    }

    /**
     * Verify a plain text password against a stored hash.
     *
     * Legacy unsalted sha1(md5()) hashes are still accepted so existing
     * accounts keep working; callers should re-hash them with
     * hashPassword() after a successful verification.
     */
    public static function verifyPassword($plain, $stored) {
        if ($plain === null || $plain === '' || $stored === null || $stored === '') {
            return false;
        }
        if (self::isLegacyHash($stored)) {
            return hash_equals(strtolower($stored), sha1(md5((string) $plain)));
        }
        try {
            return Hash::check((string) $plain, (string) $stored);
        } catch (\RuntimeException $e) {
            return false;
        }
    }

    /**
     * Whether a stored hash should be upgraded to the current algorithm.
     */
    public static function passwordNeedsRehash($stored) {
        return self::isLegacyHash($stored) || Hash::needsRehash((string) $stored);
    }

}
