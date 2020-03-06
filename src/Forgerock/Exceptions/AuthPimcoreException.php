<?php
/**
 * Samuelerwardi samuelerwardi@gmail.com
 */

namespace App\Forgerock\Exceptions;


class AuthPimcoreException extends ForgeRockBaseException
{
    protected $code;
    protected $message;
    protected $httpCode;

    public function __construct(string $message = 'error', int $httpCode = 401, int $code = 401)
    {
        $this->message = $message;
        $this->httpCode = $httpCode;
        $this->code = $code;
    }
}
