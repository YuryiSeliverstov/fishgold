<?php

namespace app\models;

use Swagger\Annotations as SWG;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @SWG\Definition()
 * @SWG\Property(property="productId", type="integer")
 * @SWG\Property(property="shopId", type="integer")
 * @SWG\Property(property="quantity", type="integer")
 * @SWG\Property(property="price", type="integer")
 */
class ProductsShops extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'products_shops';
    }

    public function rules(): array
    {
        return [
            [['productId', 'shopId'], 'integer'],
            [['quantity','price'], 'integer', 'min' => 0],
            [['productId', 'shopId','quantity','price'], 'required'],
            [
                ['productId'],
                'exist',
                'skipOnError' => false,
                'targetClass' => Products::class,
                'targetAttribute' => ['productId' => 'id']
            ],
            [
                ['shopId'],
                'exist',
                'skipOnError' => false,
                'targetClass' => Shops::class,
                'targetAttribute' => ['shopId' => 'id']
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'productId' => 'Product Id',
            'shopId' 	=> 'Shop Id',
            'quantity' 	=> 'Количество',
        ];
    }

    public function getProduct(): ActiveQuery
    {
        return $this->hasOne(Products::class, ['id' => 'productId']);
    }

    public function getShop(): ActiveQuery
    {
        return $this->hasOne(Shops::class, ['id' => 'shopId']);
    }

    public function getAddresses(): ActiveQuery
    {
        return $this->hasMany(Addresses::class, ['shopId' => 'id'])
            ->via('shop');
    }
}
