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
                'phone' => $response['phone'],
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
            -3 => 'Сообщение не найдено',
            -1 => 'Ожидает отправки',
            0  => 'Передано оператору',
            1  => 'Доставлено',
            3  => 'Просрочено',
            20 => 'Невозможно доставить',
            22 => 'Неверный номер',
            23 => 'Запрещено',
            24 => 'Недостаточно средств',
            25 => 'Недоступный номер',
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
            0	=> 'Нет ошибки',
            1	=> 'Абонент не существует',
            6	=> 'Абонент не в сети',
            11	=> 'Нет услуги SMS',
            13	=> 'Абонент заблокирован',
            21	=> 'Нет поддержки SMS',
            200	=> 'Виртуальная отправка',
            220	=> 'Переполнена очередь у оператора',
            240	=> 'Абонент занят',
            241	=> 'Ошибка конвертации звука',
            242	=> 'Зафиксирован автоответчик',
            243	=> 'Не заключен договор',
            244	=> 'Рассылки запрещены',
            245	=> 'Статус не получен',
            246	=> 'Ограничение по времени',
            247	=> 'Превышен лимит сообщений',
            248	=> 'Нет маршрута',
            249	=> 'Неверный формат номера',
            250	=> 'Номер запрещен настройками',
            251	=> 'Превышен лимит на один номер',
            252	=> 'Номер запрещен',
            253	=> 'Запрещено спам-фильтром',
            254	=> 'Незарегистрированный sender id',
            255	=> 'Отклонено оператором',
        ];

        if (isset($errors[$error])) {
            return $errors[$error];
        } else {
            return 'Unknown error';
        }
    }
}