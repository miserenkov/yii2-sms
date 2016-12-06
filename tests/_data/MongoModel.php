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
    public static function collectionName()
    {
        return 'sms_log';
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