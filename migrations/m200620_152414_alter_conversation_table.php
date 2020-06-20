<?php

use yii\db\Migration;

/**
 * Class m200620_152414_alter_conversation_table
 */
class m200620_152414_alter_conversation_table extends Migration{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp(){
		$this->addColumn('{{%conversations}}','email',$this->string());
		$this->addColumn('{{%conversations}}','phone',$this->string());
		$this->addColumn('{{%conversations}}','last_msg_at',$this->integer());
		
		$this->execute("UPDATE conversations set last_msg_at = updated_at");
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function safeDown(){
		$this->dropColumn('{{%conversations}}','email');
		$this->dropColumn('{{%conversations}}','phone');
		$this->dropColumn('{{%conversations}}','last_msg_at');
		
		return true;
	}
	
	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
		echo "m200620_152414_alter_conversation_table cannot be reverted.\n";

		return false;
	}
	*/
}
