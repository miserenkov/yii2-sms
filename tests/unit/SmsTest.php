<?php

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 30.11.2016 11:02
 */
class SmsTest extends \yii\codeception\TestCase
{
    public $appConfig = '@tests/unit/_config.php';

    public function testNotFoundClient()
    {
        $caught = false;
        try {
            Yii::$app->set('sms', [
                'class' => '\miserenkov\sms\Sms',
                'clientClass' => '\data\NotFoundClient',
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
                'class' => '\miserenkov\sms\Sms',
                'clientClass' => '\data\BadClient',
            ]);
            $sms = Yii::$app->sms;
        } catch (\yii\base\NotSupportedException $e) {
            $caught = true;
        }

        $this->assertTrue($caught, 'Caught not supported exception');
    }

    public function testGetBalance()
    {
        Yii::$app->set('sms', [
            'class' => '\miserenkov\sms\Sms',
            'login' => 'phpunit',
            'password' => '85af727fd022d3a13e7972fd6a418582',
        ]);
        $balance = Yii::$app->sms->getBalance();

        $this->assertTrue(is_float($balance), 'Balance is float');
        $this->assertGreaterThanOrEqual(0, $balance, 'Balance greater 0');
    }

    public function testBalanceException()
    {
        $caught = false;
        try {
            Yii::$app->set('sms', [
                'class' => '\miserenkov\sms\Sms',
                'login' => 'phpunit',
                'password' => '',
                'senderName' => 'PHPUnit',
                'throwExceptions' => true,
            ]);
            Yii::$app->sms->getBalance();
        } catch (\miserenkov\sms\BalanceException $e) {
            $caught = true;
        }

        $this->assertTrue($caught, 'Caught balance exception');
    }

    public function testSendSMS()
    {
        Yii::$app->set('sms', [
            'class' => '\miserenkov\sms\Sms',
            'login' => 'phpunit',
            'password' => '85af727fd022d3a13e7972fd6a418582',
            'senderName' => 'PHPUnit',
        ]);
        $sms_id = Yii::$app->sms->send('380501909090', 'Verify code: '.rand());

        $this->assertTrue(is_string($sms_id), 'Sms id is string');
        $this->assertTrue(strlen($sms_id) <= 40, 'Sms id less 40 charset');
    }

    public function testSendSMSException()
    {
        $caught = false;
        try {
            Yii::$app->set('sms', [
                'class' => '\miserenkov\sms\Sms',
                'login' => 'phpunit',
                'password' => '',
                'senderName' => 'PHPUnit',
                'throwExceptions' => true,
            ]);
            Yii::$app->sms->send('380501909090', 'Verify code: '.rand());
        } catch (\miserenkov\sms\SendException $e) {
            $caught = true;
        }

        $this->assertTrue($caught, 'Caught send exception');
    }
}