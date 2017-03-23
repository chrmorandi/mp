<?php
use yii\db\Schema;
use yii\db\Migration;

class m170322_223948_extend_message_table_with_target extends Migration
{
  public function up()
  {
    $tableOptions = null;
    if ($this->db->driverName === 'mysql') {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    }
    $this->addColumn('{{%message}}','target',Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0');
  }

  public function down()
  {
    $this->dropColumn('{{%message}}','target');    
  }
}
