<?php
/**
 * Samuelerwardi samuelerwardi@gmail.com
 */

namespace App\Forgerock\Exceptions;


class ForgeRockBaseException extends \Exception
{
    protected $errorMessage;

    protected $httpCode;

    protected $error_code;

    public function __construct(string $errorMessage = 'error', int $httpCode = 400, string $code = "0")
    {
        $this->errorMessage = $errorMessage;
        $this->httpCode = $httpCode;
        $this->code = $code;
    }

    public function jsonErrorResponse()
    {
        return response()->json([
            'status' => false,
            'code' => $this->code,
            'message' => null,
            'errorMessage' => $this->errorMessage,
            'data' => null
        ], $this->httpCode);
    }
}
