<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%social_hook_request}}`.
 */
class m190901_141927_create_social_hook_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%social_hook_request}}', [
            'id_social_hook_request' => $this->primaryKey(),
            'data'                   => 'longtext',
            'status'                 => $this->tinyInteger()->defaultValue(0),
            'created_at'             => $this->integer(),
        ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%social_hook_request}}');
    }
}
