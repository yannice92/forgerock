<?php
/**
 * Samuelerwardi samuelerwardi@gmail.com
 */

/**
 * Samuelerwardi samuelerwardi@gmail.com
 */

namespace App\Forgerock\Exceptions;

class FRSignatureInvalidException extends ForgeRockBaseException
{
    protected $code;
    protected $errorMessage;
    protected $httpCode;

    public function __construct(array $errorMessage = ['error'], int $httpCode = 400, string $code = "400")
    {
        $this->errorMessage = $errorMessage;
        $this->httpCode = $httpCode;
        $this->code = $code;
    }
}
