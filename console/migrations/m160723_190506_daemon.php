<?php

use yii\db\Schema;
use yii\db\Migration;

class m160723_190506_daemon extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%daemon}}', [
            'id' => Schema::TYPE_PK,
            'action_id' => Schema::TYPE_SMALLINT . ' NOT NULL',
            'task_id' => Schema::TYPE_SMALLINT . ' NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%daemon}}');
    }
  }
