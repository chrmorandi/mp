<?php

use yii\db\Schema;
use yii\db\Migration;

class m160824_235517_extend_meeting_place_and_time extends Migration
{
  public function up()
  {
    $tableOptions = null;
    if ($this->db->driverName === 'mysql') {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    }
    $this->addColumn('{{%meeting_time}}','availability',Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0');
    $this->addColumn('{{%meeting_place}}','availability',Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0');
  }

  public function down()
  {
    $this->dropColumn('{{%meeting_time}}','availability');
    $this->dropColumn('{{%meeting_place}}','availability');
  }
}
