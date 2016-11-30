<?php
/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 30.11.2016 11:14
 */

namespace miserenkov\sms;


use miserenkov\sms\clients\ClientInterface;
use yii\base\NotSupportedException;
use yii\base\Object;
use yii\base\UnknownClassException;

class SmsC extends Object
{
    const TYPE_REGISTRATION_MESSAGE = 1;

    /**
     * @var string
     */
    public $client = '\miserenkov\sms\clients\SoapClient';

    /**
     * @var string
     */
    public $login = null;

    /**
     * @var string
     */
    public $password = null;

    /**
     * @var string
     */
    public $senderName = null;

    /**
     * @var ClientInterface
     */
    private $_client = null;

    public function init()
    {
        if ($this->_client === null) {
            if (!class_exists(\Yii::getAlias($this->client))) {
                throw new UnknownClassException("Client class \"$this->client\" not found.");
            }

            if (!in_array('\miserenkov\sms\clients\ClientInterface', class_implements($this->client))) {
                throw new NotSupportedException("Class \"$this->client\" not implemented \"\\miserenkov\\sms\\clients\\ClientInterface\"");
            }

            $this->_client = new $this->client($this->login, $this->password, $this->senderName, []);
        }
    }

    /**
     * @return array
     */
    public function allowedTypes()
    {
        return [
            self::TYPE_REGISTRATION_MESSAGE
        ];
    }

    /**
     * @return false|float
     */
    public function getBalance()
    {
        return $this->_client->getBalance();
    }

    public function send($numbers, $message) {

    }
}