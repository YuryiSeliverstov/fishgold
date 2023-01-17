<?php

namespace app\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $email;
    public $password;
	public $fcmToken;
	
    public function rules()
    {
        return [
            [['email', 'password','fcmToken'], 'required'],
			[['email'], 'email']
        ];
    }
}
