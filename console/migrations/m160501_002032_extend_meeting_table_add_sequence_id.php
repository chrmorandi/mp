<?php
use yii\db\Schema;
use yii\db\Migration;

class m160501_002032_extend_meeting_table_add_sequence_id extends Migration
{
    public function up()
    {
      $tableOptions = null;
      if ($this->db->driverName === 'mysql') {
          $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
      }
      $this->addColumn('{{%meeting}}','sequence_id',Schema::TYPE_STRING.' NOT NULL');
    }

    public function down()
    {
      $this->dropColumn('{{%meeting}}','sequence_id');
    }
}
