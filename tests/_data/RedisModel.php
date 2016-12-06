<?php
/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 06.12.2016 21:48
 */

namespace data;


use yii\redis\ActiveRecord;

class RedisModel extends ActiveRecord
{
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