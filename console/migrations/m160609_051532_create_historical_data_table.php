<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Handles the creation for table `historical_data_table`.
 */
class m160609_051532_create_historical_data_table extends Migration
{
  public function up()
  {
    $tableOptions = null;
    if ($this->db->driverName === 'mysql') {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    }

      $this->createTable('{{%historical_data}}', [
          'id' => Schema::TYPE_PK,
          'date'=> Schema::TYPE_INTEGER.' NOT NULL',
          'percent_own_meeting'  => Schema::TYPE_FLOAT.' NOT NULL',
          'percent_own_meeting_last30'  => Schema::TYPE_FLOAT.' NOT NULL',
          // % of users invited by others who own a meeting
          'percent_invited_own_meeting'  => Schema::TYPE_FLOAT.' NOT NULL',
          'percent_participant'  => Schema::TYPE_FLOAT.' NOT NULL',
          'percent_participant_last30'  => Schema::TYPE_FLOAT.' NOT NULL',
          'count_users' => Schema::TYPE_INTEGER.' NOT NULL',
          'count_meetings_completed' => Schema::TYPE_INTEGER.' NOT NULL',
          'count_meetings_planning' => Schema::TYPE_INTEGER.' NOT NULL',
          'count_places' => Schema::TYPE_INTEGER.' NOT NULL',
          'average_meetings'  => Schema::TYPE_FLOAT.' NOT NULL',
          'average_friends'  => Schema::TYPE_FLOAT.' NOT NULL',
          'average_places'  => Schema::TYPE_FLOAT.' NOT NULL',
          'source_google' => Schema::TYPE_INTEGER.' NOT NULL',
          'source_facebook' => Schema::TYPE_INTEGER.' NOT NULL',
          'source_linkedin' => Schema::TYPE_INTEGER.' NOT NULL',
      ], $tableOptions);
      //$this->addForeignKey('fk_historical_data_user_id', '{{%historical_data}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
  }

  /**
   * @inheritdoc
   */
  public function down()
  {
    //$this->dropForeignKey('fk_historical_data_user_id', '{{%historical_data}}');
    $this->dropTable('{{%historical_data}}');
  }
}
