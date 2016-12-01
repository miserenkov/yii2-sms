<?php
/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 30.11.2016 11:39
 */

namespace miserenkov\sms\client;

use miserenkov\sms\exceptions\BalanceException;
use miserenkov\sms\exceptions\SendException;
use miserenkov\sms\exceptions\StatusException;

class SoapClient extends \SoapClient implements ClientInterface
{
    /**
     * @var string
     */
    private $_login = null;

    /**
     * @var string
     */
    private $_password = null;

    /**
     * @var null|string
     */
    private $_senderName = null;

    /**
     * @var bool
     */
    private $_throwExceptions = false;

    /**
     * @inheritdoc
     */
    public function __construct($gateway, $login, $password, $senderName, $options = [])
    {
        parent::__construct('http://' . $gateway . '/sys/soap.php?wsdl', []);
        $this->_login = $login;
        $this->_password = $password;
        $this->_senderName = $senderName;
        if (isset($options['throwExceptions'])) {
            $this->_throwExceptions = $options['throwExceptions'];
        }
    }

    /**
     * @inheritdoc
     */
    public function getBalance()
    {
        $response = $this->get_balance([
            'login' => $this->_login,
            'psw' => $this->_password,
        ]);

        if ($response->balanceresult->error == BalanceException::NO_ERROR) {
            $balance = (double)$response->balanceresult->balance;

            if (round($balance) == 0) {
                \Yii::warning(BalanceException::getErrorString(BalanceException::ERROR_NOT_MONEY), self::class);
                if ($this->_throwExceptions) {
                    throw new BalanceException(BalanceException::ERROR_NOT_MONEY);
                }
            }

            return $balance;
        } else {
            \Yii::error(BalanceException::getErrorString((int) $response->balanceresult->error), self::class);
            if ($this->_throwExceptions) {
                throw new BalanceException((int) $response->balanceresult->error);
            }

            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function sendMessage(array $params)
    {
        $query = [
            'login' => $this->_login,
            'psw' => $this->_password,
            'sender' => $this->_senderName,
        ];
        if (isset($params['phones']) && isset($params['message'])) {
            if (!isset($params['tinyurl']) || ($params['tinyurl'] != 0 && $params['tinyurl'] != 1)) {
                $query['tinyurl'] = 1;
            } else {
                $query['tinyurl'] = $params['tinyurl'];
            }

            if (is_array($params['phones'])) {
                $query['phones'] = implode(';', $params['phones']);
            } else {
                $query['phones'] = $params['phones'];
            }
            $query['mes'] = $params['message'];
            $query['id'] = $params['id'];

            $response = $this->send_sms($query);

            if (!isset($response->sendresult->error)) {
                return $response->sendresult->id;
            } else {
                \Yii::error(SendException::getErrorString((int) $response->sendresult->error), self::class);
                if ($this->_throwExceptions) {
                    throw new SendException((int) $response->sendresult->error);
                }
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getMessageStatus($id, $phone, $all = 2)
    {
        $response = $this->get_status([
            'login' => $this->_login,
            'psw' => $this->_password,
            'phone' => $phone,
            'id' => $id,
            'all' => $all,
        ]);

        $response = $response->statusresult;

        return [
            'time' => (int)$response->send_timestamp,
            'phone' => $response->phone,
            'cost' => (double)$response->cost,
            'operator' => $response->operator,
            'region' => $response->region,
            'status' => (int)$response->status,
            'error' => (int)$response->error,
            'err' => (int)$response->err,
        ];
    }
}