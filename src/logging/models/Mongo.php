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

    public static $tableName;

    public function setDb($connection)
    {
        static::$db = $connection;
    }

    public static function getDb()
    {
        return static::$db;
    }

    public static function setTableName($tableName)
    {
        static::$tableName = $tableName;
    }

    public static function collectionName()
    {
        return preg_replace('/[^A-z_]/', '', static::$tableName);
    }


    public function rules()
    {
        return [
            [['phone', 'message'], 'required'],
            [['type', 'send_time', 'status', 'error'], 'integer'],
            [['sms_id'], 'string', 'max' => 40],
            [['phone'], 'string', 'max' => 25],
            [['message'], 'string', 'max' => 800],
            [['cost'], 'number'],
            [['operator'], 'string', 'max' => 50],
            [['region'], 'string', 'max' => 150],
        ];
    }

    public function attributes()
    {
        return [
            '_id',
            'sms_id',
            'phone',
            'message',
            'type',
            'send_time',
            'cost',
            'status',
            'error',
            'operator',
            'region',
        ];
    }
}