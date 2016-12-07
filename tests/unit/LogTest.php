<?php

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 03.12.2016 19:07
 */

/**
 * Class LogTest
 * @property \Codeception\Module\Yii2 $tester
 */
class LogTest extends \Codeception\Test\Unit
{
    public function testFailedConnections()
    {
        $caught = false;
        try {
            Yii::$app->set('sms', [
                'class' => '\miserenkov\sms\Sms',
                'login' => 'phpunit',
                'password' => '85af727fd022d3a13e7972fd6a418582',
                'logging' => [
                    'connection' => '',
                ],
            ]);
            Yii::$app->sms->getLogger();
        } catch (\yii\base\InvalidConfigException $e) {
            $this->assertEquals('Logging connection must be set.', $e->getMessage());
            $caught = true;
        }
        $this->assertTrue($caught, 'Caught invalid config exception');
    }

    public function testBadLogger()
    {
        Yii::$app->set('sms', [
            'class' => '\miserenkov\sms\Sms',
            'login' => 'phpunit',
            'password' => '85af727fd022d3a13e7972fd6a418582',
            'logging' => [
                'class' => 'data\BadLogger',
                'connection' => 'db',
            ],
        ]);
        $this->assertFalse(Yii::$app->sms->getLogger());
    }
    
    public function testMysqlConnections()
    {
        $db = [
            'class' => '\yii\db\Connection',
            'dsn' => 'mysql:host=127.0.0.1;dbname=travis_test',
            'username' => 'travis',
            'password' => '',
            'charset' => 'utf8',
        ];
        Yii::$app->set('db', $db);
        Yii::$app->set('sms', [
            'class' => '\miserenkov\sms\Sms',
            'login' => 'phpunit',
            'password' => '85af727fd022d3a13e7972fd6a418582',
            'logging' => [
                'connection' => $db,
            ],
        ]);
        $mysqlConnection = Yii::$app->sms->getLogger()->getConnection();
        $this->assertInstanceOf(\yii\db\Connection::class, $mysqlConnection);
        $tableName = Yii::$app->sms->getLogger()->getTableName();
        Yii::$app->runAction('migrate/up', [
            'migrationPath' => __DIR__.'/../../src/migrations',
            'interactive' => 0,
        ]);
        $this->assertInstanceOf(\yii\db\TableSchema::class, $mysqlConnection->schema->getTableSchema($tableName));

        Yii::$app->clear('sms');
        unset($mysqlConnection);
        Yii::$app->set('sms', [
            'class' => '\miserenkov\sms\Sms',
            'login' => 'phpunit',
            'password' => '85af727fd022d3a13e7972fd6a418582',
            'logging' => [
                'connection' => 'db',
            ],
        ]);
        $mysqlConnection = Yii::$app->sms->getLogger()->getConnection();
        $this->assertInstanceOf(\yii\db\Connection::class, $mysqlConnection);
        Yii::$app->clear('db');
        Yii::$app->clear('sms');
    }

    public function testPgSqlConnections()
    {
        $db = [
            'class' => '\yii\db\Connection',
            'dsn' => 'pgsql:host=127.0.0.1;dbname=travis_test',
            'username' => 'postgres',
            'password' => '',
            'charset' => 'utf8',
        ];

        Yii::$app->set('db', $db);
        Yii::$app->set('sms', [
            'class' => '\miserenkov\sms\Sms',
            'login' => 'phpunit',
            'password' => '85af727fd022d3a13e7972fd6a418582',
            'logging' => [
                'connection' => $db,
            ],
        ]);
        $pgsqlConnection = Yii::$app->sms->getLogger()->getConnection();
        $this->assertInstanceOf(\yii\db\Connection::class, $pgsqlConnection);
        $tableName = Yii::$app->sms->getLogger()->getTableName();
        Yii::$app->runAction('migrate/up', [
            'migrationPath' => __DIR__.'/../../src/migrations',
            'interactive' => 0,
        ]);
        $this->assertInstanceOf(\yii\db\TableSchema::class, $pgsqlConnection->schema->getTableSchema($tableName));

        Yii::$app->clear('sms');
        unset($pgsqlConnection);
        Yii::$app->set('sms', [
            'class' => '\miserenkov\sms\Sms',
            'login' => 'phpunit',
            'password' => '85af727fd022d3a13e7972fd6a418582',
            'logging' => [
                'connection' => 'db',
            ],
        ]);
        $pgsqlConnection = Yii::$app->sms->getLogger()->getConnection();
        $this->assertInstanceOf(\yii\db\Connection::class, $pgsqlConnection);
        Yii::$app->clear('db');
        Yii::$app->clear('sms');
    }

