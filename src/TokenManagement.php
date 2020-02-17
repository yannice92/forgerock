<?php
/**
 * Created by PhpStorm.
 * User: SALT
 * Date: 14/10/2019
 * Time: 11:03
 */

namespace App\Forgerock;

use App\Exceptions\FRExecption;
use Illuminate\Support\Arr;
use CoderCat\JWKToPEM\JWKConverter;
use Firebase\JWT\JWT;

class TokenManagement extends ForgerockBase
{
    function __construct()
    {
        parent::__construct();
    }

    public function getAccessToken($code, $isMobile = true)
    {
        $queryString = [
            "grant_type" => 'authorization_code',
            "redirect_uri" => env('FR_REDIRECT_URI'),
            "code" => $code,
            "client_id" => $isMobile ? env('FR_CLIENTID_MOBILE') : env('FR_CLIENTID'),
            "client_secret" => env('FR_SECRETKEY')
        ];
        $headers = ['Content-Type' => 'application/x-www-form-urlencoded'];
        $result = $this->postRequest('/iam/v1/oauth2/realms/tsel/access_token', $queryString, [], $headers);
        return $result;
    }

    public function refreshAccessToken($accessToken, $isMobile = true)
    {
        $queryString = [
            "grant_type" => 'refresh_token',
            "refresh_token" => $accessToken,
            "client_id" => $isMobile ? env('FR_CLIENTID_MOBILE') : env('FR_CLIENTID'),
            "client_secret" => env('FR_SECRETKEY')
        ];
        $headers = ['Content-Type' => 'application/x-www-form-urlencoded'];
        $result = $this->postRequest('/iam/v1/oauth2/realms/tsel/access_token', $queryString, [], $headers);
        return $result;
    }
}
