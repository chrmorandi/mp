<?php

use yii\db\Schema;
use yii\db\Migration;

class m160429_203441_extend_user_setting_table_add_timezone extends Migration
{
  public function up()
  {
    $tableOptions = null;
    if ($this->db->driverName === 'mysql') {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    }

    $this->addColumn('{{%user_setting}}','timezone',Schema::TYPE_STRING.' NOT NULL');
  }

  public function down()
  {
    $this->dropColumn('{{%user_setting}}','timezone');
  }
}
