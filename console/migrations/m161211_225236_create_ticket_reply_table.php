<?php
use yii\db\Schema;
use yii\db\Migration;

class m161211_225236_create_ticket_reply_table extends Migration {
  public function up()
  {
      $tableOptions = null;
      if ($this->db->driverName === 'mysql') {
          $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
      }

      $this->createTable('{{%ticket_reply}}', [
          'id' => Schema::TYPE_PK,
          'ticket_id' => Schema::TYPE_INTEGER.' NOT NULL',
          'posted_by' => Schema::TYPE_STRING.' NOT NULL',
          'reply' => Schema::TYPE_TEXT.' NOT NULL DEFAULT ""',
          'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
          'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
      ], $tableOptions);
      $this->addForeignKey('fk_ticket_reply_ticket_id', '{{%ticket_reply}}', 'ticket_id', '{{%ticket}}', 'id', 'CASCADE', 'CASCADE');
  }

  public function down()
  {
    $this->dropForeignKey('fk_ticket_reply_ticket_id', '{{%ticket_reply}}');
    $this->dropTable('{{%ticket_reply}}');
  }
}
