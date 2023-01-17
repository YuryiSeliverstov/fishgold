<?php

namespace app\models;

use Exception;
use Swagger\Annotations as SWG;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\db\BaseActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * @SWG\Definition()
 * @SWG\Property(property="id", type="string")
 * @SWG\Property(property="name", type="string")
 * @SWG\Property(property="description", type="string")
 * @SWG\Property(property="weightNetto", type="integer")
 * @SWG\Property(property="weightBrutto", type="integer")
 */
class Products extends ActiveRecord
{	
    public static function tableName(): string
    {
        return 'products';
    }

    public function rules(): array
    {
        return [
            [['description'], 'safe'],
            [
                [
                    'weightNetto',
                    'weightBrutto',
					'packQuantity',
                ],
                'default',
                'value' => 0
            ],
            ['deleted', 'default', 'value' => 0],
            [
                [
                    'weightNetto',
					'packQuantity',
                    'weightBrutto',
                    'deleted',
                ],
                'integer'
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' 			=> 	'id',
            'name' 			=> 	'Наименование',
            'description' 	=> 	'Описание',
            'weightNetto' 	=> 	'Вес нетто',
			'weightBrutto'	=>	'Вес брутто',
			'packQuantity'	=>	'кол-во в упаковке',
        ];
    }
	
	public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['createdAt', 'updatedAt'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['updatedAt'],
                ],
            ]
        ];
    }
	
	public static function find() 
	{
       return parent::find()->onCondition([
			'and',
			['=',static::tableName().'.deleted',0]
        ]);
    }
}
