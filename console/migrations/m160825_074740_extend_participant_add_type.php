<?php

use yii\db\Schema;
use yii\db\Migration;

class m160825_074740_extend_participant_add_type extends Migration
{
  public function up()
  {
    $tableOptions = null;
    if ($this->db->driverName === 'mysql') {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    }
    $this->addColumn('{{%participant}}','participant_type',Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0');
    $this->addColumn('{{%participant}}','notify',Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0');
  }

  public function down()
  {
    $this->dropColumn('{{%participant}}','participant_type');
    $this->dropColumn('{{%participant}}','notify');
  }
}
