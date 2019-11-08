<?php

use yii\db\Migration;

/**
 * Class m180921_031110_alter_conversations_table
 */
class m180921_031110_alter_conversations_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%conversations}}', 'id_customer', $this->integer()->defaultValue(0));
    }
    
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
    
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180921_031110_alter_conversations_table cannot be reverted.\n";

        return false;
    }
    */
}
