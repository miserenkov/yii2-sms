<?php
/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 03.12.2016 23:07
 */

namespace miserenkov\sms\logging;


use yii\base\InvalidConfigException;
use yii\base\Object;
use yii\db\BaseActiveRecord;
use yii\di\Instance;
use yii\db\Connection as SqlConnection;
use yii\mongodb\Connection as MongoConnection;
use yii\redis\Connection as RedisConnection;
use miserenkov\sms\logging\models\Sql;
use miserenkov\sms\logging\models\Mongo;
use miserenkov\sms\logging\models\Redis;

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

    /**
     * Log table model
     * @var Mongo|Redis|Sql
     */
    private $_model;

    public function init()
    {
        if (empty($this->connection)) {
            throw new InvalidConfigException('');
        } else {
            $this->connection = Instance::ensure($this->connection);

            if ($this->connection instanceof SqlConnection) {
                $this->_model = Sql::class;
            } elseif ($this->connection instanceof MongoConnection) {
                $this->_model = Mongo::class;
            } elseif ($this->connection instanceof RedisConnection) {
                $this->_model = Redis::class;
            } else {
                throw new InvalidConfigException('');
            }
        }
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @inheritdoc
     */
    public function setRecord($data)
    {
        $record = new $this->_model();
        $record->setDb($this->connection);
        foreach ($data as $key => $value) {
            if ($record->hasAttribute($key)) {
                $record->$key = $value;
            }
        }
        return $record->save();
    }

    /**
     * @inheritdoc
     */
    public function updateRecordBySmsId($sms_id, $data)
    {
        if (!empty($sms_id)) {
            $record = new $this->_model();
            $record->setDb($this->connection);
            $record->findOne(['sms_id' => $sms_id]);
            if ($record instanceof BaseActiveRecord) {
                foreach ($data as $key => $value) {
                    if ($record->hasAttribute($key)) {
                        $record->$key = $value;
                    }
                }

                return $record->save();
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function updateRecordBySmsIdAndPhone($sms_id, $phone, $data)
    {
        if (!empty($sms_id)) {
            $record = new $this->_model();
            $record->setDb($this->connection);
            $record->findOne(['sms_id' => $sms_id, 'phone' => $phone]);
            if ($record instanceof BaseActiveRecord) {
                foreach ($data as $key => $value) {
                    if ($record->hasAttribute($key)) {
                        $record->$key = $value;
                    }
                }

                return $record->save();
            }
        }

        return false;
    }
}