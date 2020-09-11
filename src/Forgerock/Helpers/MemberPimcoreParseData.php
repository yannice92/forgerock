<?php
/**
 * Samuelerwardi samuelerwardi@gmail.com
 */

namespace App\Forgerock\Helpers;


use Pimcore\Model\DataObject\Member;

class MemberPimcoreParseData
{
    public static function getEmail(Member $member){
        if (!empty($member->getEmailAccount())){
            return $member->getEmailAccount();
        }
        if (!empty($member->getGoogleAccount())){
            return $member->getGoogleAccount();
        }
        if (!empty($member->getFacebookAccount())){
            return $member->getFacebookAccount();
        }
        if (!empty($member->getAppleAccount())){
            return $member->getAppleAccount();
        }
        return null;
    }
}
