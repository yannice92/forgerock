<?php
/**
 * Created by PhpStorm.
 * User: SALT
 * Date: 14/10/2019
 * Time: 11:03
 */

namespace App\Forgerock;

use App\Exceptions\FRExecption;
use App\Forgerock\Exceptions\ForgeRockExceptions;
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
        $eligibleIIS = explode(',', env('FR_ISS'));
        if (!in_array($decoded->iss, $eligibleIIS)) {
            throw new ForgeRockExceptions('Not allowed IIS', 401, 1);
        }

        if (!in_array($decoded->aud, explode(',', env('FR_VALID_AUD')))) {
            throw new ForgeRockExceptions('Not valid audience', 401, 2);
        }

        return $decoded;
    }
}
