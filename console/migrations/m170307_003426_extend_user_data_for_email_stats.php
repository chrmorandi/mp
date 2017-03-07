<?php
use yii\db\Schema;
use yii\db\Migration;
use frontend\models\UserSetting;

class m170307_003426_extend_user_data_for_email_stats extends Migration
{
  public function up()
  {
    $tableOptions = null;
    if ($this->db->driverName === 'mysql') {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    }
    $this->addColumn('{{%user_data}}','domain',Schema::TYPE_STRING.' NOT NULL');
    $this->addColumn('{{%user_data}}','domain_ext',Schema::TYPE_STRING.' NOT NULL');
  }

  public function down()
  {
    $this->dropColumn('{{%user_data}}','domain');
    $this->dropColumn('{{%user_data}}','domain_ext');
  }
}
