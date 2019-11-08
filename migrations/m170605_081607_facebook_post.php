<?php

use yii\db\Migration;

class m170605_081607_facebook_post extends Migration{
	public function up(){
		$this->createTable('facebook_post',[
			'facebook_post_id' => $this->primaryKey(),
			'message'          => $this->text(),
			'from_name'        => $this->string(),
			'from_id'          => $this->string(),
			'created_time'     => $this->dateTime(),
			'updated_time'     => $this->dateTime(),
			'post_id'          => $this->string(),
		]);
	}
	
	public function down(){
		$this->dropTable('facebook_post');
	}
}
