<?php

use yii\db\Schema;
use yii\db\Migration;

class m160503_234630_create_reminder_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%reminder}}', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_BIGINT.' NOT NULL',
            'duration_friendly' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
            'unit' => Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0',
            'duration' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
            'reminder_type' => Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
        $this->addForeignKey('fk_reminder_user', '{{%reminder}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
 	  	$this->dropForeignKey('fk_reminder_user', '{{%reminder}}');
        $this->dropTable('{{%reminder}}');
    }
}
