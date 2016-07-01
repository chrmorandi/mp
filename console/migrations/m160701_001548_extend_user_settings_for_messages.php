<?php

use yii\db\Schema;
use yii\db\Migration;

class m160701_001548_extend_user_settings_for_messages extends Migration
{
  public function up()
  {
    $tableOptions = null;
    if ($this->db->driverName === 'mysql') {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    }
    $this->addColumn('{{%user_setting}}','no_newsletter',Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0');
    $this->addColumn('{{%user_setting}}','no_updates',Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0');
  }

  public function down()
  {
    $this->dropColumn('{{%user_setting}}','no_newsletter');
    $this->dropColumn('{{%user_setting}}','no_updates');
  }
}
