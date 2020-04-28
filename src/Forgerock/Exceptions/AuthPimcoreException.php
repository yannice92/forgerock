<?php
/**
 * Samuelerwardi samuelerwardi@gmail.com
 */

namespace App\Forgerock\Exceptions;


class AuthPimcoreException extends ForgeRockBaseException
{
    protected $code;
    protected $errorMessage;
    protected $httpCode;

    public function __construct(array $errorMessage = ['error'], int $httpCode = 401, string $code = "401")
    {
        $this->errorMessage = [
            "token" => $errorMessage
        ];
        $this->httpCode = $httpCode;
        $this->code = $code;
    }
}
