<?php
use yii\db\Schema;
use yii\db\Migration;

/**
 * Handles the creation for table `mailgun_notification_table`.
 */
class m160514_010650_create_mailgun_notification_table extends Migration
{
  public function up()
  {
      $tableOptions = null;
      if ($this->db->driverName === 'mysql') {
          $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
      }

      $this->createTable('{{%mailgun_notification}}', [
          'id' => Schema::TYPE_PK,
          'url' => Schema::TYPE_STRING.' NOT NULL',
          'status' => Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0',
          'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
          'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
      ], $tableOptions);
  }

  public function down()
  {
    $this->dropTable('{{%mailgun_notification}}');
  }
}
