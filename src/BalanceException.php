<?php
/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 30.11.2016 12:04
 */

namespace miserenkov\sms;

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
            self::NO_ERROR => 'Ошибок нет',
            self::ERROR_INVALID_PARAMS => 'Ошибка в параметрах',
            self::ERROR_WRONG_LOGIN_OR_PASSWORD => 'Неверный логин или пароль',
            self::ERROR_IP_BANNED => 'IP-адрес временно заблокирован',
            self::ERROR_API_CALL_LIMIT => 'Превышен лимит запросов в течение минуты',
            self::ERROR_NOT_MONEY => 'Недостаточно средств на счете Клиента',
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