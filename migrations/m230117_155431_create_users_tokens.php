<?php

use yii\db\Migration;

class m230117_155431_create_users_tokens extends Migration
{
    private string $tableName = '{{%users_tokens}}';
    private string $indexName = 'users-tokens-';
	
    private $tableOptions = null;

    private $indexes=[
            'userId',
			'expiredAt',
			'createdAt',
			'updatedAt'
    ];

    public function safeUp()
    {
        if ($this->db->driverName === 'mysql') 
		{
            $this->tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
		
        $this->createTable($this->tableName, [
            'userId' 	=> $this->integer(),
            'token' 	=> $this->string(255)->unique(),
            'expiredAt' => $this->integer(),
            'createdAt' => $this->bigInteger(),
            'updatedAt' => $this->bigInteger(),
        ],$this->tableOptions);
		
		foreach ($this->indexes as $k=>$field)
		{
			$this->createIndex($this->indexName.$field, $this->tableName, $field);
		}
		
		$this->addForeignKey(
            'fk-' . $this->indexName . 'userId',
            $this->tableName,
            'userId',
            '{{%users}}',
            'id',
            'CASCADE'
        );
    }
    public function safeDown()
    {
		$this->dropForeignKey('fk-'.$this->indexName.'userId', $this->tableName);
		foreach ($this->indexes as $k=>$field)
		{
			$this->dropIndex($this->indexName.$field, $this->tableName);
		}
        $this->dropTable($this->tableName);
    }
}
