<?php

use yii\db\Schema;
use yii\db\Migration;

class m160704_202157_create_message_log_table extends Migration
{
  public function up()
  {
      $tableOptions = null;
      if ($this->db->driverName === 'mysql') {
          $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
      }

      $this->createTable('{{%message_log}}', [
          'id' => Schema::TYPE_PK,
          'message_id' => Schema::TYPE_INTEGER.' NOT NULL',
          'user_id' => Schema::TYPE_BIGINT.' NOT NULL',
          'response' => Schema::TYPE_SMALLINT.' NOT NULL',
      ], $tableOptions);
      $this->addForeignKey('fk_message_log_message', '{{%message_log}}', 'message_id', '{{%message}}', 'id', 'CASCADE', 'CASCADE');
      $this->addForeignKey('fk_message_log_user', '{{%message_log}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');

  }

  public function down()
  {
    $this->dropForeignKey('fk_message_log_user', '{{%message_log}}');
    $this->dropForeignKey('fk_message_log_message', '{{%message_log}}');

      $this->dropTable('{{%message_log}}');
  }
}
