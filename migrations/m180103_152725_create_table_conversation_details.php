<?php

use yii\db\Migration;

/**
 * Class m180103_152725_create_table_conversation_details
 */
class m180103_152725_create_table_conversation_details extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%conversation_details}}', [
            'conversation_detail_id' => $this->primaryKey(),
            'conversation_id'        => $this->integer(),
            'msg_id'                 => $this->string(),
            'sender_id'              => $this->string(),
            'user_id'                => $this->integer(),
            'content'                => $this->text(),
            'type'                   => $this->string(),
            'created_at'             => $this->integer(),
            'created_time'           => $this->integer(),
        ], $tableOptions);
    }
    
    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%conversation_details}}');
        
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
