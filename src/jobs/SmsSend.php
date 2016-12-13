<?php
/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 14.12.2016 0:08
 */

namespace miserenkov\sms\jobs;


use miserenkov\gearman\JobBase;
use miserenkov\sms\Sms;

class SmsSend extends JobBase
{
    public function execute(\GearmanJob $job = null)
    {
        $workload = $this->getWorkload($job);
        if ($workload->phones && $workload->message) {
            \Yii::$app->sms->send($workload->phones, $workload->message, $workload->type, Sms::ACTION_REALLY);
        }
    }
}