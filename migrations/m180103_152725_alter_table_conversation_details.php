<?php

use yii\db\Migration;

/**
 * Class m180103_152725_create_table_conversation_details
 */
class m180103_152725_alter_table_conversation_details extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%conversation_details}}', 'href', $this->string());
        $this->addColumn('{{%conversation_details}}', 'thumb', $this->text());
        $this->addColumn('{{%conversation_details}}', 'description', $this->text());
        $this->addColumn('{{%conversation_details}}', 'sticker_id', $this->string());
        $this->addColumn('{{%conversation_details}}', 'params', $this->text());
    }
    
    /**
     * @inheritdoc
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
        echo "m180103_152725_create_table_conversation_details cannot be reverted.\n";

        return false;
    }
    */
}
