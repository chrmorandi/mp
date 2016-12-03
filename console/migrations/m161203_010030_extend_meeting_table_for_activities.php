  <?php

  use yii\db\Schema;
  use yii\db\Migration;

class m161203_010030_extend_meeting_table_for_activities extends Migration
  {
    public function up()
    {
      $tableOptions = null;
      if ($this->db->driverName === 'mysql') {
          $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
      }

      $this->addColumn('{{%meeting}}','is_activity',Schema::TYPE_SMALLINT.' NOT NULL');
    }

    public function down()
    {
      $this->dropColumn('{{%meeting}}','is_activity');      
    }
  }
