
<?php
use yii\db\Schema;
use yii\db\Migration;

class m161212_234657_extend_user_contact_for_verify extends Migration
{
  public function up()
  {
    $tableOptions = null;
    if ($this->db->driverName === 'mysql') {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    }
    $this->addColumn('{{%user_contact}}','request_count',Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0');
    $this->addColumn('{{%user_contact}}','verify_code',Schema::TYPE_INTEGER.' NOT NULL');
    $this->addColumn('{{%user_contact}}','requested_at',Schema::TYPE_INTEGER.' NOT NULL');
  }

  public function down()
  {
    $this->dropColumn('{{%user_contact}}','requested_at');
    $this->dropColumn('{{%user_contact}}','verify_code');
    $this->dropColumn('{{%user_contact}}','request_count');
  }
}
