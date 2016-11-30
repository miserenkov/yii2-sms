<?php
/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 30.11.2016 11:39
 */

namespace miserenkov\sms\clients;


class SoapClient extends \SoapClient implements ClientInterface
{
    /**
     * @inheritdoc
     */
    public function __construct($login, $password, $senderName, $options = [])
    {

    }

    /**
     * @inheritdoc
     */
    public function getBalance()
    {
        
    }

    /**
     * @inheritdoc
     */
    public function send($numbers, $message)
    {
        
    }
}