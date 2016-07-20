<?php
use yii\db\Schema;
use yii\db\Migration;

class m160720_192941_extend_template_table extends Migration
{
  public function up()
  {
    $tableOptions = null;
    if ($this->db->driverName === 'mysql') {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
    }
    $this->addColumn('{{%template}}','subject',Schema::TYPE_STRING.' NOT NULL');
  }

  public function down()
  {
    $this->dropColumn('{{%template}}','subject');
  }
}
