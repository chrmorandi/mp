<?php
use yii\db\Schema;
use yii\db\Migration;

class m160904_001244_create_table_address extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%address}}', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_BIGINT.' NOT NULL',
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
            'firstname' =>Schema::TYPE_STRING.' NOT NULL',
            'lastname' =>Schema::TYPE_STRING.' NOT NULL',
            'fullname' =>Schema::TYPE_STRING.' NOT NULL',
            'email' =>Schema::TYPE_STRING.' NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
        $this->addForeignKey('fk_address_user_id', '{{%address}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
      $this->dropForeignKey('fk_address_user_id', '{{%address}}');
      $this->dropTable('{{%address}}');
    }
  }
