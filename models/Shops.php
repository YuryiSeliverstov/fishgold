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
 * @SWG\Property(property="createdAt", type="integer")
 * @SWG\Property(property="updatedAt", type="integer")
 */
class Shops extends ActiveRecord
{	
    public static function tableName(): string
    {
        return 'shops';
    }

    public function rules(): array
    {
        return [
			[['name'], 'string','max'=>255],
            [['description'], 'safe'],
            [
                [
                    'createdAt',
					'updatedAt'
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
			'createdAt'		=>	'Дата создания',
			'updatedAt'		=>	'Дата изменения'
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
}
