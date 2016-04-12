<?php

use yii\db\Schema;
use yii\db\Migration;

class m160408_232704_user_profile_table extends Migration
{
  public function up()
  {
      $tableOptions = null;
      if ($this->db->driverName === 'mysql') {
          $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
      }

      $this->createTable('{{%user_profile}}', [
          'id' => Schema::TYPE_PK,
          'user_id' => Schema::TYPE_BIGINT.' NOT NULL',
          'firstname' => Schema::TYPE_STRING.' NOT NULL',
          'lastname' => Schema::TYPE_STRING.' NOT NULL',
          'fullname' => Schema::TYPE_STRING.' NOT NULL',
          'filename' => Schema::TYPE_STRING.' NOT NULL',
          'avatar' => Schema::TYPE_STRING.' NOT NULL',
          'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
          'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
      ], $tableOptions);
      $this->addForeignKey('fk_user_profile_user_id', '{{%user_profile}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
  }

  public function down()
  {
      $this->dropForeignKey('fk_user_profile_user_id', '{{%user_profile}}');
      $this->dropTable('{{%user_profile}}');
  }
}
