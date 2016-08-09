<?php
  use yii\db\Schema;
  use yii\db\Migration;

  class m160809_212621_extend_user_settings_for_timezone_check extends Migration
  {
      public function up()
      {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->addColumn('{{%user_setting}}','has_updated_timezone',Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0');
      }

      public function down()
      {
        $this->dropColumn('{{%user_setting}}','has_updated_timezone');
      }
  }
