<?php

use yii\db\Migration;

/**
 * Class m200407_172327_alter_conversation_table
 */
class m200407_172327_alter_conversation_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn('{{%conversations}}','locked_by',$this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
	    $this->dropColumn('{{%conversations}}','locked_by');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200407_172327_alter_conversation_table cannot be reverted.\n";

        return false;
    }
    */
}
