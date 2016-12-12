
<?php
use yii\db\Schema;
use yii\db\Migration;

class m161211_224822_create_ticket_table extends Migration {
  public function up()
  {
      $tableOptions = null;
      if ($this->db->driverName === 'mysql') {
          $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
      }

      $this->createTable('{{%ticket}}', [
          'id' => Schema::TYPE_PK,
          'posted_by' => Schema::TYPE_STRING.' NOT NULL',
          'email' => Schema::TYPE_STRING.' NOT NULL',
          'subject' => Schema::TYPE_STRING.' NOT NULL',
          'details' => Schema::TYPE_TEXT.' NOT NULL DEFAULT ""',
          'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
          'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
          'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
      ], $tableOptions);
  }

  public function down()
  {
      $this->dropTable('{{%ticket}}');
  }
}
