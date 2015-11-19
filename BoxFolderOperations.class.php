<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BoxFolderOperations
 *
 * @author richard
 */
class BoxFolderOperations {

    public static function getOptions() {
        return parse_ini_file('options.ini');
    }

    public static function doOauth2() {
        $o = self::getOptions();

//        shell_exec("gnome-www-browser https://app.box.com/api/oauth2/authorize?response_type=code\&client_id={$o['client_id']}\&state=security_token%3DKnhMJatFipTAnM0nHlZA");
//        fwrite(STDOUT, "Did you get the code?");
//        $code = trim(fgets(STDIN));

        $code = "12345";
        $result = file_get_contents('https://app.box.com/api/oauth2/token', FALSE, stream_context_create([
            'http' => [
                'header' => 'GET',
                'content' => http_build_query([
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'client_id' => $o['client_id'],
                    'client_secret' => $o['client_secret'],
                ])
            ]
        ]));
        
        
        print_r(http_build_query([
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'client_id' => $o['client_id'],
                    'client_secret' => $o['client_secret'],
                ]));

        // if fail log, it 
        if (!$result) {
            error_log("__CLASS__: Error getting authtoken");
        }

        // still return array pass or fail.
        return json_decode($result);
    }

    public static function refreshToken() {
        $o = self::getOptions();
    }

}
