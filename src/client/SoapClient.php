<?php
/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 30.11.2016 11:39
 */

namespace miserenkov\sms\client;

use miserenkov\sms\exceptions\BalanceException;
use miserenkov\sms\exceptions\Exception;
use miserenkov\sms\exceptions\SendException;
use miserenkov\sms\exceptions\StatusException;
use yii\base\InvalidConfigException;

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
        $https = true;
        $this->_login = $login;
        $this->_password = $password;
        $this->_senderName = $senderName;
        if (isset($options['throwExceptions'])) {
            $this->_throwExceptions = $options['throwExceptions'];
        }
        if (isset($options['useHttps']) && is_bool($options['useHttps'])) {
            $https = $options['useHttps'];
        }
        parent::__construct($this->getWsdl($gateway, $https), []);
    }

    private function getWsdl($gateway, $useHttps = true)
    {
        $httpsWsdl = 'https://' . $gateway . '/sys/soap.php?wsdl';
        $httpWsdl = 'http://' . $gateway . '/sys/soap.php?wsdl';

        if ($useHttps) {
            $ch = curl_init($httpsWsdl);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $errorCode = curl_errno($ch);
            curl_close($ch);

            if ($errorCode === CURLE_OK && $httpCode === 200) {
                return $httpsWsdl;
            }
            \Yii::warning("Gateway \"$gateway\" doesn't support https connections. Will use http", self::class);
            if ($this->_throwExceptions) {
                throw new InvalidConfigException("Gateway \"$gateway\" doesn't support https connections. Will use http");
            }
        }

        return $httpWsdl;
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

            $response = (array) $response->sendresult;

            if (!isset($response['error'])) {
                return $response['id'];
            } else {
                \Yii::error(SendException::getErrorString((int) $response['error']), self::class);
                if ($this->_throwExceptions) {
                    throw new SendException((int) $response['error']);
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

        $response = (array) $response->statusresult;

        if (count($response) > 0) {
            if ((int) $response['error'] !== StatusException::NO_ERROR) {
                \Yii::error(SendException::getErrorString((int) $response['error']), self::class);
                if ($this->_throwExceptions) {
                    throw new StatusException((int) $response['error']);
                }
                return false;
            }

            return [
                'status' => (int) $response['status'],
                'status_message' => $this->getSendStatus((int) $response['status']),
                'err' => (int) $response['err'],
                'err_message' => $this->getSendStatusError((int) $response['err']),
                'time' => (int) $response['send_timestamp'],
                'cost' => (float) $response['cost'],
                'operator' => $response['operator'],
                'region' => $response['region'],
            ];
        } else {
            \Yii::error(Exception::getErrorString(Exception::EMPTY_RESPONSE), self::class);
            if ($this->_throwExceptions) {
                throw new Exception(Exception::EMPTY_RESPONSE);
            }
            return false;
        }
    }

    private function getSendStatus($code)
    {
        $codes = [
            -3 => 'The message is not found',
            -1 => 'Waiting to be sent',
            0  => 'Transferred to the operator',
            1  => 'Delivered',
            3  => 'Expired',
            20 => 'It is impossible to deliver',
            22 => 'Wrong number',
            23 => 'Prohibited',
            24 => 'Insufficient funds',
            25 => 'Unavailable number',
        ];

        if (isset($codes[$code])) {
            return $codes[$code];
        } else {
            return 'Unknown status';
        }
    }

    private function getSendStatusError($error)
    {
        $errors = [
            0	=> 'There are no errors',
            1	=> 'The subscriber does not exist',
            6	=> 'The subscriber is not online',
            11	=> 'No SMS',
            13	=> 'The subscriber is blocked',
            21	=> 'There is no support for SMS',
            200	=> 'Virtual dispatch',
            220	=> 'Queue overflowed from the operator',
            240	=> 'Subscriber is busy',
            241	=> 'Error converting audio',
            242	=> 'Recorded answering machine',
            243	=> 'Not a contract',
            244	=> 'Distribution is prohibited',
            245	=> 'Status is not received',
            246	=> 'A time limit',
            247	=> 'Limit exceeded messages',
            248	=> 'There is no route',
            249	=> 'Invalid number format',
            250	=> 'The phone number of prohibited settings',
            251	=> 'Limit is exceeded on a single number',
            252	=> 'Phone number is prohibited',
            253	=> 'Prohibited spam filter',
            254	=> 'Unregistered sender id',
            255	=> 'Rejected by the operator',
        ];

        if (isset($errors[$error])) {
            return $errors[$error];
        } else {
            return 'Unknown error';
        }
    }
}