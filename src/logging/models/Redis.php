<?php
/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 03.12.2016 19:20
 */

namespace miserenkov\sms\logging\models;


use yii\redis\ActiveRecord;

class Redis extends ActiveRecord
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

    public static function setTableName($tableName)
    {

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
            'id',
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