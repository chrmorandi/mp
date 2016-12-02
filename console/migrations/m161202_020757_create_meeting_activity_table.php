<?php
use yii\db\Schema;
use yii\db\Migration;

class m161202_020757_create_meeting_activity_table extends Migration
{
  public function up()
  {
      $tableOptions = null;
      if ($this->db->driverName === 'mysql') {
          $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
      }

      $this->createTable('{{%meeting_activity}}', [
          'id' => Schema::TYPE_PK,
          'meeting_id' => Schema::TYPE_INTEGER.' NOT NULL',
          'activity' => Schema::TYPE_STRING.' NOT NULL',          
          'suggested_by' => Schema::TYPE_BIGINT.' NOT NULL',
          'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
          'availability' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
          'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
          'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
      ], $tableOptions);
      $this->addForeignKey('fk_meeting_activity_meeting', '{{%meeting_activity}}', 'meeting_id', '{{%meeting}}', 'id', 'CASCADE', 'CASCADE');
      $this->addForeignKey('fk_activity_suggested_by', '{{%meeting_activity}}', 'suggested_by', '{{%user}}', 'id', 'CASCADE', 'CASCADE');

  }

  public function down()
  {
    $this->dropForeignKey('fk_activity_suggested_by', '{{%meeting_activity}}');
    $this->dropForeignKey('fk_meeting_activity_meeting', '{{%meeting_activity}}');
      $this->dropTable('{{%meeting_activity}}');
  }
}
