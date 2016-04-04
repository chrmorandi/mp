<?php

use yii\db\Schema;
use yii\db\Migration;

class m160401_203412_meeting_setting_table extends Migration
{
  public function up()
   {
       $tableOptions = null;
       if ($this->db->driverName === 'mysql') {
           $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
       }

       $this->createTable('{{%meeting_setting}}', [
           'id' => Schema::TYPE_PK,
           'meeting_id' => Schema::TYPE_INTEGER.' NOT NULL',
           'participant_add_place' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
           'participant_add_date_time' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
           'participant_choose_place' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
           'participant_choose_date_time' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
           'participant_finalize' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
           'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
           'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
       ], $tableOptions);
       $this->addForeignKey('fk_meeting_setting', '{{%meeting_setting}}', 'meeting_id', '{{%meeting}}', 'id', 'CASCADE', 'CASCADE');
   }

   public function down()
   {
      $this->dropForeignKey('fk_meeting_setting', '{{%meeting_setting}}');    
      $this->dropTable('{{%meeting_setting}}');
   }
}
