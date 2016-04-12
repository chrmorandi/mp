<?php

use yii\db\Schema;
use yii\db\Migration;

class m160409_204159_extend_meeting_table_add_subject extends Migration
{
  public function up()
  {
    $tableOptions = null;
    if ($this->db->driverName === 'mysql') {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    }

    $this->addColumn('{{%meeting}}','subject','string NOT NULL');
  }

  public function down()
  {
    $this->dropColumn('{{%meeting}}','subject');
  }
}
