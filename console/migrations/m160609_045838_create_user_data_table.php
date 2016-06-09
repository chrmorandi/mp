<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Handles the creation for table `user_data_table`.
 */
class m160609_045838_create_user_data_table extends Migration
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

        $this->createTable('{{%user_data}}', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_BIGINT.' NOT NULL',
            'is_social' => Schema::TYPE_SMALLINT.' NOT NULL',
            'invite_then_own' => Schema::TYPE_SMALLINT.' NOT NULL',
            'count_meetings' => Schema::TYPE_INTEGER.' NOT NULL',
            'count_meetings_last30' => Schema::TYPE_INTEGER.' NOT NULL',
            'count_meeting_participant' => Schema::TYPE_INTEGER.' NOT NULL',
            'count_meeting_participant_last30' => Schema::TYPE_INTEGER.' NOT NULL',
            'count_places' => Schema::TYPE_INTEGER.' NOT NULL',
            'count_friends' => Schema::TYPE_INTEGER.' NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
        $this->addForeignKey('fk_user_data_user_id', '{{%user_data}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
      $this->dropForeignKey('fk_user_data_user_id', '{{%user_data}}');
      $this->dropTable('{{%user_data}}');
    }
  }
