<?php
use yii\db\Schema;
use yii\db\Migration;

class m160501_002825_extend_meeting_time_table_add_duration extends Migration
{
  public function up()
  {
    $tableOptions = null;
    if ($this->db->driverName === 'mysql') {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    }
    $this->addColumn('{{%meeting_time}}','duration',Schema::TYPE_INTEGER.' NOT NULL');
    $this->addColumn('{{%meeting_time}}','end',Schema::TYPE_INTEGER.' NOT NULL');
  }

  public function down()
  {
    $this->dropColumn('{{%meeting_time}}','end');
    $this->dropColumn('{{%meeting_time}}','duration');
  }
}
