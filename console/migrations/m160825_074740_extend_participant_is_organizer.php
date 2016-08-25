<?php

use yii\db\Schema;
use yii\db\Migration;

class m160825_074740_extend_participant_is_organizer extends Migration
{
  public function up()
  {
    $tableOptions = null;
    if ($this->db->driverName === 'mysql') {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    }
    $this->addColumn('{{%participant}}','is_organizer',Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0');
  }

  public function down()
  {
    $this->dropColumn('{{%meeting_time}}','is_organizer');
  }
}
