<?php

use yii\db\Migration;

class m230117_155431_create_users extends Migration
{
    private string $tableName = '{{%users}}';
    private string $indexName = 'users-';
	
	private $tableOptions = null;
	
	private $indexes=[
			'email',
			'password',
			'active',
			'deleted',
			'createdAt',
			'updatedAt',
			'loggedAt'
	];
	
    public function safeUp()
    {
		if ($this->db->driverName === 'mysql') 
		{
            $this->tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
		
        $this->createTable($this->tableName, [
            'id' 					=> $this->primaryKey(),
            'email' 				=> $this->string(255)->unique(),
			'emailCode' 			=> $this->string(255),
            'firstName' 			=> $this->string(255),
            'lastName' 				=> $this->string(255),
            'secondName' 			=> $this->string(255),
            'nickName' 				=> $this->string(255),
            'password' 				=> $this->string(255),
            'dateOfBirth' 			=> $this->string(255),
            'gender' 				=> $this->tinyInteger(1),
            'fcmToken' 				=> $this->string(255),
            'createdAt' 			=> $this->bigInteger(),
            'updatedAt'	 			=> $this->bigInteger(),
            'loggedAt' 				=> $this->bigInteger(),
            'active' 				=> $this->tinyInteger(1),
			'deleted' 				=> $this->tinyInteger(1)
        ],$this->tableOptions);
		
		foreach ($this->indexes as $k=>$v)
		{
			$this->createIndex($this->indexName.$v, $this->tableName, $v);
		}
    }

    public function safeDown()
    {
		foreach ($this->indexes as $k=>$v)
		{
			$this->dropIndex($this->indexName.$v, $this->tableName);
		}
		
        $this->dropTable($this->tableName);
    }
}
