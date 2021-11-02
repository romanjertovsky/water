<?php


class Core
{


    private static $aObjects;


    private static function getInstance($sClassName)
    {

        if (empty(self::$aObjects[$sClassName])) {
            self::$aObjects[$sClassName] = new $sClassName;
        }

        return self::$aObjects[$sClassName];

    }


    /**
     * @return Db
     */
    public static function getDb()
    {
        return self::getInstance('Db');
    }


    /**
     * @return View
     */
    public static function getView()
    {
        return self::getInstance('View');
    }


    /**
     * @return Controller
     */
    public static function getController()
    {
        return self::getInstance('Controller');
    }


    /**
     * @return User
     */
    public static function getUser()
    {
        return self::getInstance('User');
    }


}