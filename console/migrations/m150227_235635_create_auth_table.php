<?php

use yii\db\Schema;
use yii\db\Migration;

class m150227_235635_create_auth_table extends Migration
{
   public function up()
   {
       $tableOptions = null;
       if ($this->db->driverName === 'mysql') {
           $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
       }

       $this->createTable('{{%auth}}', [
           'id' => Schema::TYPE_PK,
           'user_id' => Schema::TYPE_BIGINT.' NOT NULL',
           'source' => Schema::TYPE_STRING.' NOT NULL',
           'source_id' => Schema::TYPE_STRING.' NOT NULL',
       ], $tableOptions);
       $this->addForeignKey('fk-auth-user_id-user-id', '{{%auth}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
   }

   public function down()
   {
     $this->dropForeignKey('fk-auth-user_id-user-id', '{{%auth}}');
     $this->dropTable('{{%auth}}');
   }
}
