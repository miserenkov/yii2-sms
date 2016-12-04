<?php
/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 03.12.2016 23:07
 */

namespace miserenkov\sms\logging;


use yii\base\Object;

class Logger extends Object implements LoggerInterface
{
    /**
     * @var string
     */
    public $tableName = '{{%sms_log}}';

    /**
     * @var array|string|\yii\db\Connection|\yii\mongodb\Connection|\yii\redis\Connection
     */
    public $connection = null;

    public function init()
    {

    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}