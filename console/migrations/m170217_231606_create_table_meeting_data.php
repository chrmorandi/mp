<?php

use yii\db\Schema;
use yii\db\Migration;

class m170217_231606_create_table_meeting_data extends Migration
{
  public function up()
  {
      $tableOptions = null;
      if ($this->db->driverName === 'mysql') {
          $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
      }

      $this->createTable('{{%meeting_data}}', [
          'id' => Schema::TYPE_PK,
          'meeting_id' => Schema::TYPE_INTEGER.' NOT NULL',
          'owner_id' => Schema::TYPE_BIGINT.' NOT NULL',
          'owner_tz' => Schema::TYPE_STRING.' NOT NULL',
          'status' => Schema::TYPE_SMALLINT.' NOT NULL',
          'is_activity' => Schema::TYPE_SMALLINT.' NOT NULL',
          'count_activities' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
          'count_places' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
          'count_participants' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
          'count_times' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
          'chosen_time' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
          'chosen_place_id' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
          'chosen_activity' => Schema::TYPE_STRING.' NOT NULL',
          'hour' => Schema::TYPE_SMALLINT.' NOT NULL',
          'dayweek' => Schema::TYPE_SMALLINT.' NOT NULL',
          'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
          'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
      ], $tableOptions);
      $this->addForeignKey('fk_meeting_data_meeting', '{{%meeting_data}}', 'meeting_id', '{{%meeting}}', 'id', 'CASCADE', 'CASCADE');
  }

  public function down()
  {
      $this->dropForeignKey('fk_meeting_data_meeting', '{{%meeting_data}}');
      $this->dropTable('{{%meeting_data}}');
  }
}
