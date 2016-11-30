<?php
/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 30.11.2016 11:26
 */

namespace miserenkov\sms\clients;

use miserenkov\sms\BalanceException;
use miserenkov\sms\SendException;

interface ClientInterface {
    /**
     * ClientInterface constructor.
     * @param string $login
     * @param string $password
     * @param string $senderName
     * @param array $options
     */
    public function __construct($login, $password, $senderName, $options = []);

    /**
     * Get user balance on http://smsc.ru
     * @throws BalanceException
     * @return float|bool
     */
    public function getBalance();

    /**
     * @param array $params
     * @throws SendException
     * @return string
     */
    public function sendMessage(array $params);

    /**
     * @param string $id
     * @param string $phone
     * @param int $all
     * @return array
     */
    public function getMessageStatus($id, $phone, $all = 2);
}