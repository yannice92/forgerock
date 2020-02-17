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
use Illuminate\Support\Facades\Cache;

class SessionManagement extends ForgerockBase
{
    function __construct()
    {
        parent::__construct();
    }

    public function getJWK()
    {

        $queryString = [
            'client_id' => env('FR_CLIENTID')
        ];
        $data = $this->getRequest('/iam/v1/oauth2/realms/tsel/connect/jwk_uri', $queryString);
        $rs256 = Arr::first($data->keys, function ($value, $key) {
            return $value->alg === env('FR_ALG');
        });
        return (array)$rs256;
    }

    public function getPEM()
    {
        return Cache::remember('public_key_pem', 1000, function () {
            $jwkConverter = new JWKConverter();
            $publicKey = $jwkConverter->toPEM($this->getJWK());
            return $publicKey;
        });
    }

    public function validateToken($token)
    {
        $decoded = JWT::decode($token, $this->getPEM(), [env('FR_ALG')]);
//        var_dump($decoded->iss);
//        var_dump($decoded->aud);
//        var_dump($decoded);
//        die;
//        $eligibleIIS = [
//            env('FR_ISS', 'https://ciamampappbsd.ciam.telkomsel.com:10010/openam/oauth2/tsel/duniagames/web'),
//            env('FR_ISS_MOBILE', 'https://ciamampappbsd.ciam.telkomsel.com:10010/openam/oauth2/tsel/duniagames/mobile'),
//            env('FR_ISS_TBS', 'https://ciamampapptbs.ciam.telkomsel.com:10010/openam/oauth2/tsel/duniagames/web'),
//            env('FR_ISS_MOBILE_TBS', 'https://ciamampapptbs.ciam.telkomsel.com:10010/openam/oauth2/tsel/duniagames/mobile'),
//        ];
//        if (!in_array($decoded->iss, $eligibleIIS)) {
//            throw new \Exception('Not allowed IIS', 401, 1);
//        }
//
//        if (!in_array($decoded->aud, explode(',', env('FR_VALID_AUD')))) {
//            throw new \Exception('Not valid audience', 401, 2);
//        }

        return $decoded;
    }
}
