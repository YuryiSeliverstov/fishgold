<?php


namespace app\models;

use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributeBehavior;

/**
 * @property float|int $expiredAt
 * @property false|string $createdAt
 * @property false|string $updatedAt
 * @property mixed|null $token
 *
 *
 * @SWG\Definition()
 * @SWG\Property(property="userId", type="integer")
 * @SWG\Property(property="token", type="string")
 * @SWG\Property(property="expiredAt", type="string")
 * @SWG\Property(property="createdAt", type="string")
 * @SWG\Property(property="updatedAt", type="string")
 */
class UsersTokens extends ActiveRecord{
	
	public static function tableName(): string
	{
		return 'users_tokens';
	}
	
	public function rules(): array
	{
		return [
			[['userId','createdAt','expiredAt','updatedAt'], 'integer'],
			[['token'], "string",'max'=>255],
			[['token'], "unique"],
			[['token','userId'], 'required'],
			
			[['userId'],
				'exist',
				'skipOnError'     => false,
				'targetClass'     => Users::className(),
				'targetAttribute' => ['userId' => 'id']
			],
		];
		
	}
	
	public function attributeLabels(): array
	{
		return [
			'id' 		=> "ID",
			'userId' 	=> "ID пользователя",
			'token' 	=> "Токен",
			'expiredAt' => "Время истечения токена",
			'createdAt' => "Дата создания",
			'updatedAt' => "Дата обновления",
		];
	}

	public function beforeValidate()
	{
		$this->token=hash('SHA256',strval($this->userId.time().microtime()));
		return parent::beforeValidate();
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
            ],
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['expiredAt'],
                    BaseActiveRecord::EVENT_AFTER_UPDATE => ['expiredAt'],
                ],
                'value' => time() + (86400 * 30),
            ],
			/*
			[
                'class' => AttributeBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['token'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['token'],
                ],
                'value' => hash('SHA256',strval($this->userId.time().microtime())),
            ],
			*/
        ];
    }
	
}