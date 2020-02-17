<?php
/**
 * Created by PhpStorm.
 * User: SALT
 * Date: 14/10/2019
 * Time: 11:03
 */

namespace App\Forgerock;


use function foo\func;
use Illuminate\Support\Arr;

class OTPManagement extends ForgerockBase
{
    private $queryString;

    function __construct()
    {
        $this->queryString = [
            'authIndexType' => 'service',
            'authIndexValue' => 'phoneLogin'
        ];
        parent::__construct();
    }

    public function sendOTP($phoneNumber, $isMobile)
    {
        $headers = [
            'AM-PHONENUMBER' => $phoneNumber,
            'AM-SEND' => 'otp',
            'AM-CLIENTID' => $isMobile ? env('FR_CLIENTID_MOBILE') : env('FR_CLIENTID'),
            'Content-Type' => 'application/json'
        ];
        return $this->postRequest('/iam/v1/realms/tsel/authenticate', $this->queryString, [], $headers);
    }

    public function resendOTP($phoneNumber, $authId, $isMobile)
    {
        $headers = [
            'AM-PHONENUMBER' => $phoneNumber,
            'AM-SEND' => 'otp',
            'AM-CLIENTID' => $isMobile ? env('FR_CLIENTID_MOBILE') : env('FR_CLIENTID'),
            'Content-Type' => 'application/json'
        ];

        $passwordCallback = [
            'type' => 'PasswordCallback',
            'output' => [
                ['name' => 'prompt', 'value' => 'One Time Password']
            ],
            'input' => [
                ['name' => 'IDToken1', 'value' => '']
            ]
        ];
        $confirmationCallback = [
            'type' => 'ConfirmationCallback',
            'output' => [
                ['name' => 'prompt', 'value' => ''],
                ['name' => 'messageType', 'value' => 0],
                ['name' => 'options', 'value' => ['Submit OTP', 'Request OTP']],
                ['name' => 'optionType', 'value' => -1],
                ['name' => 'defaultOption', 'value' => 0],
            ],
            'input' => [
                ['name' => 'IDToken2', 'value' => 1]
            ]
        ];
        $data = [
            'authId' => $authId,
            'callbacks' => [
                $passwordCallback,
                $confirmationCallback
            ]
        ];

        return $this->postRequest('/iam/v1/realms/tsel/authenticate', $this->queryString,
            $data, $headers);
    }

    public function onBoardingRequestOTP($phoneNumber, $validationMethod, $userName, $isMobile)
    {
        $headers = [
            'phoneNumber' => $phoneNumber,
            'validationMethod' => $validationMethod,
            'AM-CLIENTID' => $isMobile ? env('FR_CLIENTID_MOBILE') : env('FR_CLIENTID'),
            'Content-Type' => 'application/json'
        ];
        $body = [

        ];

        return $this->postRequest('/iam/v1/profiles/onboarding', [], $body, $headers);
    }

}