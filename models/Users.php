<?php
namespace app\models;

use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;
use Yii;


use app\models\UsersTokens;

/**
 * @SWG\Definition(required={"fullName"})
 *
 * @SWG\Property(property="id", type="integer")
 * @SWG\Property(property="firstName", type="string")
 * @SWG\Property(property="lastName", type="string")
 * @SWG\Property(property="secondName", type="string")
 * @SWG\Property(property="email", type="string")
 * @SWG\Property(property="dateOfBirth", type="string")
 */
class Users extends ActiveRecord implements IdentityInterface
{	
	
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
	
    public static function tableName(): string
    {
        return 'users';
    }
	
	public function rules(): array
    {
        return [
            [['firstName','lastName','secondName', 'password','emailCode','nickName','gender'], 'string','max'=>255],
			[['createdAt','updatedAt','loggedAt','active','deleted'], 'integer'],
			[['active','deleted'], 'default','value'=>0],

			[['email'], 'email'],
			[['email'], 'unique'],
	        [['dateOfBirth'], 'safe']
        ];
    }

    public function attributeLabels(): array
    {
        return [
	        'id' 			=> 	"ID пользователя",
			'firstName'		=>	'Имя',
			'lastName'		=>	'Фамилия',
			'secondName'	=>	'Отчество',
			'dateOfBirth'	=>	'Дата рождения',
	        'password' 		=> 	'Пароль',
	        'email' 		=> 	'E-mail',
	        'createdAt'		=>	'Дата создания',
	        'updatedAt'		=>	'Дата последнего изменения',
	        'loggedAt'		=>	'Дата последней авторизации',
		];
    }
	
	public function validatePassword($password): bool
	{
        return $this->password === self::hashPassword($password);
    }
	
	public function setPassword($password)
	{
		$this->password	=	self::hashPassword($password);
	}
	
	public static function hashPassword($password): string
	{
		return sha1("ASDQW#1231aDF#$$@Q#" . md5("AS(D*&ASD(F&*S(D*(CXSazxz$@#@$" . $password));
	}
	
	public function generateEmailCode()
	{
		$this->emailCode	=	strval(rand(1000,9999));
	}

    public static function findIdentity($id) 
	{
        return self::findOne($id);
    }
	
    public static function findIdentityByAccessToken($token,$type=null) 
	{
	   return self::find()
		    ->leftJoin(UsersTokens::tableName().' as ut',self::tableName().'.id=ut.userId')
		    ->where(['ut.token'=>$token])
			->andWhere(['>=','ut.expiredAt',time()])
		    ->one();
	}

    public function getId() 
	{
        return $this->id;
    }

    public function getAuthKey() 
	{

    }

    public function validateAuthKey($authKey) 
	{

    }
	
	public function beforeValidate(): bool
	{
        if ($this->isNewRecord)
        {
			$this->createdAt = time();
        }
		
		$this->updatedAt = time();
        return parent::beforeValidate();
    }
	
	public function getToken(): ActiveQuery
	{
		return $this->hasOne(UsersTokens::className(), ["userId" => "id"]);
	}
	
	public static function dateFormat(int $time)
	{
		return date('d.m.Y H:i:s',$time);
	}

	public function fields(): array
	{
		$fields = parent::fields();
		
		$fields["createdAt"] 	= 	function()
		{ 
			return self::dateFormat($this->createdAt);
		};
		
		$fields["updatedAt"] 	= 	function()
		{ 
			return self::dateFormat($this->updatedAt);
		};
		
		$fields["loggedAt"] 	= 	function()
		{ 
			return self::dateFormat($this->loggedAt);
		};
		
		unset($fields['password']);
		unset($fields['emailCode']);
		unset($fields['fcmToken']);
		unset($fields['active']);
		unset($fields['deleted']);
		return $fields;
	}	
}
