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
    const GATEWAY_UKRAINE       = 'smsc.ua';
    const GATEWAY_RUSSIA        = 'smsc.ru';
    const GATEWAY_KAZAKHSTAN    = 'smsc.kz';
    const GATEWAY_TAJIKISTAN    = 'smsc.tj';
    const GATEWAY_UZBEKISTAN    = 'smsc.uz';
    const GATEWAY_WORLD         = 'smscentre.com';

    const TYPE_DEFAULT_MESSAGE = 0;
    const TYPE_REGISTRATION_MESSAGE = 1;
    const TYPE_RESET_PASSWORD_MESSAGE = 2;
    const TYPE_LOGIN_MESSAGE = 3;
    const TYPE_NOTIFICATION_MESSAGE = 4;
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
     * @var array
     */
    public $options = [];

    /**
     * @var SoapClient
     */
    protected $_client = null;

    /**
     * Allowed gateways
     *
     * @var array
     */
    protected static $_allowedGateways = [
        self::GATEWAY_UKRAINE,
        self::GATEWAY_RUSSIA,
        self::GATEWAY_KAZAKHSTAN,
        self::GATEWAY_TAJIKISTAN,
        self::GATEWAY_UZBEKISTAN,
        self::GATEWAY_WORLD,
    ];

    /**
     * Allowed message types
     *
     * @return array
     */
    protected function allowedTypes()
    {
        return [
            self::TYPE_DEFAULT_MESSAGE,
            self::TYPE_REGISTRATION_MESSAGE,
            self::TYPE_RESET_PASSWORD_MESSAGE,
            self::TYPE_LOGIN_MESSAGE,
            self::TYPE_NOTIFICATION_MESSAGE,
        ];
    }

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
                $this->options,
            ]);
        }
    }

    /**
     * Get balance
     *
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
     * Send sms message
     *
     * @param string|array $numbers
     * @param string $message
     * @param int $type
     * @return bool|string
     * @throws NotSupportedException|\InvalidArgumentException
     */
    public function send($numbers, $message, $type = self::TYPE_DEFAULT_MESSAGE)
    {
        if (!in_array($type, $this->allowedTypes())) {
            throw new NotSupportedException("Message type \"$type\" doesn't support.");
        }

        if (empty($numbers) || count($numbers) === 0 || empty($message)) {
            throw new \InvalidArgumentException('For sending sms, please, set phone number and message');
        }

        return $this->_client->sendMessage([
            'phones' => $numbers,
            'message' => $message,
            'id' => $this->smsIdGenerator(),
        ]);
    }

    /**
     * Get sms status by id and phone
     *
     * @param string $id
     * @param string $phone
     * @param int $all
     * @return array|bool
     * @throws \InvalidArgumentException
     */
    public function getStatus($id, $phone, $all = 2)
    {
        if (empty($id) || empty($phone)) {
            throw new \InvalidArgumentException('For getting sms status, please, set id and phone');
        }

        return $this->_client->getMessageStatus($id, $phone, $all);
    }
}