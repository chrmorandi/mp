<?php
use yii\db\Schema;
use yii\db\Migration;

class m160429_065853_extend_user_table_add_block_all extends Migration
{
  public function up()
  {
    $tableOptions = null;
    if ($this->db->driverName === 'mysql') {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    }

    $this->addColumn('{{%user}}','block_all',Schema::TYPE_SMALLINT.' NOT NULL');
  }

  public function down()
  {
    $this->dropColumn('{{%user}}','block_all');
  }
}
