<?php

use yii\db\Schema;
use yii\db\Migration;

class m170406_034755_extend_user_setting_for_language extends Migration
{
  public function up()
  {
    $tableOptions = null;
    if ($this->db->driverName === 'mysql') {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    }

    $this->addColumn('{{%user_setting}}','language',Schema::TYPE_STRING.' NOT NULL DEFAULT "xx"');
  }

  public function down()
  {
    $this->dropColumn('{{%user_setting}}','language');
  }
}
