<?php
use yii\db\Schema;
use yii\db\Migration;

class m160719_230252_extend_user_contact_for_sms extends Migration
{
  public function up()
  {
    $tableOptions = null;
    if ($this->db->driverName === 'mysql') {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    }
    $this->addColumn('{{%user_contact}}','accept_sms',Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0');
  }

  public function down()
  {
    $this->dropColumn('{{%user_contact}}','accept_sms');
  }
}
