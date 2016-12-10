<?php
  use yii\db\Schema;
  use yii\db\Migration;
  use frontend\models\Meeting;
  class m161202_024352_extend_meeting_setting_table_for_activities extends Migration
  {
    public function up()
    {
      $tableOptions = null;
      if ($this->db->driverName === 'mysql') {
          $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
      }
      $this->addColumn('{{%meeting_setting}}','participant_add_activity',Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0');
      $this->addColumn('{{%meeting_setting}}','participant_choose_activity',Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0');
    }

    public function down()
    {
      $this->dropColumn('{{%meeting_setting}}','participant_add_activity');
      $this->dropColumn('{{%meeting_setting}}','participant_choose_activity');
    }
  }
