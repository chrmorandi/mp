<?php

use yii\db\Schema;
use yii\db\Migration;
/**
 * Handles the creation for table `message_table`.
 */
class m160701_002537_create_message_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%message}}', [
            'id' => Schema::TYPE_PK,
            'subject' => Schema::TYPE_STRING.' NOT NULL DEFAULT 0',
            'caption' => Schema::TYPE_TEXT.' NOT NULL DEFAULT ""',
            'content' => Schema::TYPE_TEXT.' NOT NULL DEFAULT ""',
            'action_text' => Schema::TYPE_STRING.' NOT NULL DEFAULT 0',
            'action_url' => Schema::TYPE_STRING.' NOT NULL DEFAULT 0',
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%message}}');
    }
}
