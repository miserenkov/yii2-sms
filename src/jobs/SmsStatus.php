<?php
/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 14.12.2016 0:13
 */

namespace miserenkov\sms\jobs;


use miserenkov\gearman\JobBase;

class SmsStatus extends JobBase
{
    public function execute(\GearmanJob $job = null)
    {
        $workload = $this->getWorkload($job);
    }
}