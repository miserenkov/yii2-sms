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

    public function testEmptyLoginOrPassword()
    {
        $caught = false;
        try {
            Yii::$app->set('sms', [
                'class' => '\miserenkov\sms\Sms',
                'login' => 'phpunit'
            ]);
            Yii::$app->sms;
        } catch (\yii\base\InvalidConfigException $e) {
            $this->assertEquals('Login and password must be set.', $e->getMessage());
            $caught = true;
        }

        $this->assertTrue($caught, 'Caught invalid config exception');

        $caught = false;
        try {
            Yii::$app->set('sms', [
                'class' => '\miserenkov\sms\Sms',
                'password' => '85af727fd022d3a13e7972fd6a418582'
            ]);
            Yii::$app->sms;
        } catch (\yii\base\InvalidConfigException $e) {
            $this->assertEquals('Login and password must be set.', $e->getMessage());
            $caught = true;
        }

        $this->assertTrue($caught, 'Caught invalid config exception');
    }

    public function testBadGateway()
    {
        $caught = false;
        try {
            Yii::$app->set('sms', [
                'class' => '\miserenkov\sms\Sms',
                'login' => 'phpunit',
                'password' => '85af727fd022d3a13e7972fd6a418582',
                'gateway' => 'smsc.by'
            ]);
            Yii::$app->sms;
        } catch (\yii\base\InvalidConfigException $e) {
            $this->assertEquals("Gateway \"smsc.by\" doesn't support.", $e->getMessage());
            $caught = true;
        }

        $this->assertTrue($caught, 'Caught not supported exception');
    }

    public function testBalanceException()
    {
        $caught = false;
        try {
            Yii::$app->set('sms', [
                'class' => '\miserenkov\sms\Sms',
                'login' => 'phpunit',
                'password' => 'wrong_password',
                'senderName' => 'PHPUnit',
                'throwExceptions' => true,
            ]);
            Yii::$app->sms->getBalance();
        } catch (\miserenkov\sms\exceptions\BalanceException $e) {
            $caught = true;
        }

        $this->assertTrue($caught, 'Caught balance exception');
    }

    public function testSendSMSException()
    {
        $caught = false;
        try {
            Yii::$app->set('sms', [
                'class' => '\miserenkov\sms\Sms',
                'login' => 'phpunit',
                'password' => 'wrong_password',
                'senderName' => 'PHPUnit',
                'throwExceptions' => true,
            ]);
            Yii::$app->sms->send('380501909090', 'Verify code: '.rand());
        } catch (\miserenkov\sms\exceptions\SendException $e) {
            $caught = true;
        }

        $this->assertTrue($caught, 'Caught send exception');
    }

    public function testAnotherGateways()
    {
        $gateways = [
            \miserenkov\sms\Sms::GATEWAY_UKRAINE,
            \miserenkov\sms\Sms::GATEWAY_RUSSIA,
            \miserenkov\sms\Sms::GATEWAY_KAZAKHSTAN,
            \miserenkov\sms\Sms::GATEWAY_TAJIKISTAN,
            \miserenkov\sms\Sms::GATEWAY_UZBEKISTAN,
            \miserenkov\sms\Sms::GATEWAY_WORLD,
        ];

        foreach ($gateways as $gateway) {
            Yii::$app->clear('sms');
            Yii::$app->set('sms', [
                'class' => '\miserenkov\sms\Sms',
                'login' => 'phpunit',
                'gateway' => $gateway,
                'password' => '85af727fd022d3a13e7972fd6a418582',
                'senderName' => 'PHPUnit',
            ]);
            $sms_id = Yii::$app->sms->send('380501909090', 'Verify code: '.rand());

            $this->assertTrue(is_string($sms_id) && strlen($sms_id) <= 40, "Sending SMS through the gateway '$gateway' successful");
        }
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
        $this->assertGreaterThanOrEqual(0, $balance, 'Balance greater or equal 0');
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
        $this->assertLessThanOrEqual(40, strlen($sms_id), 'Sms id less or equal 40 charset');
    }
}