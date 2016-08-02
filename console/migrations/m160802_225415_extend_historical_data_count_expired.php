<?php

use yii\db\Schema;
use yii\db\Migration;

class m160802_225415_extend_historical_data_count_expired extends Migration
{
    public function up()
    {
      $tableOptions = null;
      if ($this->db->driverName === 'mysql') {
          $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
      }
      $this->addColumn('{{%historical_data}}','count_meetings_expired',Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0');
    }

    public function down()
    {
      $this->dropColumn('{{%historical_data}}','count_meetings_expired');
    }
}
