<?php

class BaseController {

    public static function get_user_logged_in() {
        if (isset($_SESSION['user'])) {
            $adminid = $_SESSION['user'];
            $admin = Admin::find($adminid);
            return $admin;
        }
        return null;
    }

    public static function checkLogged() {
        if (!isset($_SESSION['user'])) {
            View::make('failed.html');
        }
    }

    public static function checkBanned() {
        if (self::isBanned()) {
            View::make('banned.html');
            exit();
        }
    }

    public static function isBanned() {
        $banned = Banned::all();

        $ip_sources = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
        foreach ($ip_sources as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (self::checkBan($banned, $ip)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    private static function checkBan($banned, $ip) {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return true;
        }
        foreach ($banned as $ban) {
            if ($ip === $ban->ip) {
                return true;
            }
        }
    }

}
