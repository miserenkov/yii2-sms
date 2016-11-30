<?php

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 30.11.2016 11:02
 */
class SmsCTest extends \yii\codeception\TestCase
{
    public $appConfig = '@tests/unit/_config.php';

    public function testNotFoundClient()
    {
        $caught = false;
        try {
            Yii::$app->set('sms', [
                'class' => '\miserenkov\sms\SmsC',
                'client' => '',
            ]);
            $sms = Yii::$app->sms;
        } catch (\yii\base\UnknownClassException $e) {
            $caught = true;
        }
        $this->assertTrue($caught, 'Caught unknown class exception');
    }

    public function testBadClient()
    {
        $caught = false;
        try {
            Yii::$app->set('sms', [
                'class' => '\miserenkov\sms\SmsC',
                'client' => '\data\BadClient',
            ]);
            $sms = Yii::$app->sms;
        } catch (\yii\base\NotSupportedException $e) {
            $caught = true;
        }
        $this->assertTrue($caught, 'Caught not supported exception');
    }
}