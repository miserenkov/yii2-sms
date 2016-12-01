<?php
/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 30.11.2016 11:14
 */

namespace miserenkov\sms;


use miserenkov\sms\client\SoapClient;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Object;
use yii\base\NotSupportedException;

class Sms extends Object
{
    /**
     * Api gateways
     */
    const GATEWAY_UKRAINE = 'smsc.ua';
    const GATEWAY_RUSSIA = 'smsc.ru';
    const GATEWAY_KAZAKHSTAN = 'smsc.kz';
    const GATEWAY_TAJIKISTAN = 'smsc.tj';
    const GATEWAY_UZBEKISTAN = 'smsc.uz';
    const GATEWAY_WORLD = 'smscentre.com';

    const TYPE_DEFAULT_MESSAGE = 0;
    const TYPE_REGISTRATION_MESSAGE = 1;

    /**
     * @var string
     */
    public $gateway = self::GATEWAY_UKRAINE;

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
     * @var SoapClient
     */
    protected $_client = null;

    protected static $_allowedGateways = [
        self::GATEWAY_UKRAINE,
        self::GATEWAY_RUSSIA,
        self::GATEWAY_KAZAKHSTAN,
        self::GATEWAY_TAJIKISTAN,
        self::GATEWAY_UZBEKISTAN,
        self::GATEWAY_WORLD,
    ];

    public function init()
    {
        if (empty($this->login) || empty($this->password)) {
            throw new InvalidConfigException('Login and password must be set.');
        }

        if (!in_array($this->gateway, self::$_allowedGateways)) {
            throw new InvalidConfigException("Gateway \"$this->gateway\" doesn't support.");
        }

        if ($this->_client === null) {
            $this->_client = Yii::createObject(SoapClient::class, [
                $this->gateway,
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
            throw new NotSupportedException("Message type \"$type\" doesn't support.");
        }
        return $this->_client->sendMessage([
            'phones' => $numbers,
            'message' => $message,
            'id' => $this->smsIdGenerator(),
        ]);
    }
}