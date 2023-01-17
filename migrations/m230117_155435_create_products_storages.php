<?php

use yii\db\Migration;

class m230117_155435_create_products_storages extends Migration
{
    private string $tableName = '{{%products_storages}}';
    private string $indexName = 'products-storages-';
	
    private $tableOptions = null;

    private $indexes=[
            'price',
			'quantity',
			'createdAt',
			'updatedAt',
			'storageId',
			'productId'
    ];
	
	private $fkKeys=[
		'storageId'	=>	'storages',
		'productId'	=>	'products'
	];

    public function safeUp()
    {
        if ($this->db->driverName === 'mysql') 
		{
            $this->tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
		
        $this->createTable($this->tableName, [
            'id' 			=> 	$this->primaryKey(),
			'storageId'		=>	$this->integer(),
			'productId'		=>	$this->integer(),
			'quantity'		=>	$this->integer(),
			'price'			=>	$this->bigInteger(),
            'createdAt' 	=> 	$this->bigInteger(),
            'updatedAt' 	=> 	$this->bigInteger(),
        ],$this->tableOptions);
		
		foreach ($this->indexes as $k=>$field)
		{
			$this->createIndex($this->indexName.$field, $this->tableName, $field);
		}
		
		foreach ($this->fkKeys as $field=>$table)
		{
			$this->addForeignKey(
				'fk-' . $this->indexName . $field,
				$this->tableName,
				$field,
				'{{%'.$table.'}}',
				'id',
				'CASCADE'
			);
		}
    }
    public function safeDown()
    {
		foreach ($this->fkKeys as $field=>$table)
		{
			$this->dropForeignKey('fk-'.$this->indexName.$field, $this->tableName);
		}
		
		foreach ($this->indexes as $k=>$field)
		{
			$this->dropIndex($this->indexName.$field, $this->tableName);
		}
        $this->dropTable($this->tableName);
    }
}
