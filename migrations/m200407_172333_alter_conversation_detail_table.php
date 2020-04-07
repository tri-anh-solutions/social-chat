<?php

use yii\db\Migration;

/**
 * Class m200407_172333_alter_conversation_detail_table
 */
class m200407_172333_alter_conversation_detail_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%conversation_details}}','sender_name',$this->string());
	    
	    $this->execute('UPDATE conversation_details set sender_name = (SELECT username FROM `user` WHERE id = conversation_details.user_id)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
	    $this->dropColumn('{{%conversation_details}}','sender_name');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200407_172333_alter_conversation_detail_table cannot be reverted.\n";

        return false;
    }
    */
}
