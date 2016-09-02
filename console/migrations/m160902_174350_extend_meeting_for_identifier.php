<?php

use yii\db\Schema;
use yii\db\Migration;
use frontend\models\Meeting;
class m160902_174350_extend_meeting_for_identifier extends Migration
{
  public function up()
  {
    $tableOptions = null;
    if ($this->db->driverName === 'mysql') {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    }
    $this->addColumn('{{%meeting}}','identifier',Schema::TYPE_STRING.' NOT NULL');
    $all = Meeting::find()
      ->where(['identifier'=>''])
      ->all();
    foreach ($all as $m) {
      $m->identifier = Yii::$app->security->generateRandomString(8);
      $m->update();
    }
  }

  public function down()
  {
    $this->dropColumn('{{%meeting}}','identifier');
  }
}
