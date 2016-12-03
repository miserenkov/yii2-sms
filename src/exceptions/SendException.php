<?php
/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 30.11.2016 12:02
 */

namespace miserenkov\sms\exceptions;

class SendException extends Exception
{
    const NO_ERROR = 0;
    const ERROR_INVALID_PARAMS = 1;
    const ERROR_WRONG_LOGIN_OR_PASSWORD = 2;
    const ERROR_NOT_MONEY = 3;
    const ERROR_IP_BANNED = 4;
    const ERROR_INVALID_DATE = 5;
    const ERROR_MESSAGE_BLOCKED = 6;
    const ERROR_INVALID_PHONE_NUMBER = 7;
    const ERROR_MESSAGE_NOT_DELIVERED = 8;
    const ERROR_API_CALL_LIMIT = 9;

    /**
     * @inheritdoc
     */
    protected static function errors()
    {
        return [
            self::NO_ERROR                      => 'There are no errors',
            self::ERROR_INVALID_PARAMS          => 'Error in the parameters',
            self::ERROR_WRONG_LOGIN_OR_PASSWORD => 'Invalid username or password',
            self::ERROR_NOT_MONEY               => 'Insufficient funds in the Customer\'s account',
            self::ERROR_IP_BANNED               => 'IP address is temporarily blocked',
            self::ERROR_INVALID_DATE            => 'Wrong date format',
            self::ERROR_MESSAGE_BLOCKED         => 'The message is prohibited',
            self::ERROR_INVALID_PHONE_NUMBER    => 'Invalid phone number format',
            self::ERROR_MESSAGE_NOT_DELIVERED   => 'The message to the specified number can not be delivered',
            self::ERROR_API_CALL_LIMIT          => 'Sending more than one identical request within minutes is prohibited',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'SendException';
    }
}