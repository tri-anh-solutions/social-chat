<?php

use yii\db\Migration;

class m170606_022726_facebook_comment extends Migration{
	public function up(){
		$this->createTable('facebook_comment',[
			'facebook_comment_id' => $this->primaryKey(),
			'comment_id'          => $this->string()->notNull(), // id của comment
			'post_id'             => $this->string()->notNull(), // id post
			'parent_id'           => $this->string(), // Parent comment
			'id'                  => $this->string(), //id comment của người
			'name'                => $this->string(),
			'message'             => $this->text(),
			'created_time'        => $this->dateTime(),
		]);
	}
	
	public function down(){
		$this->dropTable('facebook_comment');
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
