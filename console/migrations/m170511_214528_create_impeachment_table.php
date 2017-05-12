<?php

use yii\db\Schema;
use yii\db\Migration;

class m170511_214528_create_impeachment_table extends Migration
{
  public function up()
  {
      $tableOptions = null;
      if ($this->db->driverName === 'mysql') {
          $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
      }

      $this->createTable('{{%impeachment}}', [
          'id' => Schema::TYPE_PK,
          'user_id' => Schema::TYPE_BIGINT.' NOT NULL',
          'referrer_id' => Schema::TYPE_STRING .'(12) NOT NULL',
          'referred_by' => Schema::TYPE_STRING.'(12) NOT NULL',
          'estimate' => Schema::TYPE_INTEGER.' NOT NULL',
          'month' => Schema::TYPE_SMALLINT.' NOT NULL',
          'year' => Schema::TYPE_SMALLINT.' NOT NULL',
          'monthyear' => Schema::TYPE_STRING . '(10) NOT NULL',
          'daystamp' => Schema::TYPE_INTEGER.' NOT NULL',
          'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
          'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
      ], $tableOptions);
      $this->addForeignKey('fk_impeachment_user', '{{%impeachment}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
  }

  public function down()
  {
      $this->dropForeignKey('fk_impeachment_user', '{{%impeachment}}');
      $this->dropTable('{{%impeachment}}');
  }
}
