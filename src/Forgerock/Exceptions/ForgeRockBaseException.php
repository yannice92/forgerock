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

    public function __construct(array $errorMessage = ['error'], int $httpCode = 400, string $code = "AUTH99")
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
