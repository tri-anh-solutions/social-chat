<?php

use yii\db\Migration;

/**
 * Class m200623_062706_alter_conversation_detail_table
 */
class m200623_062706_alter_conversation_detail_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->alterColumn('{{%conversation_details}}','content',$this->text()->append('CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200623_062706_alter_conversation_detail_table cannot be reverted.\n";

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200623_062706_alter_conversation_detail_table cannot be reverted.\n";

        return false;
    }
    */
}
