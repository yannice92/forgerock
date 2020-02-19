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

class IdentityManagement extends ForgerockBase
{
    private $basicHeaders;
    private $user;
    public $oldAuthId;
    public $metaData;
    public $roleDuniagames = 'member';
    public $cmsRoleDuniaGames = 'member';
    public $isContentPublisher = false;
    public $isInfluencer = false;
    public $phoneNumbers = [];
    public $emails = [];

    function __construct()
    {
        parent::__construct();
        $this->basicHeaders = [
            'headers' => [
                'x-openidm-username' => env('FR_OPENIDM_USERNAME'),
                'Accept' => 'application/json',
                'x-openidm-password' => env('FR_OPENID_PASSWORD'),
            ]
        ];
    }

    private function getIdentifier($queryString)
    {
        return $this->getRequest('/openidm/managed/Identifier');
    }

    public function queryByPhoneNumber($phoneNumber)
    {
        $queryString = [
            '_queryFilter' => urldecode('identifierID eq "' . $phoneNumber . '"')
        ];
        $data = $this->getIdentifier($queryString);
        return $data;
    }

    public function getIdentifierByUUID($uuid)
    {
        return $this->getRequest('/openidm/managed/Identifier/' . $uuid);
    }

    public function deleteIdentifierByUUID($uuid)
    {
        return $this->deleteRequest('/openidm/managed/Identifier/' . $uuid);
    }

    public function getMe($token)
    {
        $queryString = [
            '_fields' => '*,identifiers/*,application/*'
        ];
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ];
        $this->user = $this->getRequest('/iam/v1/profiles/me', $queryString, $headers);
        dd($this->user);die;
        $this->parseData();
        return $this->user;
    }

    public function parseData()
    {
        $this->setIdentifier();
        $this->setMetaData();
    }

    public function setIdentifier()
    {
        $oldAuth0Id = null;
        if (isset($this->user->identifiers)) {
            $this->parseOldUid($this->user->identifiers);
            $this->parsePhoneNumber($this->user->identifiers);
            $this->parseEmail($this->user->identifiers);
        }
    }

    public function parseOldUid($identifiers)
    {
        $primaryIdentifier = Arr::first($identifiers, function ($value, $key) {
            return isset($value->oldUidDG) && $value->isPrimary == 'true';
        });
        if (isset($primaryIdentifier->oldUidDG)) {
            $this->oldAuthId = $primaryIdentifier->oldUidDG;
        }
    }

    public function parsePhoneNumber($identifiers)
    {
        $identifiersPhoneNumber = Arr::where($identifiers, function ($value, $key) {
            return $value->idenType === 'mobile';
        });

        $this->phoneNumbers = Arr::pluck($identifiersPhoneNumber, 'identifierID');
    }

    public function parseEmail($identifiers)
    {
        $identifiersEmail = Arr::where($identifiers, function ($value, $key) {
            return $value->idenType === 'email';
        });

        $this->emails = Arr::pluck($identifiersEmail, 'identifierID');
    }

    public function setMetaData()
    {
        $metaData = new \StdClass();
        if (isset($this->user->effectiveApplications)) {
            $duniaGamesApplicationData = Arr::first($this->user->effectiveApplications, function ($value, $key) {
                return $value->appType === 'duniagames';
            });
			if($duniaGamesApplicationData && isset($duniaGamesApplicationData->appMetadata)) {
				$appMetadata = \GuzzleHttp\json_decode($duniaGamesApplicationData->appMetadata);
				$this->parseMetaData($appMetadata);
			}
        }
        return $metaData;
    }

    public function parseMetaData($metaData)
    {
        if (isset($metaData->roleDuniagames)) {
            $this->roleDuniagames = $metaData->roleDuniagames;
        }

        if (isset($metaData->cmsRoleDuniaGames)) {
            $this->cmsRoleDuniaGames = $metaData->cmsRoleDuniaGames;
        }

        if (isset($metaData->isContentPublisher)) {
            $this->isContentPublisher = $metaData->isContentPublisher;
        }

        if (isset($metaData->isInfluencer)) {
            $this->isInfluencer = $metaData->isInfluencer;
        }
    }

    public function getFirstPhoneNumber()
    {
        $phoneNumber = null;
        if (count($this->phoneNumbers) > 0) {
            $phoneNumber = head($this->phoneNumbers);
        }
        return $phoneNumber;
    }

    public function getFirstEmail()
    {
        $email = null;
        if (count($this->emails) > 0) {
            $email = head($this->emails);
        }
        return $email;
    }

    public static function checkEligiblePhoneNumber($userPhoneNumbers, $paymentPhoneNumber)
    {

        $telkomselPhoneNumber = Arr::first($userPhoneNumbers, function ($value, $key) use ($paymentPhoneNumber) {
            if(strpos($paymentPhoneNumber,'+') !== 0){
                $paymentPhoneNumber = '+' . $paymentPhoneNumber;
            }
            $prefix = substr($paymentPhoneNumber, 0, 6);
            $telkomselPrefix = ['+62811', '+62812', '+62813', '+62821', '+62822', '+62823', '+62851', '+62852', '+62853'];
            return $value === $paymentPhoneNumber && in_array($prefix, $telkomselPrefix);
        });
        return ($telkomselPhoneNumber) ? true : false;
    }

    public static function isTelkomseNumber($phoneNumber)
    {
        if(strpos($phoneNumber,'+') !== 0){
            $phoneNumber = '+' . $phoneNumber;
        }
        $prefix = substr($phoneNumber, 0, 6);
        $telkomselPrefix = ['+62811', '+62812', '+62813', '+62821', '+62822', '+62823', '+62851', '+62852', '+62853'];
        return in_array($prefix, $telkomselPrefix);
    }
}
