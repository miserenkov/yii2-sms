<?php
/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 06.12.2016 22:13
 */

namespace data;

class MongoModel extends \yii\mongodb\ActiveRecord
{
    public static function getDb()
    {
        return \Yii::$app->sms->getLogger()->getConnection();
    }

    public static function collectionName()
    {
        return preg_replace('/[^A-z_]/', '', \Yii::$app->sms->getLogger()->getTableName());
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