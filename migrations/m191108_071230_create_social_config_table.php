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
			'type'             => $this->string()->notNull(),
			'key'              => $this->string()->notNull(),
			'value'            => $this->text(),
			'created_at'       => $this->integer(),
			'updated_at'       => $this->integer(),
		]);
		
		$this->createIndex('social_config_unique','{{%social_config}}',['type','key'],true);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function safeDown(){
		$this->dropTable('{{%social_config}}');
	}
}
