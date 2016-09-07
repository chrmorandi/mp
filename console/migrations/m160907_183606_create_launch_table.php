<?php

use yii\db\Schema;
use yii\db\Migration;
/**
 * Handles the creation for table `launch`.
 */
class m160907_183606_create_launch_table extends Migration{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%launch}}', [
            'id' => Schema::TYPE_PK,
            'email' => Schema::TYPE_STRING.' NOT NULL DEFAULT 0',
            'ip_addr' => Schema::TYPE_STRING.' NOT NULL DEFAULT 0',
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%launch}}');
    }
}
