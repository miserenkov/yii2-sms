<?php
/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 30.11.2016 11:14
 */

namespace miserenkov\sms;


use Yii;
use yii\base\Object;
use yii\base\NotSupportedException;
use yii\base\UnknownClassException;
use miserenkov\sms\clients\ClientInterface;

class Sms extends Object
{
    const TYPE_DEFAULT_MESSAGE = 0;
    const TYPE_REGISTRATION_MESSAGE = 1;

    /**
     * @var string
     */
    public $clientClass = '\miserenkov\sms\clients\smsc\SoapClient';

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
     * @var bool
     */
    public $throwExceptions = false;

    /**
     * @var ClientInterface
     */
    private $_client = null;

    public function init()
    {
        if ($this->_client === null) {
            $clientClassName = Yii::getAlias($this->clientClass);
            if (!class_exists($clientClassName)) {
                throw new UnknownClassException("Client class \"$clientClassName\" not found.");
            }

            $interfaceClassName = ClientInterface::class;
            if (!in_array($interfaceClassName, class_implements($clientClassName))) {
                throw new NotSupportedException("Class \"$clientClassName\" not implemented \"$interfaceClassName\"");
            }

            $this->_client = Yii::createObject($clientClassName, [
                $this->login,
                $this->password,
                $this->senderName,
                [
                    'throwExceptions' => $this->throwExceptions,
                ],
            ]);
        }
    }

    /**
     * @return array
     */
    public function allowedTypes()
    {
        return [
            self::TYPE_DEFAULT_MESSAGE,
            self::TYPE_REGISTRATION_MESSAGE,
        ];
    }

    /**
     * @return false|float
     */
    public function getBalance()
    {
        return $this->_client->getBalance();
    }

    /**
     * Generate random sms identifier
     * @return string
     */
    protected function smsIdGenerator()
    {
        return Yii::$app->security->generateRandomString(40);
    }

    /**
     * @param $numbers
     * @param $message
     * @param int $type
     * @return bool|string
     * @throws NotSupportedException
     */
    public function send($numbers, $message, $type = self::TYPE_DEFAULT_MESSAGE)
    {
        if (!in_array($type, $this->allowedTypes())) {
            throw new NotSupportedException("Message type \"$type\" doesn't support");
        }
        return $this->_client->sendMessage([
            'phones' => $numbers,
            'message' => $message,
            'id' => $this->smsIdGenerator(),
        ]);
    }
}