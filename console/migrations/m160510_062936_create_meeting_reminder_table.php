<?php
use yii\db\Schema;
use yii\db\Migration;

/**
 * Handles the creation for table `meeting_reminder_table`.
 */
class m160510_062936_create_meeting_reminder_table extends Migration
{

  public function up()
  {
      $tableOptions = null;
      if ($this->db->driverName === 'mysql') {
          $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
      }

      $this->createTable('{{%meeting_reminder}}', [
          'id' => Schema::TYPE_PK,
          'meeting_id' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
          'reminder_id' => Schema::TYPE_BIGINT.' NOT NULL',
          'user_id' => Schema::TYPE_BIGINT.' NOT NULL',
          'due_at' => Schema::TYPE_INTEGER . ' NOT NULL',
          'status' => Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0',
      ], $tableOptions);
      $this->addForeignKey('fk_meeting_reminder_user', '{{%meeting_reminder}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
      $this->addForeignKey('fk_meeting_reminder_meeting', '{{%meeting_reminder}}', 'meeting_id', '{{%meeting}}', 'id', 'CASCADE', 'CASCADE');
  }

  public function down()
  {
    $this->dropForeignKey('fk_meeting_reminder_user', '{{%meeting_reminder}}');
    $this->dropForeignKey('fk_meeting_reminder_meeting', '{{%meeting_reminder}}');
    $this->dropTable('{{%meeting_reminder}}');
  }

}
