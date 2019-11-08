<?php

use yii\db\Migration;

/**
 * Class m180103_152339_create_table_conversations
 */
class m180103_152339_create_table_conversations extends Migration{
	/**
	 * @inheritdoc
	 */
	public function safeUp(){
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			// http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
		}
		
		$this->createTable('{{%conversations}}',[
			'conversation_id' => $this->primaryKey(),
			'sender_id'       => $this->string(),
			'sender_name'     => $this->string(),
			'receiver_id'     => $this->string(),
			'receiver_name'   => $this->string(),
			'type'            => $this->smallInteger(),
			'message_count'   => $this->integer(),
			'unread_count'    => $this->integer(),
			'created_at'      => $this->integer(),
			'updated_at'      => $this->integer(),
		],$tableOptions);
	}
	
	/**
	 * @inheritdoc
	 */
	public function safeDown(){
		$this->dropTable('{{%conversations}}');
		
		return true;
	}
	
	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
		echo "m180103_152339_create_table_conversations cannot be reverted.\n";

		return false;
	}
	*/
}
