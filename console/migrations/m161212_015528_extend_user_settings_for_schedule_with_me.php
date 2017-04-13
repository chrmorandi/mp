<?php
use yii\db\Schema;
use yii\db\Migration;
use frontend\models\UserSetting;

class m161212_015528_extend_user_settings_for_schedule_with_me extends Migration
{
  public function up()
  {
    $tableOptions = null;
    if ($this->db->driverName === 'mysql') {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    }
    $this->addColumn('{{%user_setting}}','schedule_with_me',Schema::TYPE_SMALLINT.' NOT NULL DEFAULT '.UserSetting::SETTING_ON);
    /*
    for past installations, this code turned on the setting for prior users
    but it doesn't work after the guide property has been added to user_setting
    needs to be rewritten */
    /*
    $all = UserSetting::find()->all();
    foreach ($all as $us) {
      $us->schedule_with_me = UserSetting::SETTING_ON;
      $us->update();
    }*/
  }

  public function down()
  {
    $this->dropColumn('{{%user_setting}}','schedule_with_me');
  }
}
