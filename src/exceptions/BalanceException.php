<?php
/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 30.11.2016 12:04
 */

namespace miserenkov\sms\exceptions;

class BalanceException extends Exception
{
    const NO_ERROR = 0;
    const ERROR_INVALID_PARAMS = 1;
    const ERROR_WRONG_LOGIN_OR_PASSWORD = 2;
    const ERROR_IP_BANNED = 4;
    const ERROR_API_CALL_LIMIT = 9;
    const ERROR_NOT_MONEY = 10;

    /**
     * @inheritdoc
     */
    protected static function errors()
    {
        return [
            self::NO_ERROR                      => 'There are no errors',
            self::ERROR_INVALID_PARAMS          => 'Error in the parameters',
            self::ERROR_WRONG_LOGIN_OR_PASSWORD => 'Invalid username or password',
            self::ERROR_IP_BANNED               => 'IP address is temporarily blocked',
            self::ERROR_API_CALL_LIMIT          => 'You have exceeded the limit of requests within minutes',
            self::ERROR_NOT_MONEY               => 'Insufficient funds in the Customer\'s account',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'BalanceException';
    }
}