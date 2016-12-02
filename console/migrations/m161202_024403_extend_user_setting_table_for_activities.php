<?php

use yii\db\Schema;
use yii\db\Migration;

class m161202_024403_extend_user_setting_table_for_activities extends Migration
{
  public function up()
  {
    $tableOptions = null;
    if ($this->db->driverName === 'mysql') {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    }

    $this->addColumn('{{%user_setting}}','participant_add_activity',Schema::TYPE_SMALLINT.' NOT NULL');
    $this->addColumn('{{%user_setting}}','participant_choose_activity',Schema::TYPE_SMALLINT.' NOT NULL');
  }

  public function down()
  {        
    $this->dropColumn('{{%user_setting}}','participant_add_activity');
    $this->dropColumn('{{%user_setting}}','participant_choose_activity');
  }
}
