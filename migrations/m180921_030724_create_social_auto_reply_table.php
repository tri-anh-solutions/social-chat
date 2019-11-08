<?php

use yii\db\Migration;

/**
 * Handles the creation of table `social_auto_reply`.
 */
class m180921_030724_create_social_auto_reply_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('social_auto_reply', [
            'id_social_auto_reply' => $this->primaryKey(),
            'title'                => $this->string(),
            'message'              => $this->string(),
            'reply_content'        => $this->text(),
            'created_at'           => $this->integer(),
            'updated_at'           => $this->integer(),
        ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('social_auto_reply');
    }
}
