<?php

use yii\db\Migration;

/**
 * Handles the creation of table `social_config`.
 */
class m191108_071230_create_social_config_table extends Migration{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp(){
		$this->createTable('{{%social_config}}',[
			'id_social_config' => $this->primaryKey(),
			'key'              => $this->string()->notNull()->unique(),
			'value'            => $this->text(),
			'created_at'       => $this->integer(),
			'updated_at'       => $this->integer(),
		]);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function safeDown(){
		$this->dropTable('{{%social_config}}');
	}
}
