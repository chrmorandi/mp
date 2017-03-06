<?php
use yii\db\Schema;
use yii\db\Migration;
use frontend\models\UserSetting;

class m170305_223420_extend_user_settings_for_guide extends Migration
{
  public function up()
  {
    $tableOptions = null;
    if ($this->db->driverName === 'mysql') {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    }
    $this->addColumn('{{%user_setting}}','guide',Schema::TYPE_SMALLINT.' NOT NULL DEFAULT '.UserSetting::SETTING_ON);
    $all = UserSetting::find()->all();
    foreach ($all as $us) {
      $us->guide = UserSetting::SETTING_ON;
      $us->update();
    }
  }

  public function down()
  {
    $this->dropColumn('{{%user_setting}}','guide');
  }
}
