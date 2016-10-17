<?php
  use yii\db\Schema;
  use yii\db\Migration;
  use frontend\models\Meeting;
  class m161016_204028_extend_user_and_meeting_for_simple extends Migration
  {
    public function up()
    {
      $tableOptions = null;
      if ($this->db->driverName === 'mysql') {
          $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
      }
      $this->addColumn('{{%meeting}}','site_id',Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0');
      $this->addColumn('{{%user}}','site_id',Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0');
    }

    public function down()
    {
      $this->dropColumn('{{%meeting}}','site_id');
      $this->dropColumn('{{%user}}','site_id');
    }
  }
