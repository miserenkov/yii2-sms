<?php
/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 03.12.2016 19:19
 */

namespace miserenkov\sms\logging\models;


use yii\mongodb\ActiveRecord;

class Mongo extends ActiveRecord
{
    public static $db;

    public function setDb($connection)
    {
        static::$db = $connection;
    }

    public static function getDb()
    {
        return static::$db;
    }
}