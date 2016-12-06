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
    public function testLoggersArrayConfig()
    {
        Yii::$app->set('db', [
            'class' => '\yii\db\Connection',
            'dsn' => 'mysql:host=127.0.0.1;dbname=travis_test',
            'username' => 'travis',
            'password' => '',
            'charset' => 'utf8',
        ]);
        Yii::$app->set('sms', [
            'class' => '\miserenkov\sms\Sms',
            'login' => 'phpunit',
            'password' => '85af727fd022d3a13e7972fd6a418582',
            'logging' => [
                'connection' => [
                    'class' => '\yii\db\Connection',
                    'dsn' => 'mysql:host=127.0.0.1;dbname=travis_test',
                    'username' => 'travis',
                    'password' => '',
                    'charset' => 'utf8',
                ],
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

        Yii::$app->set('db', [
            'class' => '\yii\db\Connection',
            'dsn' => 'pgsql:host=127.0.0.1;dbname=travis_test',
            'username' => 'postgres',
            'password' => '',
            'charset' => 'utf8',
        ]);
        Yii::$app->set('sms', [
            'class' => '\miserenkov\sms\Sms',
            'login' => 'phpunit',
            'password' => '85af727fd022d3a13e7972fd6a418582',
            'logging' => [
                'connection' => [
                    'class' => '\yii\db\Connection',
                    'dsn' => 'pgsql:host=127.0.0.1;dbname=travis_test',
                    'username' => 'postgres',
                    'password' => '',
                    'charset' => 'utf8',
                ],
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


        Yii::$app->set('sms', [
            'class' => '\miserenkov\sms\Sms',
            'login' => 'phpunit',
            'password' => '85af727fd022d3a13e7972fd6a418582',
            'logging' => [
                'connection' => [
                    'class' => '\yii\mongodb\Connection',
                    'dsn' => 'mongodb://travis:test@127.0.0.1:27017/travis_test',
                ],
            ],
        ]);
        $this->assertInstanceOf(\yii\mongodb\Connection::class, Yii::$app->sms->getLogger()->connection);

        Yii::$app->set('sms', [
            'class' => '\miserenkov\sms\Sms',
            'login' => 'phpunit',
            'password' => '85af727fd022d3a13e7972fd6a418582',
            'logging' => [
                'connection' => [
                    'class' => '\yii\redis\Connection',
                    'hostname' => '127.0.0.1',
                    'port' => 6379,
                    'database' => 0,
                ],
            ],
        ]);
        $this->assertInstanceOf(\yii\redis\Connection::class, Yii::$app->sms->getLogger()->connection);
    }

    public function testLoggersStringConfig()
    {
        Yii::$app->set('mysql_db', [
            'class' => '\yii\db\Connection',
            'dsn' => 'mysql:host=127.0.0.1;dbname=travis_test',
            'username' => 'travis',
            'password' => '',
            'charset' => 'utf8',
        ]);
        Yii::$app->set('sms', [
            'class' => '\miserenkov\sms\Sms',
            'login' => 'phpunit',
            'password' => '85af727fd022d3a13e7972fd6a418582',
            'logging' => [
                'connection' => 'mysql_db',
            ],
        ]);
        $this->assertInstanceOf(\yii\db\Connection::class, Yii::$app->sms->getLogger()->connection);

        Yii::$app->set('pgsql_db', [
            'class' => '\yii\db\Connection',
            'dsn' => 'pgsql:host=127.0.0.1;dbname=travis_test',
            'username' => 'postgres',
            'password' => '',
            'charset' => 'utf8',
        ]);
        Yii::$app->set('sms', [
            'class' => '\miserenkov\sms\Sms',
            'login' => 'phpunit',
            'password' => '85af727fd022d3a13e7972fd6a418582',
            'logging' => [
                'connection' => 'pgsql_db',
            ],
        ]);
        $this->assertInstanceOf(\yii\db\Connection::class, Yii::$app->sms->getLogger()->connection);

        Yii::$app->set('mongo_db', [
            'class' => '\yii\mongodb\Connection',
            'dsn' => 'mongodb://travis:test@127.0.0.1:27017/travis_test',
        ]);
        Yii::$app->set('sms', [
            'class' => '\miserenkov\sms\Sms',
            'login' => 'phpunit',
            'password' => '85af727fd022d3a13e7972fd6a418582',
            'logging' => [
                'connection' => 'mongo_db',
            ],
        ]);
        $this->assertInstanceOf(\yii\mongodb\Connection::class, Yii::$app->sms->getLogger()->connection);

        Yii::$app->set('redis_db', [
            'class' => '\yii\redis\Connection',
            'hostname' => '127.0.0.1',
            'port' => 6379,
            'database' => 0,
        ]);
        Yii::$app->set('sms', [
            'class' => '\miserenkov\sms\Sms',
            'login' => 'phpunit',
            'password' => '85af727fd022d3a13e7972fd6a418582',
            'logging' => [
                'connection' => 'redis_db',
            ],
        ]);
        $this->assertInstanceOf(\yii\redis\Connection::class, Yii::$app->sms->getLogger()->connection);
    }
}