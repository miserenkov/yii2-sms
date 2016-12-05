<?php
/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 03.12.2016 23:07
 */

namespace miserenkov\sms\logging;

interface LoggerInterface
{
    /**
     * Add new record to log
     *
     * @param array $data
     * @return bool
     */
    public function setRecord($data);

    /**
     * Update log record by sms_id
     *
     * @param string $sms_id
     * @param array $data
     * @return bool
     */
    public function updateRecordBySmsId($sms_id, $data);

    /**
     * Update log record by sms_id and recipient phone number
     *
     * @param string $sms_id
     * @param string $phone
     * @param array $data
     * @return bool
     */
    public function updateRecordBySmsIdAndPhone($sms_id, $phone, $data);
}