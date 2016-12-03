<?php
/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 30.11.2016 22:37
 */

namespace miserenkov\sms\exceptions;

class StatusException extends Exception
{
    const NO_ERROR = 0;
    const ERROR_INVALID_PARAMS = 1;
    const ERROR_WRONG_LOGIN_OR_PASSWORD = 2;
    const ERROR_MESSAGE_NOT_FOUND = 3;
    const ERROR_IP_BANNED = 4;
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
            self::ERROR_MESSAGE_NOT_FOUND       => 'The message is not found',
            self::ERROR_IP_BANNED               => 'IP address is temporarily blocked',
            self::ERROR_API_CALL_LIMIT          => 'You have exceeded the limit of requests within minutes',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'StatusException';
    }

    public static function getErrorString($code)
    {
        if (isset(static::errors()[$code])) {
            return static::errors()[$code];
        } else {
            return 'Unknown error';
        }
    }
}