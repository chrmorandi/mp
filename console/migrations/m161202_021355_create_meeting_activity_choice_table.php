<?php

use yii\db\Schema;
use yii\db\Migration;

class m161202_021355_create_meeting_activity_choice_table extends Migration
{
  public function up()
  {
      $tableOptions = null;
      if ($this->db->driverName === 'mysql') {
          $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
      }

      $this->createTable('{{%meeting_activity_choice}}', [
          'id' => Schema::TYPE_PK,
          'meeting_activity_id' => Schema::TYPE_INTEGER.' NOT NULL',
          'user_id' => Schema::TYPE_BIGINT.' NOT NULL',
          'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
          'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
          'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
      ], $tableOptions);
      $this->addForeignKey('fk_mac_meeting_activity', '{{%meeting_activity_choice}}', 'meeting_activity_id', '{{%meeting_activity}}', 'id', 'CASCADE', 'CASCADE');
      $this->addForeignKey('fk_mac_user_id', '{{%meeting_activity_choice}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');

  }

  public function down()
  {
    $this->dropForeignKey('fk_mac_user_id', '{{%meeting_activity_choice}}');
    $this->dropForeignKey('fk_mac_meeting_activity', '{{%meeting_activity_choice}}');
    $this->dropTable('{{%meeting_activity_choice}}');
  }
}
