<?php
use yii\db\Schema;
use yii\db\Migration;

class m160813_012323_create_request_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%request}}', [
            'id' => Schema::TYPE_PK,
            'meeting_id' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
            'requestor_id' => Schema::TYPE_BIGINT.' NOT NULL DEFAULT 0',
            'completed_by' => Schema::TYPE_BIGINT.' NOT NULL DEFAULT 0',
            'time_adjustment' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
            'alternate_time' => Schema::TYPE_BIGINT.' NOT NULL DEFAULT 0',
            'meeting_time_id' => Schema::TYPE_BIGINT.' NOT NULL DEFAULT 0',
            'place_adjustment' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
            'meeting_place_id' => Schema::TYPE_BIGINT.' NOT NULL DEFAULT 0',
            'note' => Schema::TYPE_TEXT.' NOT NULL DEFAULT ""',
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
        $this->addForeignKey('fk_request_meeting', '{{%request}}', 'meeting_id', '{{%meeting}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_request_user', '{{%request}}', 'requestor_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
 	  	$this->dropForeignKey('fk_request_meeting', '{{%request}}');
      $this->dropForeignKey('fk_request_user', '{{%request}}');
      $this->dropTable('{{%request}}');
    }
}