    public function testMongoConnections()
    {
        $mongo = [
            'class' => '\yii\mongodb\Connection',
            'dsn' => 'mongodb://travis:test@127.0.0.1:27017/travis_test',
        ];
        Yii::$app->set('mongo', $mongo);
        Yii::$app->set('sms', [
            'class' => '\miserenkov\sms\Sms',
            'login' => 'phpunit',
            'password' => '85af727fd022d3a13e7972fd6a418582',
            'logging' => [
                'connection' => $mongo,
            ],
        ]);
        $this->assertInstanceOf(\yii\mongodb\Connection::class, Yii::$app->sms->getLogger()->connection);

        Yii::$app->clear('sms');
        Yii::$app->set('sms', [
            'class' => '\miserenkov\sms\Sms',
            'login' => 'phpunit',
            'password' => '85af727fd022d3a13e7972fd6a418582',
            'logging' => [
                'connection' => 'mongo',
            ],
        ]);
        $this->assertInstanceOf(\yii\mongodb\Connection::class, Yii::$app->sms->getLogger()->connection);
    }

    public function testRedisConnections()
    {
        $redis = [
            'class' => '\yii\redis\Connection',
            'hostname' => '127.0.0.1',
            'port' => 6379,
            'database' => 0,
        ];
        Yii::$app->set('redis', $redis);
        Yii::$app->set('sms', [
            'class' => '\miserenkov\sms\Sms',
            'login' => 'phpunit',
            'password' => '85af727fd022d3a13e7972fd6a418582',
            'logging' => [
                'connection' => $redis,
            ],
        ]);
        $this->assertInstanceOf(\yii\redis\Connection::class, Yii::$app->sms->getLogger()->connection);

        Yii::$app->clear('sms');
        Yii::$app->set('sms', [
            'class' => '\miserenkov\sms\Sms',
            'login' => 'phpunit',
            'password' => '85af727fd022d3a13e7972fd6a418582',
            'logging' => [
                'connection' => 'redis',
            ],
        ]);
        $this->assertInstanceOf(\yii\redis\Connection::class, Yii::$app->sms->getLogger()->connection);
    }

//    public function testWriteAndReadRedisLog()
//    {
//        $sms = [];
//        Yii::$app->set('redis', [
//            'class' => '\yii\redis\Connection',
//            'hostname' => '127.0.0.1',
//            'port' => 6379,
//            'database' => 0,
//        ]);
//        Yii::$app->set('sms', [
//            'class' => '\miserenkov\sms\Sms',
//            'login' => 'phpunit',
//            'password' => '85af727fd022d3a13e7972fd6a418582',
//            'logging' => [
//                'connection' => 'redis',
//            ],
//        ]);
//
//        for ($i = 0; $i < 10; $i++) {
//            $phone = sprintf('%s', rand(38050000000, 380509999999));
//            $message = 'Verify code: ' . rand();
//            $sms_id = Yii::$app->sms->send($phone, $message);
//            $sms[] = ['phone' => $phone, 'sms_id' => $sms_id];
//        }
//
//        foreach ($sms as $oneSms) {
//            if (is_string($oneSms['sms_id']) && strlen($oneSms['sms_id']) > 0) {
//                $status = Yii::$app->sms->getStatus($oneSms['sms_id'], $oneSms['phone']);
//                $this->tester->seeRecord('data\RedisModel', ['sms_id' => $oneSms['sms_id'], 'phone' => $oneSms['phone']]);
//            }
//        }
//    }

    public function testWriteAndReadMongoLog()
    {
        $sms = [];
        Yii::$app->set('mongodb', [
            'class' => '\yii\mongodb\Connection',
            'dsn' => 'mongodb://travis:test@127.0.0.1:27017/travis_test',
        ]);
        Yii::$app->set('sms', [
            'class' => '\miserenkov\sms\Sms',
            'login' => 'phpunit',
            'password' => '85af727fd022d3a13e7972fd6a418582',
            'logging' => [
                'connection' => 'mongodb',
            ],
        ]);

        for ($i = 0; $i < 10; $i++) {
            $phone = sprintf('%s',rand(38050000000, 380509999999));
            $message = 'Verify code: '.rand();
            $sms_id = Yii::$app->sms->send($phone, $message);
            $sms[] = ['phone' => $phone, 'sms_id' => $sms_id];
        }

        foreach ($sms as $oneSms) {
            if (is_string($oneSms['sms_id']) && strlen($oneSms['sms_id']) > 0) {
                $status = Yii::$app->sms->getStatus($oneSms['sms_id'], $oneSms['phone']);
                $this->tester->seeRecord('data\MongoModel', ['sms_id' => $oneSms['sms_id'], 'phone' => $oneSms['phone']]);
            }
        }
    }
}