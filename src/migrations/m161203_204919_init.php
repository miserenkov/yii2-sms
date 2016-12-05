<?php

use yii\db\Migration;

class m161203_204919_init extends Migration
{
    private $tableName = '{{%sms_log}}';
    public function init()
    {
        /**
         * @var \miserenkov\sms\logging\Logger $logger
         */
        $logger = Yii::$app->sms->getLogger();
        if ($logger === false) {
            throw new Exception('Logger must be set');
        }
        $this->tableName = $logger->getTableName();
        $this->db = $logger->getConnection();
    }

    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned()->notNull(),
            'sms_id' => $this->string(40)->notNull(),
            'phone' => $this->string(25)->notNull(),
            'message' => $this->string(800),
            'type' => $this->smallInteger(3)->defaultValue(0),
            'send_time' => $this->integer(11)->unsigned()->notNull(),
            'cost' => $this->money(5,2)->unsigned(),
            'status' => $this->smallInteger(3),
            'error' => $this->smallInteger(3),
            'operator' => $this->string(50),
            'region' => $this->string(150)
        ]);
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
