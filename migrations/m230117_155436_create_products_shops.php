<?php

use yii\db\Migration;

class m230117_155436_create_products_shops extends Migration
{
    private string $tableName = '{{%products_shops}}';
    private string $indexName = 'products-shops-';
	
    private $tableOptions = null;

    private $indexes=[
            'quantity',
			'price',
			'shopId',
			'productId',
			'createdAt',
			'updatedAt'
    ];
	
	private $fkKeys=[
		'shopId'	=>	'shops',
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
            'productId' 	=> 	$this->integer(),
			'shopId' 		=> 	$this->integer(),
			'quantity' 		=> 	$this->integer(),
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
