<?php
/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 30.11.2016 12:02
 */

namespace miserenkov\sms;



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
            self::NO_ERROR => 'Ошибок нет',
            self::ERROR_INVALID_PARAMS => 'Ошибка в параметрах',
            self::ERROR_WRONG_LOGIN_OR_PASSWORD => 'Неверный логин или пароль',
            self::ERROR_NOT_MONEY => 'Недостаточно средств на счете Клиента',
            self::ERROR_IP_BANNED => 'IP-адрес временно заблокирован',
            self::ERROR_INVALID_DATE => 'Неверный формат даты',
            self::ERROR_MESSAGE_BLOCKED => 'Сообщение запрещено',
            self::ERROR_INVALID_PHONE_NUMBER => 'Неверный формат номера телефона',
            self::ERROR_MESSAGE_NOT_DELIVERED => 'Сообщение на указанный номер не может быть доставлено',
            self::ERROR_API_CALL_LIMIT => 'Отправка более одного одинакового запроса в течение минуты запрещена',
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