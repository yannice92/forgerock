<?php
/**
 * Samuelerwardi samuelerwardi@gmail.com
 */

namespace App\Forgerock\Exceptions;


class ForgeRockBaseException extends \Exception
{
    protected $message;

    protected $httpCode;

    protected $error_code;

    public function __construct(string $message = 'error', int $httpCode = 400, int $code = 1)
    {
        $this->message = $message;
        $this->httpCode = $httpCode;
        $this->code = $code;
    }

    public function jsonErrorResponse()
    {
        return response()->json([
            'status' => false,
            'code' => $this->code,
            'message' => $this->message,
            'error_message' => $this->message,
            'data' => null
        ], $this->httpCode);
    }
}
