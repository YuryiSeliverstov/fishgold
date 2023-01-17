<?php

namespace app\models;

use Swagger\Annotations as SWG;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @SWG\Definition()
 * @SWG\Property(property="productId", type="integer")
 * @SWG\Property(property="storageId", type="integer")
 * @SWG\Property(property="quantity", type="integer")
 * @SWG\Property(property="price", type="integer")
 */
class ProductsStorages extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'products_storages';
    }
	
    public function rules(): array
    {
        return [
            [['productId', 'storageId'], 'integer'],
            [['quantity','price'], 'integer', 'min' => 0],
            [['productId', 'storageId','quantity','price'], 'required'],
            [
                ['productId'],
                'exist',
                'skipOnError' => false,
                'targetClass' => Products::class,
                'targetAttribute' => ['productId' => 'id']
            ],
            [
                ['storageId'],
                'exist',
                'skipOnError' => false,
                'targetClass' => Storages::class,
                'targetAttribute' => ['storageId' => 'id']
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'productId' => 'Product Id',
            'storageId' => 'Storage Id',
            'quantity' 	=> 'Количество',
        ];
    }

    public function getProduct(): ActiveQuery
    {
        return $this->hasOne(Products::class, ['id' => 'productId']);
    }

    public function getStorage(): ActiveQuery
    {
        return $this->hasOne(Storages::class, ['id' => 'storageId']);
    }
}
