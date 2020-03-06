<?php
/**
 * Samuelerwardi samuelerwardi@gmail.com
 */

/**
 * Samuelerwardi samuelerwardi@gmail.com
 */

namespace App\Forgerock\Exceptions;

class ForgeRockExceptions extends ForgeRockBaseException
{
    protected $code;
    protected $message;
    protected $httpCode;

    public function __construct(string $message = 'error', int $httpCode = 400, int $code = 400)
    {
        $this->message = $message;
        $this->httpCode = $httpCode;
        $this->code = $code;
    }
}
