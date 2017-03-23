<?php
use yii\db\Schema;
use yii\db\Migration;

class m170311_015904_domain_table extends Migration
{
  public function up()
  {
      $tableOptions = null;
      if ($this->db->driverName === 'mysql') {
          $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
      }

      $this->createTable('{{%domain}}', [
          'id' => Schema::TYPE_PK,
          'domain' => Schema::TYPE_STRING.' NOT NULL',
          'level' => Schema::TYPE_SMALLINT.' NOT NULL',
          'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
          'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
      ], $tableOptions);
  }

  public function down()
  {
      $this->dropTable('{{%domain}}');
  }
}
?>
