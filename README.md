# Yii2 Sms component
Yii2 component for sending SMS messages through Smsc

[![License](https://poser.pugx.org/miserenkov/yii2-sms/license)](https://packagist.org/packages/miserenkov/yii2-sms)
[![Latest Stable Version](https://poser.pugx.org/miserenkov/yii2-sms/v/stable)](https://packagist.org/packages/miserenkov/yii2-sms)
[![Latest Unstable Version](https://poser.pugx.org/miserenkov/yii2-sms/v/unstable)](https://packagist.org/packages/miserenkov/yii2-sms)
[![Total Downloads](https://poser.pugx.org/miserenkov/yii2-sms/downloads)](https://packagist.org/packages/miserenkov/yii2-sms)
[![Build Status](https://travis-ci.org/miserenkov/yii2-sms.svg?branch=master)](https://travis-ci.org/miserenkov/yii2-sms)

## Support

[GitHub issues](https://github.com/miserenkov/yii2-sms).


## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist miserenkov/yii2-sms "^1.0"
```

or add

```
"miserenkov/yii2-sms": "^1.0"
```

to the require section of your `composer.json` file.

## Configuration

To use sender, you should configure it in the application configuration like the following
```php
'components' => [
    ...
    'sms' => [
        'class' => 'miserenkov\sms\Sms',
        'gateway' => 'smsc.ua',     // gateway, through which will sending sms, default 'smsc.ua'
        'login' => '',              // login
        'password' => '',           // password or lowercase password MD5-hash
        'senderName' => '',         // sender name
        'options' => [
            'useHttps' => true,     // use secure HTTPS connection, default true
        ],
    ],
    ...
],
```
#### Available gateways
```php
\miserenkov\sms\Sms::GATEWAY_UKRAINE,     // smsc.ua
\miserenkov\sms\Sms::GATEWAY_RUSSIA,      // smsc.ru
\miserenkov\sms\Sms::GATEWAY_KAZAKHSTAN,  // smsc.kz
\miserenkov\sms\Sms::GATEWAY_TAJIKISTAN,  // smsc.tj
\miserenkov\sms\Sms::GATEWAY_UZBEKISTAN,  // smsc.uz
\miserenkov\sms\Sms::GATEWAY_WORLD,       // smscentre.com
```

## Basic usages

#### Get balance
```php
/**
 * return an float in case of successful or false in case of error 
 */
Yii::$app->sms->getBalance();
```

#### Sending message
```php
/**
 * $phones an string for single number or array for multiple numbers
 * $message an string
 *
 * return an string sms identifier in case successful or false in case error
 */
Yii::$app->sms->send($phones, $message);
```

#### Get message status
```php
/**
 * $id sms identifier
 * $phone phone number of recipient
 *
 * return an array [
 *      status           - status code
 *      status_message   - status message
 *      err              - error code
 *      err_message      - error message
 *      time             - date of send
 *      cost             - message cost
 *      operator         - recipient operator
 *      region           - recipient region
 * ] in case successful or false in case error
 */
Yii::$app->sms->getStatus($id, $phone);
```