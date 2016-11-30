<?php
/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 30.11.2016 11:39
 */

namespace miserenkov\sms\clients;


use miserenkov\sms\BalanceException;

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
    public function __construct($login, $password, $senderName, $options = [])
    {
        parent::__construct('http://smsc.ua/sys/soap.php?wsdl', []);
        $this->_login = $login;
        $this->_password = $password;
        $this->_senderName = $senderName;
        if (isset($options['throwExceptions'])) {
            $this->_throwExceptions = $options['throwExceptions'];
        }

        $this->getBalance();
    }

    /**
     * Get user balance on http://smsc.ru
     * @return bool|float
     * @throws BalanceException
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
                if ($this->_throwExceptions) {
                    throw new BalanceException(BalanceException::ERROR_NOT_MONEY);
                } else {
                    \Yii::warning(BalanceException::getErrorString(BalanceException::ERROR_NOT_MONEY), self::class);
                }
            }

            return $balance;
        } else {
            if ($this->_throwExceptions) {
                throw new BalanceException((int) $response->balanceresult->error);
            } else {
                \Yii::error(BalanceException::getErrorString((int) $response->balanceresult->error), self::class);
            }

            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function sendMessage(array $params)
    {
        
    }
}