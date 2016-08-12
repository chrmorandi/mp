<?php

use yii\db\Schema;
use yii\db\Migration;

class m160812_012823_extend_meeting_setting_make_changes extends Migration
{
  public function up()
  {
    $tableOptions = null;
    if ($this->db->driverName === 'mysql') {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    }
    $this->addColumn('{{%meeting_setting}}','participant_reopen',Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0');
    $this->addColumn('{{%meeting_setting}}','participant_request_change',Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0');
  }

  public function down()
  {
    $this->dropColumn('{{%meeting_setting}}','participant_reopen');
    $this->dropColumn('{{%meeting_setting}}','participant_request_change');
  }
}
