<?php

use yii\db\Schema;
use yii\db\Migration;

class m160401_210852_extend_user_setting_table extends Migration
{
  public function up()
  {
    $tableOptions = null;
    if ($this->db->driverName === 'mysql') {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    }    
    
    $this->addColumn('{{%user_setting}}','participant_add_place',Schema::TYPE_SMALLINT.' NOT NULL');
    $this->addColumn('{{%user_setting}}','participant_add_date_time',Schema::TYPE_SMALLINT.' NOT NULL');
    $this->addColumn('{{%user_setting}}','participant_choose_place',Schema::TYPE_SMALLINT.' NOT NULL');
    $this->addColumn('{{%user_setting}}','participant_choose_date_time',Schema::TYPE_SMALLINT.' NOT NULL');
    $this->addColumn('{{%user_setting}}','participant_finalize',Schema::TYPE_SMALLINT.' NOT NULL');
  }

  public function down()
  {
    $this->dropColumn('{{%user_setting}}','participant_finalize');
    $this->dropColumn('{{%user_setting}}','participant_choose_date_time');
    $this->dropColumn('{{%user_setting}}','participant_choose_place');
    $this->dropColumn('{{%user_setting}}','participant_add_date_time');
    $this->dropColumn('{{%user_setting}}','participant_add_place');
  }
}
