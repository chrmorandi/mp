<?php

use yii\db\Schema;
use yii\db\Migration;

class m160420_200549_extend_meeting_table_add_logged_at extends Migration
{
  public function up()
  {
    $tableOptions = null;
    if ($this->db->driverName === 'mysql') {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    }

    $this->addColumn('{{%meeting}}','logged_at',Schema::TYPE_INTEGER . ' NOT NULL');
    $this->addColumn('{{%meeting}}','cleared_at',Schema::TYPE_INTEGER . ' NOT NULL');
  }

  public function down()
  {
    $this->dropColumn('{{%meeting}}','cleared_at');
    $this->dropColumn('{{%meeting}}','logged_at');
  }
}
