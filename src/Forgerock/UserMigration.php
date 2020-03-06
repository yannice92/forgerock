<?php


namespace App\Forgerock;


use App\Helpers\LoggerHelper as Logger;

class UserMigration extends ForgerockBase
{
    function __construct()
    {
        parent::__construct();
    }

    public function createIdentity($data)
    {
        $userId = $data['user_id'];
        $identityType = $this->identityTypeByProvider($data['identities'][0]['provider']);
        $identityID = $this->identityIDByIdentityType($identityType, $data);
        $picture = $data['picture'] ?? "";
        $pictureLarge = $data['picture_large'] ?? "";
        $email = $data['email'] ?? '';
        if($identityType != 'mobile') {
            $givenName = $data['given_name'] ?? "";
            $displayName = $data['nickname'] ?? '';
            $name = $data['name'] ?? '';
        }else{
            $givenName = "";
            $displayName = '';
            $name = '';
        }
        $body = [
            "identifierID" => $identityID,
            "idenType" => $identityType,
            "givenName" => $givenName,
            "sn" => '',
            "fullName" => $name,
            "middleName" => "",
            "title" => '',
            "picture" => $picture,
            "pictureLarge" => $pictureLarge,
            "oldUidDG" => $userId,
            "emailAddr" => $email,
            "displayName" => $displayName,
            "appType" => env('FR_APP_TYPE'),
            "userInfo" => [
                "cn" => [$name],
                "sn" => [''],
                "identifierID" => [$identityID],
                "mail" => [$email],
                "identType" => [$identityType],
                "givenName" => [$givenName]
            ]
        ];
        $headers = ['Content-Type' => 'application/json'];
        return $this->request('POST','/iam/v1/profiles/createIdentity', [], $body, $headers);
    }

    public function handleMultipleIdentities($data,$userId){
        $identities = $data['identities'];
        $length = count($identities);
        $insertString = "";
        if($length > 1){
            $identityTypeMain = $this->identityTypeByProvider($identities[0]['provider']);
            $identityIDMain = $this->identityIDByIdentityType($identityTypeMain, $data);
            for ($i = 1; $i < $length; $i++) {
                $result = $this->linkIdentity($identities[$i],$userId,$identityIDMain);
                $response = $result->getBody()->getContents();
                $httpCode = $result->getStatusCode();
                $status = ($httpCode == "200")?"success (200)":"failed ($httpCode)";
                //date|userId|status|message (optional)
                $insertString .= Logger::loggerDateFormat()."|".$userId.'|'.$status.'|'.$response."\n";
            }
            Logger::recordToLogFile($insertString, "fr_migrate/linking-". date("Y-m-d-H") .".log");
        }
    }

    public function linkIdentity($identity, $userId, $identityIDMain)
    {
        $identityType = $this->identityTypeByProvider($identity['provider']);
        $identityID = $this->identityIDByIdentityType($identityType, $identity, "identities");

        $body = [
            "targetIdentifierID" => $identityIDMain,
            "changeType" => "link",
            "identifierID" => $identityID,
            "idenType" => $identityType,
            "oldUidDG" => $userId,
            "appType" => env('FR_APP_TYPE')
        ];
        if($identityType == "email"){
            $body["emailAddr"] = $identityID;
            $body["isVerified"] = $identity['profileData']['email_verified'];
        }elseif($identityType == "mobile"){
            $body["isVerified"] = $identity['profileData']['phone_verified'];
        }

        $headers = ['Content-Type' => 'application/json'];
        return $this->request('POST','/iam/v1/profiles/updateIdentity', [], $body, $headers);
    }

    private function identityTypeByProvider($provider){
        if($provider == 'sms'){
            return 'mobile';
        }elseif ($provider == 'google-oauth2'){
            return 'google';
        }elseif (in_array($provider, ['email', 'auth0'])){
            return 'email';
        }elseif($provider == 'twitter'){
            return 'twitter';
        }elseif($provider == 'facebook'){
            return 'facebook';
        }else{
            return "";
        }
    }

    private function identityIDByIdentityType($identityType, $data, $dataSource="main"){
        if($dataSource == "main"){
            if($identityType == 'mobile'){
                return $data['phone_number'];
            }elseif ($identityType == 'email'){
                return $data['email'];
            }elseif (in_array($identityType, ['google','twitter','facebook'])){
                return $identityType.'-'.$data['identities'][0]['user_id'];
            }
        }elseif($dataSource == 'identities'){
            if($identityType == 'mobile'){
                return $data['profileData']['phone_number'];
            }elseif ($identityType == 'email'){
                return $data['profileData']['email'];
            }elseif (in_array($identityType, ['google','twitter','facebook'])){
                return $identityType.'-'.$data['user_id'];
            }
        }
        return "";
    }
}
