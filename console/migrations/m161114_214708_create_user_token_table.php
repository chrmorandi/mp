<?php
  use yii\db\Schema;
  use yii\db\Migration;

  /**
   * Handles the creation for table `user_token_table`.
   */
   class m161114_214708_create_user_token_table extends Migration
   {
      /**
       * @inheritdoc
       */
      public function up()
      {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

          $this->createTable('{{%user_token}}', [
              'id' => Schema::TYPE_PK,
              'user_id' => Schema::TYPE_BIGINT.' NOT NULL',
              'token' => Schema::TYPE_STRING.' NOT NULL DEFAULT 0',
              'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
              'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
          ], $tableOptions);
          $this->addForeignKey('fk_user_token_user_id', '{{%user_token}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
      }

      /**
       * @inheritdoc
       */
      public function down()
      {
        $this->dropForeignKey('fk_user_token_user_id', '{{%user_token}}');
        $this->dropTable('{{%user_token}}');
      }
    }
