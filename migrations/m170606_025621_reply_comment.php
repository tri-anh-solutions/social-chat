<?php

use yii\db\Migration;

class m170606_025621_reply_comment extends Migration
{
    public function up()
    {
        $this->createTable('facebook_reply_comment',[
            'facebook_reply_comment_id' => $this->primaryKey(),
            'facebook_comment_id'       => $this->integer()->notNull(),
            'name'                      => $this->string(),
            'id'                        => $this->string(),
            'comment_id'                => $this->string(),
            'reply_comment_id'          => $this->string(),
            'message'                   => $this->text(),
            'created_time'              => $this->dateTime(),
        ]);
    }
    
    public function down()
    {
        $this->dropTable('facebook_reply_comment');
    }
    
    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
