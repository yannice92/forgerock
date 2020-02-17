<?php
/**
 * Samuelerwardi samuelerwardi@gmail.com
 */

namespace App\Forgerock;


final class MemberPimcore
{
    private static $instance = null;
    /**
     * Private ctor so nobody else can instantiate it
     *
     */
    private function __construct()
    {
    }

    /**
     * Call this method to get singleton
     * @param $memberForgeRock
     * @return \Pimcore\Model\DataObject|null
     */
    public static function Instance($memberForgeRock)
    {
        if (self::$instance === null) {
            $member = new  \App\Forgerock\Repositories\MembersRepository(new \Illuminate\Container\Container());
            $result = $member->findBy("forgeRockId", $memberForgeRock->_id);
            if (count($result) == 1){
                self::$instance = $result[0];
            }
        }
        return self::$instance;
    }

}


