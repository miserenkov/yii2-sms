<?php
/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 30.11.2016 15:45
 */

namespace miserenkov\sms\exceptions;


class Exception extends \yii\base\Exception
{
    /**
     * Exception constructor.
     * @param string $code
     */
    public function __construct($code)
    {
        parent::__construct(static::getErrorString($code), $code);
    }

    /**
     * @return array
     */
    protected static function errors()
    {
        return [];
    }

    /**
     * @param $code
     * @return string
     */
    public static function getErrorString($code)
    {
        if (isset(static::errors()[$code])) {
            return static::errors()[$code];
        } else {
            return 'Unknown error';
        }
    }
}