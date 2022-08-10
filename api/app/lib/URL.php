<?php

namespace App\Lib;

class URL
{
    public static function getURL()
    {
        global $_SERVER;

        $document_root = strlen($_SERVER["DOCUMENT_ROOT"]) - 1;
        $exclude_url = substr($_SERVER["SCRIPT_FILENAME"], $document_root, -10);

        $request_uri = $_SERVER['REQUEST_URI'];
        $request_uri = substr($request_uri, strlen($exclude_url));

        $request_uri_exp = explode("?", $request_uri);
        $request_uri = $request_uri_exp[0];

        $url_exp = explode("/", $request_uri);
        $url_arr = array();

        for ($a = 0; $a <= count($url_exp); $a++) {
            if (isset($url_exp[$a]) && $url_exp[$a] != "") {
                array_push($url_arr, $url_exp[$a]);
            }
        }

        $url = '/' . implode('/', $url_arr);

        return $url;
    }
}