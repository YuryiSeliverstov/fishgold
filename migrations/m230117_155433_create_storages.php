<?php

use yii\db\Migration;

class m230117_155433_create_storages extends Migration
{
    private string $tableName = '{{%storages}}';
    private string $indexName = 'storages-';
	
    private $tableOptions = null;

    private $indexes=[
            'name',
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
            'id' 			=> 	$this->primaryKey(),
            'name' 			=> 	$this->string(255),
			'description' 	=> 	$this->text(),
            'createdAt' 	=> 	$this->bigInteger(),
            'updatedAt' 	=> 	$this->bigInteger(),
        ],$this->tableOptions);
		
		foreach ($this->indexes as $k=>$field)
		{
			$this->createIndex($this->indexName.$field, $this->tableName, $field);
		}
    }
    public function safeDown()
    {
		foreach ($this->indexes as $k=>$field)
		{
			$this->dropIndex($this->indexName.$field, $this->tableName);
		}
        $this->dropTable($this->tableName);
    }
}
